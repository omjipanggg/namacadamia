<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseController extends Controller
{
    public function sendResponse($status = true, $message = "Message", $resource = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'resource'    => $resource,
            'success' => $status,
        ]);
    }

    public function sendError($message = "Message", $errorMessages = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];  

        if (!empty($errorMessages)) {
            $response['resource'] = $errorMessages;
        }

        return response()->json($response);
    }

    public function paginate(Request $request, Builder $query, ?array $columns = [], ?string $initialSort = 'id'): LengthAwarePaginator
    {        
        if ($search = $request->input('search')) {
            $escapedSearch = addcslashes($search, '%_');

            $query->where(function (Builder $subQuery) use ($columns, $escapedSearch) {
                    foreach ($columns as $column) {
                        $subQuery->orWhere($column, 'LIKE', "%{$escapedSearch}%");
                    }
                }
            );
        }

        $sortBy = $request->input('sort_by', $initialSort);
        $sortDirection = $request->input('sort_direction', 'desc');

        if (in_array($sortBy, $columns, true)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $perPage = (int) $request->input('per_page', 12);
        
        return $query->paginate($perPage)->onEachSide(1);
    }
}