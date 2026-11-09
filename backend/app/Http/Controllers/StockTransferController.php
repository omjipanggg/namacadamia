<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Resources\StockTransferResource;

use App\Models\StockLocation;
use App\Models\StockTransfer;

use App\Services\Inventory\StockTransferService;

class StockTransferController extends Controller
{
    public function __construct(protected StockTransferService $service) {}

    public function index(Request $request)
    {
        $transfers = StockTransfer::query()
            ->with([
                'sourceLocation',
                'targetLocation',
            ])
            ->latest()
            ->paginate(
                $request->integer('per_page', 12)
            );

        return StockTransferResource::collection($transfers);
    }

    public function store(StoreStockTransferRequest $request): StockTransferResource
    {
        $validated = $request->validated();

        $transfer = $this->service->create(
            source: StockLocation::findOrFail($validated['source_location_id']),
            target: StockLocation::findOrFail($validated['target_location_id']),
            items: $validated['items'],
            notes: $validated['notes'],
            auto: $validated['auto'],
        );

        return StockTransferResource::make($transfer);
    }
    
    public function show(StockTransfer $stockTransfer): StockTransferResource
    {
        $stockTransfer->load([
            'sourceLocation',
            'targetLocation',
            'items.sku.product',
        ]);

        return StockTransferResource::make($stockTransfer);
    }

    public function complete(StockTransfer $stockTransfer): StockTransferResource
    {
        $transfer = $this->stockTransferService->complete($stockTransfer);
        return StockTransferResource::make($transfer);
    }
}
