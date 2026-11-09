<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Services\Dashboard\DashboardService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseController
{
    public function __construct(protected DashboardService $dashboardService) {}
    
    public function index()
    {
        return $this->sendResponse(true, 'Stats retrieved', $this->dashboardService->getStats());
    }

    public function lookups(Request $request): JsonResponse
    {
        $includes = collect(
            explode(',', $request->input('include'))
        )->filter()->unique()->values();

        return response()->json($this->dashboardService->load($includes));
    }
}
