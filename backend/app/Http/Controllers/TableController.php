<?php

namespace App\Http\Controllers;

use App\Http\Resources\TableResource;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableController extends Controller
{
    private function tableNames() {
        return Schema::getTableListing(
            schema: DB::connection()->getDatabaseName(),
            schemaQualified: false
        );
    }
    
    private function getColumns($table, $object = false) {
        if ($object) {
        return array_map(function ($column) use ($table) {
            return [
                'key'   => $column['name'],
                'label' => $column['name'],
                'type' => $column['type_name'],
                'nullable' => $column['nullable'],
            ];
        }, Schema::getColumns($table));
        } else {
            return Schema::getColumnListing($table);
        }
    }

    public function index() {
        return $this->tableNames();
    }

    public function show(Request $request, string $table) 
    {
        if (!in_array($table, $this->tableNames())) {
            return response()->json([
                'message' => "Table '{$table}' not found",
            ], 404);
        }

        $columns = $this->getColumns($table);
        $query = DB::table($table);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if ($sortBy = $request->input('sort_by')) {
            if (in_array($sortBy, $columns)) {
                $direction = strtolower($request->input('sort_direction')) === 'desc' ? 'desc' : 'asc';
                $query->orderBy($sortBy, $direction);
            }
        }

        if ($request->input('lookup')) {
            $lookupColumns = array_intersect(['id', 'name'], $columns);
            
            if (count($lookupColumns) === 2) {
                return response()->json($query->select('id', 'name')->get());
            }
            
            return response()->json(['data' => $query->select(array_slice($columns, 0, 2))->get()]);
        }

        $result = $query->paginate($request->integer('per_page', 12));

        return TableResource::collection($result)->additional([
            'columns' => $this->getColumns($table, true),
        ]);
    }

    public function store(Request $request, string $table)
    {
        if (! Schema::hasTable($table)) {
            abort(404, "Table '{$table}' does not exist.");
        }
    
        $validColumns = $this->getColumns($table);
        $allowedColumns = array_diff($validColumns,['id', 'created_at', 'updated_at', 'deleted_at']);

        // $filteredColumns = $request->only(array_diff($validColumns, ['id', 'created_at', 'updated_at', 'deleted_at']));
        $filteredColumns = array_filter(
            $request->only($allowedColumns),
            fn ($value) => $value !== null,
        );

        $now = now();

        if (in_array('created_at', $validColumns)) {
            $filteredColumns['created_at'] = $now;
        }

        if (in_array('updated_at', $validColumns)) {
            $filteredColumns['updated_at'] = $now;
        }

        $insertedId = DB::table($table)->insertGetId($filteredColumns);
        $insertedRecord = DB::table($table)->find($insertedId);

        return TableResource::make($insertedRecord)->additional([
            'columns' => $this->getColumns($table, true),
        ]);
    }

    public function destroy (Request $request, string $table)
    {
        $ids = array_map(function ($item) {
            return is_array($item) ? ($item['id'] ?? $item['key'] ?? null) : $item;
        }, $request->data);

        $ids = array_filter($ids);

        if (empty($ids)) {
            return response()->json(['message' => 'No valid IDs provided'], 422);
        }

        $hasSoftDeletes = in_array('deleted_at', $this->getColumns($table));
        $count = 0;

        DB::transaction(function () use ($table, $ids, $hasSoftDeletes, &$count) {
            $query = DB::table($table)->whereIn('id', $ids);
            
            if ($hasSoftDeletes) {
                $count = $query->update(['deleted_at' => now()]);
            } else {
                $count = $query->delete();
            }
        });

        return response()->json([
            'message' => "Successfully deleted {$count} record(s)",
            'deleted_items_count' => $count,
            'columns' => $this->getColumns($table, true),
        ], 200);
    }
}
