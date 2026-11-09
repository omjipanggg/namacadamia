<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseStatus;
use App\Http\Resources\PurchaseResource;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;

use App\Models\Purchase;
use App\Services\Purchase\PurchaseService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $purchases = $this->purchaseService->paginate($request);
        return PurchaseResource::collection($purchases);
    }

    public function store(StorePurchaseRequest $request): PurchaseResource
    {
        $purchase = $this->purchaseService->create($request->validated());

        return PurchaseResource::make(
            $purchase->fresh([
                'supplier',
                'location',
                'items.sku',
                'items.measureUnit',
            ])
        );
    }

    public function receive(Request $request, Purchase $purchase)
    {
        $data = $this->purchaseService->receive($purchase);
        return PurchaseResource::make($data->fresh()->load([
            'location',
            'supplier',
            'items.sku',
            'items.measureUnit',
        ]));
    }

    public function show(Purchase $purchase)
    {
        return PurchaseResource::make($purchase->load([
            'location',
            'supplier',
            'items.sku',
            'items.measureUnit',
        ]));
    }

    public function update(UpdatePurchaseRequest $request)
    {
        //
    }

    public function destroy(Purchase $purchase)
    {
        //
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codes' => 'required',
            'codes.*' => 'exists:purchases,code',
        ]);

        $count = 0;
        foreach ($request->input('codes') as $code) {
            Purchase::where('code', $code)->delete();
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Removed {$count} item(s)",
        ]);
    }
}
