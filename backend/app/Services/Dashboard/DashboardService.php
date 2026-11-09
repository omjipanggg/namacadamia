<?php
namespace App\Services\Dashboard;

use App\Http\Resources\LookupResource;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\MeasureUnit;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\SalesChannel;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockLocation;
use App\Models\User;
use App\Models\Role;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class DashboardService {
    protected array $registry;

    public function __construct()
    {
        $this->register('branches', fn () => LookupResource::collection(
            Branch::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('stock_locations', fn () => LookupResource::collection(
            StockLocation::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('suppliers', fn () => LookupResource::collection(
            Supplier::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('customers', fn () => LookupResource::collection(
            Customer::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('units', fn () => LookupResource::collection(
            MeasureUnit::orderBy('name')->get())
        );
        $this->register('skus', fn () => LookupResource::collection(
            ProductSku::where('is_active', true)->orderBy('code')->get()
                ->sortBy(fn ($sku) => $sku->product?->name))
        );
        $this->register('product_attributes', fn () => LookupResource::collection(
            ProductAttribute::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('product_attribute_values', fn () => LookupResource::collection(
            ProductAttributeValue::orderBy('value')->get())
        );
        $this->register('roles', fn () => LookupResource::collection(Role::orderBy('name')->get()));
        $this->register('product_categories', fn () => LookupResource::collection(
            ProductCategory::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('sales_channels', fn () => LookupResource::collection(
            SalesChannel::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('payment_methods', fn () => LookupResource::collection(
            PaymentMethod::where('is_active', true)->orderBy('name')->get())
        );
        $this->register('stocks', fn () => LookupResource::collection(Stock::all()));
    }

    protected function register(string $key, callable $callback): void
    {
        $this->registry[$key] = $callback;
    }

    public function load(Collection $lookups): array
    {
        $result = [];

        foreach ($lookups as $lookup) {
            if (!isset($this->registry[$lookup])) {
                continue;
            }

            $result[$lookup] = ($this->registry[$lookup])();
        }

        return $result;
    }
    
    public function getStats() {        
        return [
            'revenue' => $this->getRevenue(),
            'sales_count' => Sale::count(),
            'skus_count' => Product::count(),
            'stocks' => $this->getAvailableStock(),
            'users_count' => User::count(),
        ];
    }
    
    private function getAvailableStock()
    {
        return Stock::query()
            ->leftJoin('stock_locations', 'stocks.stock_location_id', '=', 'stock_locations.id')
            ->select([
                'stocks.stock_location_id as key',
                DB::raw("COALESCE(stock_locations.code, '000000') as code"),
                DB::raw("COALESCE(stock_locations.name, 'N/A') as title"),
                DB::raw("COALESCE(SUM(stocks.quantity - stocks.quantity_reserved), 0) as value"),
            ])
            ->groupBy('stocks.stock_location_id', 'stock_locations.code', 'stock_locations.name')
            ->orderByDesc('value')
            ->get()
            ->map(fn ($item) => [
                'key' => $item->key,
                'code' => $item->code,
                'title' => $item->title,
                'value' => (int) $item->value,
            ]);
    }
    
    private function getRevenue(): int {
        $revenue = Sale::query()->selectRaw('
            COUNT(*) as orders_count, COALESCE(SUM(subtotal), 0) as gross_sales,
            COALESCE(SUM(discount), 0) as discounts, COALESCE(SUM(tax), 0) as tax,
            COALESCE(SUM(grand_total), 0) as net_sales')->first()->net_sales;
        // return 'Rp' . $this->formatMKNumber($revenue);
        return (int) $revenue;
    }

    private function formatMKNumber(float|int $num): string 
    {
        if ($num >= 1_000_000) {
            return preg_replace('/\.0$/', '', number_format($num / 1_000_000, 1, '.', '')) . 'M';
        }
     
        if ($num >= 1_000) {
            return preg_replace('/\.0$/', '', number_format($num / 1_000, 1, '.', '')) . 'K';
        }
     
        return (string) $num;
    }
    
    private function cards(): JsonResponse  
    {
        return response()->json([], 200);
    }
    
    private function topProducts(): JsonResponse
    {
        return response()->json([], 200);
    }
    
    private function salesChart(): JsonResponse
    {
        return response()->json([], 200);
    }
    
    private function recentSales(): JsonResponse
    {
        return response()->json([], 200);
    }
    
    private function recentPurchases(): JsonResponse
    {
        return response()->json([], 200);
    }

}