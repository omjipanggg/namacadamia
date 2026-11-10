<?php

    $data = Product::query()
        ->search($request->search)
        ->whereHasCategory($request->category)
        ->sort($request->sort_by, $request->sort_direction)
        ->with(['categories'])
        ->withCount('skus')
        ->paginate($request->integer('per_page', 12))
        ->onEachSide(1);

    $products = ProductResource::collection($data);

    return $this->sendResponse(true, 'Products fetched.', $products);


// TABLE PAGINATION OBJECTS

    return [
        'data' => $this->data,
        'links' => [
            'first' => $this->first_page_url,
            'last' => $this->last_page_url,
            'next' => $this->next_page_url,
            'prev' => $this->prev_page_url,
        ],
        'meta' => [
            'current_page' => $this->current_page,
            'from' => $this->from,
            'last_page' => $this->last_page,
            'links' => $this->links,
            'path' => $this->path,
            'per_page' => $this->per_page,
            'to' => $this->to,
            'total' => $this->total,
        ],
    ];

// TABLES CONTROLLER

    public function show(Request $request, string $table) {
        if (!in_array($table, $this->tableNames())) {
            return $this->sendError('Table not found', $table);
        }

        $query = DB::table($table);
        
        if ($search = $request->search) {
            $query->where(function ($q) use ($search, $table) {
                foreach ($this->getColumns($table) as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if ($sortBy = $request->sort_by) {
            $query->orderBy($sortBy, $request->sort_direction ?? 'asc');
        }

        if ($isLookup) {
            $lookup = $query->select('id', 'name')->get();
            return $lookup->toArray();
        } else {
            $result = $query->paginate($request->integer('per_page', 12));
            $result->getCollection()->transform(fn ($item) => (array) $item);
        }

        return TableResource::collection($result)->additional([
            'columns' => $this->getColumns($table, true),
        ]);
    }

    
    public function __construct()
    {
        $this->register('branches', fn () => LookupResource::collection(
            Branch::orderBy('name')->get())
        );
        $this->register('suppliers', fn () => LookupResource::collection(
            Supplier::orderBy('name')->get())
        );
        $this->register('units', fn () => LookupResource::collection(
            MeasureUnit::orderBy('name')->get())
        );
        $this->register('skus', fn () => 
            ProductSku::select('id', 'product_id', 'code')->with(['product:id,name', 'attributes.productAttribute'])->orderBy('code')->get()
            ->sortBy(fn ($sku) => $sku->product?->name)
            ->values()
            ->map(
                fn ($sku) => [
                    'id' => $sku->id,
                    'code' => $sku->code,
                    'product_name' => $sku->product?->name,
                    'attributes' => $sku->attributes->pluck('value')->toArray(),
                ]
            )
        );
    }


    // sync variant skus
    
    private function syncVariantSkus(Product $product, array $skus): void
    {
        foreach ($skus as $skuData) {
            if (!empty($skuData['id'])) {
                $sku = $product->skus()
                    ->whereKey($skuData['id'])
                    ->firstOrFail();

                $sku->update([
                    'code' => $skuData['code'],
                    'barcode' => $skuData['barcode'] ?? null,
                    'cost_price' => $skuData['cost_price'] ?? 0,
                    'selling_price' => $skuData['selling_price'] ?? $product->price,
                    'description' => $skuData['description'] ?? null,
                ]);
            } else {
                $sku = $product->skus()->create([
                    'code' => $skuData['code'],
                    'barcode' => $skuData['barcode'] ?? null,
                    'cost_price' => $skuData['cost_price'] ?? 0,
                    'selling_price' => $skuData['selling_price'] ?? $product->price,
                    'description' => $skuData['description'] ?? null,
                ]);

                $this->initStock($sku);
            }

            $sku->variants()->sync(
                $skuData['attributes'] ?? []
            );
        }
    }
    
// SaleService


    public function pay(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $sale = $sale::lockForUpdate()->findOrFail($sale->id);

            if (! in_array($sale->status, [SaleStatus::PENDING, SaleStatus::PARTIAL])) {
                throw new CompletedOrExpiredSaleException();
            }

            $paidAmount = $sale->payments()?->sum('amount');
            $remainingAmount = $sale->grand_total - $paidAmount;
            $receivedAmount = $data['amount'];
            $paymentAmount = min($receivedAmount, $remainingAmount);
            $changeAmount = max(0, $receivedAmount - $remainingAmount);

            if ($data['amount'] > $remainingAmount) {
                $data['notes'] = 'Kembalian: ' . $data['amount'] - $remainingAmount;
            }

            $data['amount'] = $paymentAmount;

            $this->createPayment($sale, $data, $paidAmount);

            return $sale->fresh([
                'items',
                'payments.paymentMethod',
                'customer',
                'salesChannel',
                'discounts',
            ]);
        });
    }

// stocks
'stocks' => Stock::select('branch_id', DB::raw('SUM(quantity) as value'))->with(['branch:id,name,code'])->groupBy('branch_id')->get()->map(function ($item) {
    return [
        'key' => $item->branch_id,
        'code' => $item->branch->code ?? '000000',
        'title' => $item->branch->name ?? 'N/A',
        'value' => (int) $item->value,
    ];
}),

// top-products

    private function topProducts(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ) {
        $limit = 10;

        $query = $this
            ->baseQuery($filters, $dateFrom, $dateTo)
            ->join(
                'sale_items',
                'sale_items.sale_id',
                '=',
                'sales.id'
            )
            ->join(
                'product_skus',
                'product_skus.id',
                '=',
                'sale_items.product_sku_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'product_skus.product_id'
            );

        $products = $query
            ->selectRaw('
                sale_items.product_sku_id,
                products.name as name,
                product_skus.code as sku_code,
                COUNT(DISTINCT sales.id) as orders_count,
                SUM(sale_items.quantity) as quantity_sold,
                COALESCE(SUM(sale_items.subtotal), 0) as revenue
            ')
            ->groupBy(
                'sale_items.product_sku_id',
                'product_skus.code',
                'products.name',
            )
            ->orderByDesc('quantity_sold')
            ->limit($limit)
            ->paginate();

        $skus = \App\Models\ProductSku::with('variants')
            ->whereIn('id', $products->pluck('product_sku_id'))
            ->get()
            ->keyBy('id');

        return $products->map(function ($product) use ($skus) {
            $sku = $skus->get($product->product_sku_id);
            return [
                'product_sku_id' => $product->product_sku_id,
                'product_name' => $product->name,
                'sku_code' => $product->sku_code,
                'orders_count' => (int) $product->orders_count,
                'quantity_sold' => (int) $product->quantity_sold,
                'variant_label' => $sku?->variants?->pluck('value')->implode(' • ') ?? 'N/A',
                'revenue' => (float) $product->revenue,
            ];
        })->values()->toArray();

    }
    

// available stock in dashboard preview

    private function getAvailableStock() {
        return Stock::query()
            ->leftJoin('branches', 'stocks.branch_id', '=', 'branches.id')
            ->select([
                'stocks.branch_id as key',
                DB::raw("COALESCE(branches.code, '000000') as code"),
                DB::raw("COALESCE(branches.name, 'N/A') as title"),
                DB::raw("COALESCE(SUM(stocks.quantity - stocks.quantity_reserved), 0) as value"),
            ])
            ->groupBy('stocks.branch_id', 'branches.code', 'branches.name')
            ->orderBy('branches.name', 'asc')
            ->get()
            ->map(fn ($item) => [
                'key' => $item->key,
                'code' => $item->code,
                'title' => $item->title,
                'value' => (int) $item->value,
            ]);
    }