<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaySaleRequest;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;

use App\Http\Resources\SaleResource;

use App\Filters\SaleFilter;

use App\Models\Sale;

use App\Services\Sale\SaleService;

use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService, protected SaleFilter $saleFilter) {}
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::query()->with([
            'branch',
            'customer',
            'salesChannel',
        ]);
        
        $sales = $this->saleFilter->apply($query, $request->all())->paginate($request->integer('per_page', 12));
        return SaleResource::collection($sales); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request): SaleResource
    {
        $sale = $this->saleService->create($request->validated());
        return SaleResource::make($sale);
    }

    public function pay(PaySaleRequest $request, Sale $sale): SaleResource
    {
        $paidSale = $this->saleService->pay($sale, $request->validated());
        return SaleResource::make($paidSale->sale)->additional([
            'transaction' => [
                'received_amount' => $paidSale->receivedAmount,
                'payment_amount' => $paidSale->paymentAmount,
                'change_amount' => $paidSale->changeAmount,
            ],
        ]);
    }

    public function complete(Sale $sale): SaleResource
    {
        $completedSale = $this->saleService->complete($sale);
        return SaleResource::make($completedSale);
    }
    
    public function cancel(Sale $sale): SaleResource
    {
        $cancelledSale = $this->saleService->cancel($sale);
        return SaleResource::make($cancelledSale);
    }
    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): SaleResource
    {
        $sale->load([
            'branch',
            'customer',
            'salesChannel',
            'items.sku.variants',
            'items.unit',
            'payments.paymentMethod',
            'discounts',
            'creator',
        ]);

        return SaleResource::make($sale);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        return response()->json($sale);
    }
}
