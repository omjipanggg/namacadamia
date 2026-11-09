<?php

namespace App\Services\Report;

use App\Enums\PurchaseStatus;
use App\Enums\SaleStatus;
use App\Models\Sale;

use App\Http\Resources\TopProductResource;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    public function generate(array $filters): array
    {
        $dateFrom = isset($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : now()->startOfMonth();

        $dateTo = isset($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : now()->endOfMonth();

        return [
            'summary' => $this->summary(
                $filters,
                $dateFrom,
                $dateTo,
            ),

            'sales_trend' => $this->salesTrend(
                $filters,
                $dateFrom,
                $dateTo,
            ),

            'sales_and_purchases_trends' => $this->salesAndPurchasesTrends(
                $filters,
                $dateFrom,
                $dateTo,
            ),

            'sales_by_branch' => $this->salesByBranch(
                $filters,
                $dateFrom,
                $dateTo,
            ),

            'sales_by_channel' => $this->salesByChannel(
                $filters,
                $dateFrom,
                $dateTo,
            ),

            'top_products' => $this->topProducts($filters, $dateFrom, $dateTo),
            
            'meta' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'branch_id' => $filters['branch_id'] ?? null,
                'sales_channel_id' => $filters['sales_channel_id'] ?? null,
                'group_by' => $filters['group_by'] ?? 'day',
            ],
        ];
    }

    private function baseQuery(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): Builder {
        return Sale::query()
            ->whereBetween('sales.created_at', [
                $dateFrom,
                $dateTo,
            ])
            ->whereIn('status', [
                SaleStatus::PAID,
                SaleStatus::COMPLETED,
            ])
            ->when(
                !empty($filters['branch_id']),
                fn ($query) => $query->where(
                    'branch_id',
                    $filters['branch_id']
                )
            )
            ->when(
                !empty($filters['sales_channel_id']),
                fn ($query) => $query->where(
                    'sales_channel_id',
                    $filters['sales_channel_id']
                )
            );
    }

    private function summary(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        $base = $this->baseQuery($filters, $dateFrom, $dateTo);
        
        $summary = (clone $base)->selectRaw('
            COUNT(*) as orders_count,
            COALESCE(SUM(subtotal), 0) as gross_sales,
            COALESCE(SUM(tax), 0) as tax,
            COALESCE(SUM(grand_total), 0) as net_sales')->first();

        $sumDiscountedItems = (clone $base)->join(
            'sale_items', 'sale_items.sale_id', '=', 'sales.id'
        )->sum('sale_items.discount');

        $ordersCount = (int) $summary->orders_count;
        $grossSales = (float) $summary->gross_sales;
        $discounts = (float) $sumDiscountedItems;
        $tax = (float) $summary->tax;
        $netSales = (float) $summary->net_sales;

        return [
            'gross_sales' => $grossSales,
            'discounts' => $discounts,
            'tax' => $tax,
            'net_sales' => $grossSales - $discounts,
            'orders_count' => $ordersCount,
            'average_order' => $ordersCount > 0 ? $netSales / $ordersCount : 0,
        ];
    }

    private function salesTrend(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        $groupBy = $filters['group_by'] ?? 'day';

        // Build the SQL expression so every group produces a full YYYY-MM-DD date
        $dateExpr = match ($groupBy) {
            'day'   => "DATE_FORMAT(created_at, '%Y-%m-%d')",
            'week'  => "DATE_FORMAT(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), '%Y-%m-%d')",
            'month' => "DATE_FORMAT(created_at, '%Y-%m-01')",
            'year'  => "DATE_FORMAT(created_at, '%Y-01-01')",
        };

        return $this->baseQuery($filters, $dateFrom, $dateTo)
            ->selectRaw("
                {$dateExpr} as period,
                COUNT(*) as orders_count,
                COALESCE(SUM(subtotal), 0) as gross_sales,
                COALESCE(SUM(discount), 0) as discounts,
                COALESCE(SUM(tax), 0) as tax,
                COALESCE(SUM(grand_total), 0) as net_sales
            ")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn ($sale) => [
                'period' => $sale->period,
                'orders_count' => (int) $sale->orders_count,
                'gross_sales' => (float) $sale->gross_sales,
                'discounts' => (float) $sale->discounts,
                'tax' => (float) $sale->tax,
                'net_sales' => (float) $sale->net_sales,
            ])
            ->values()
            ->toArray();
    }

    private function salesAndPurchasesTrends(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        $groupBy = $filters['group_by'] ?? 'day';

        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%x-W%v',
            'month' => '%Y-%m',
            'year' => '%Y',
        };

        $sales = $this->baseQuery($filters, $dateFrom, $dateTo)
            ->selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                COUNT(DISTINCT id) as orders_count,
                COALESCE(SUM(grand_total), 0) as net_sales
            ")
            ->groupBy('period')
            ->get() 
            ->keyBy('period');

        $purchases = $this->purchaseQuery($filters, $dateFrom, $dateTo)
            ->select(
                DB::raw("DATE_FORMAT(purchases.created_at, '{$dateFormat}') as period"),
                DB::raw("COUNT(DISTINCT purchases.id) as purchases_count"),
                DB::raw("COALESCE(SUM(purchase_items.subtotal), 0) as total_purchases")
            )
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $allPeriods = $sales->keys()->merge($purchases->keys())->unique()->sort();

        return $allPeriods->map(function ($period) use ($sales, $purchases) {
            $sale = $sales->get($period);
            $purchase = $purchases->get($period);

            return [
                'period' => $period,
                'orders_count' => (int) ($sale->orders_count ?? 0),
                'net_sales' => (float) ($sale->net_sales ?? 0),
                'purchases_count' => (int) ($purchase->purchases_count ?? 0),
                'total_purchases' => (float) ($purchase->total_purchases ?? 0),
            ];
        })->values()->toArray();
    }

    private function purchaseQuery(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): Builder {
        return \App\Models\Purchase::query()
            ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereBetween('purchases.created_at', [
                $dateFrom,
                $dateTo,
            ])
            ->whereIn('purchases.status', [
                \App\Enums\PurchaseStatus::ORDERED,
                \App\Enums\PurchaseStatus::COMPLETED,
            ])
            ->when(
                !empty($filters['stock_location_id']),
                fn ($query) => $query->where(
                    'purchases.stock_location_id',
                    $filters['stock_location_id']
                )
            )
            ->when(
                !empty($filters['supplier_id']),
                fn ($query) => $query->where(
                    'purchases.supplier_id',
                    $filters['supplier_id']
                )
            );   
    }

    private function salesByBranch(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        return $this
            ->baseQuery($filters, $dateFrom, $dateTo)
            ->join(
                'branches',
                'branches.id',
                '=',
                'sales.branch_id'
            )
            ->selectRaw('
                sales.branch_id,
                branches.name as branch_name,
                branches.code as branch_code,
                COUNT(*) as orders_count,
                COALESCE(SUM(sales.subtotal), 0) as gross_sales,
                COALESCE(SUM(sales.discount), 0) as discounts,
                COALESCE(SUM(sales.tax), 0) as tax,
                COALESCE(SUM(sales.grand_total), 0) as net_sales
            ')
            ->groupBy(
                'sales.branch_id',
                'branches.name',
                'branches.code',
            )
            ->orderBy('branches.name')
            ->get()
            ->map(fn ($branch) => [
                'branch_id' => $branch->branch_id,
                'branch_code' => $branch->branch_code,
                'branch_name' => $branch->branch_name,
                'orders_count' => (int) $branch->orders_count,
                'gross_sales' => (float) $branch->gross_sales,
                'discounts' => (float) $branch->discounts,
                'tax' => (float) $branch->tax,
                'net_sales' => (float) $branch->net_sales,
            ])
            ->values()
            ->toArray();
    }

    private function salesByChannel(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        return $this
            ->baseQuery($filters, $dateFrom, $dateTo)
            ->join(
                'sales_channels',
                'sales_channels.id',
                '=',
                'sales.sales_channel_id'
            )
            ->selectRaw('
                sales.sales_channel_id,
                sales_channels.name as channel_name,
                COUNT(*) as orders_count,
                COALESCE(SUM(sales.subtotal), 0) as gross_sales,
                COALESCE(SUM(sales.discount), 0) as discounts,
                COALESCE(SUM(sales.tax), 0) as tax,
                COALESCE(SUM(sales.grand_total), 0) as net_sales
            ')
            ->groupBy(
                'sales.sales_channel_id',
                'sales_channels.name'
            )
            ->orderByDesc('net_sales')
            ->get()
            ->map(fn ($channel) => [
                'sales_channel_id' => $channel->sales_channel_id,
                'channel_name' => $channel->channel_name,
                'orders_count' => (int) $channel->orders_count,
                'gross_sales' => (float) $channel->gross_sales,
                'discounts' => (float) $channel->discounts,
                'tax' => (float) $channel->tax,
                'net_sales' => (float) $channel->net_sales,
            ])
            ->values()
            ->toArray();
    }
    
    private function topProducts(
        array $filters,
        Carbon $dateFrom,
        Carbon $dateTo
    ) {
        $limit = $filters['per_page'] ?? 10;

        $products = $this->baseQuery($filters, $dateFrom, $dateTo)
            ->join('sale_items', 'sale_items.sale_id', '=', 'sales.id')
            ->join('product_skus', 'product_skus.id', '=', 'sale_items.product_sku_id')
            ->join('products', 'products.id', '=', 'product_skus.product_id')
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
                'products.name'
            )
            ->orderByDesc('quantity_sold')
            ->paginate($limit);

        $skus = \App\Models\ProductSku::with('variants')
            ->whereIn('id', $products->pluck('product_sku_id'))
            ->get()
            ->keyBy('id');

        $products->getCollection()->transform(function ($product) use ($skus) {
            $product->sku = $skus->get($product->product_sku_id);
            return $product;
        });

        return [
            "data" => TopProductResource::collection($products),
            "links" => [],
            "meta" => [],
        ];
    }
}