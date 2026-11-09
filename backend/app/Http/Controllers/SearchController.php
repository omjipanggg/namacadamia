<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

use App\Http\Resources\SearchResource;
use App\Services\Search\SearchService;
use App\Services\Search\Data\SearchContext;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class SearchController extends BaseController
{
    public function __construct(protected SearchService $searchService) {}
    
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:191'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $intervalStarts = microtime(true);

        $limit = $request->filled('limit') ? $validated['limit'] : 999;

        $context = new SearchContext(
            query: trim($validated['query']),
            locale: app()->getLocale(),
            user: $request->user(),
            limit: $limit,
        );

        $results = $this->searchService->search($context);

        $duration = round((microtime(true) - $intervalStarts) * 1000, 2);

        return $this->sendResponse(true, "Searching...", [
            'count' => $results->count(),
            'data' => SearchResource::collection($results),
            'duration' => $duration,
            'query' => $context->query,
        ]);
    }

    public function popular(): JsonResponse
    {
        
    }
}
