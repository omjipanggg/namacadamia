<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementType;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockTransferResource;

use App\Models\Stock;
use App\Models\StockLocation;

use App\Services\Inventory\StockService;
use App\Services\Inventory\StockTransferService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockController extends BaseController
{
    public function __construct(protected StockService $stockService, protected StockTransferService $stockTransferService) {}    

    public function index(Request $request)
    {
        $stocks_by_branches = Stock::select(
            'stock_location_id',
            DB::raw('SUM(quantity) as value')
        )->with(['location:id,name,code'])
            ->groupBy('stock_location_id')
            ->get()
        ->map(function ($item) {
            return [
                'key' => $item->stock_location_id ?? 0,
                'code' => $item->location->code ?? '000000',
                'title' => $item->location->name ?? 'N/A',
                'value' => (int) $item->value ?? 0,
            ];
        });

        $stocks = $this->stockService->paginate($request);

        return StockResource::collection($stocks)->additional([
            'stocks_by_branches' => $stocks_by_branches,
        ]);
    }
    
    public function adjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stock_location_id' => ['required', 'exists:stock_locations,id'],
            'product_sku_id' => ['required', 'exists:product_skus,id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'notes' => ['nullable', 'string'],
        ]);        
        $stock = $this->stockService->adjust(
            productSkuId: $validated['product_sku_id'],
            stockLocationId: $validated['stock_location_id'],
            quantity: $validated['quantity'],
            notes: $validated['notes'] ?? null,
        );

        return $this->sendResponse(true, 'Stock adjusted successfully', $stock);
    }
        
    public function setQuantity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stock_location_id' => ['required', 'exists:stock_locations,id'],
            'product_sku_id' => ['required', 'exists:product_skus,id'],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'type' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $stock = $this->stockService->adjust(
            productSkuId: $validated['product_sku_id'],
            stockLocationId: $validated['stock_location_id'],
            quantity: $validated['quantity'],
            type: $validated['type'],
            notes: $validated['notes'],
        );

        return $this->sendResponse(true, 'Stock audited successfully', $stock);
    }

    public function show(Stock $stock): StockResource
    {
        return StockResource::make($stock);
    }

    public function transfer(StoreStockTransferRequest $request): StockTransferResource
    {
        $validated = $request->validated();

        $transfer = $this->stockTransferService
            ->transfer(
                productSkuId: $validated['product_sku_id'],
                source: StockLocation::findOrFail($validated['source_location_id']),
                target: StockLocation::findOrFail($validated['target_location_id']),
                quantity: $validated['quantity'],
                reference: $validated['reference'],
                notes: $validated['notes'],
            );

        return StockTransferResource::make($transfer);
    }

    public function destroy(Stock $stock): JsonResource
    {
        return $this->sendResponse(true, 'Stock has been deleted', $stock);
    }
}
