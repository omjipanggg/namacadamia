<?php

namespace App\Http\Controllers;

use App\Services\Report\SalesReportService;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected SalesReportService $salesReportService) {}    
    
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'sales_channel_id' => ['nullable', 'exists:sales_channels,id'],
            'group_by' => ['nullable', 'in:day,week,month,year'],
        ]);

        return $this->salesReportService->generate($validated);
    }
}
