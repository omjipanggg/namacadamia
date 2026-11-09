<?php
namespace App\Services\Dashboard;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class TableService {
    public function __construct() {

    }

    private function tableNames() {
        return Schema::getTableListing(
            schema: DB::connection()->getDatabaseName(),
            schemaQualified: false
        );
    }
    
    private function getColumns($table) {
        return array_map(function ($column) {
            return [
                'key'   => $column,
                'label' => $column,
            ];
        }, Schema::getColumnListing($table));
    }
    

    public function paginate($request, string|object $model): LengthAwarePaginator
    {
        $query = DB::table('users');
        return $query->paginate(12)->onEachSide(1);
    }

    public function paginateBackup($request, string|object $model, array $columns = [], array $relations = []): LengthAwarePaginator
    {
        $query = is_string($model) ? $model::query() : $model->newQuery();
        $table = $query->getModel()->getTable();

        if (!$empty($relations)) {
            $query->with($relations);
        }

        if ($search = $request->search) {
            $query->where(function (Builder $q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $key = $column['key'] ?? null;

                    if (!$key) continue;

                    if (str_contains($key, '.')) {
                        [$relation, $relationColumn] = explode('.', $key, 2);
                        $q->orWhereHas($relation, function(Builder $relQuery) use ($relationColumn, $search) {
                            $relQuery->where($relationColumn, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($key, 'like', "%{$search}%");
                    }
                }
            });
        }

        if ($categories = $request->category) {
            $query->whereHas('categories', function(Builder $builder) use ($categories) {
                $builder->whereIn('name', $categories);
            });
        }

        if ($sortBy = $request->sort_by) {
            $sortDirection = $request->sort_direction ?? 'asc';

            if (str_contains($sortBy, '.')) {
                [$relation, $relationColumn] = explode('.', $sortBy, 2);
                $relatedModel = ((new $query)->getModel()->$relation())->getModel();
                $query->orderBy(
                    $relatedModel::select($relationColumn)
                        ->whereColumn($relatedModel->getTable() . '.' . ($query)->getModel()->$relation()->getForeignKeyName(),
                        ($query)->getModel()->getTable() . '.' . $query->getModel()->getKeyName()
                    ),
                    $sortDirection
                );
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        return $query->paginate($request->integer('per_page', 12))->onEachSide(1);
    }
}