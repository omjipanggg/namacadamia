<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Stock;
use App\Models\StockLocation;

use App\Http\Resources\BranchResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\StockLocationResource;

use Illuminate\Http\Request;

class BranchController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = StockLocation::query()
            ->with(['branch' => function ($query) {
                $query->withCount(['sales', 'users']);
            }])
            ->withSum('stocks', 'quantity')
            ->withCount(['purchases', 'stocks']);

        $result = $this->paginate($request, $locations, ['code', 'name']);
        return StockLocationResource::collection($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load([
            'purchases',
            'sales',
            'stocks.sku',
        ]);
        return BranchResource::make($branch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        //
    }

    public function showStock(Request $request, int $id)
    {
        $stocks = Stock::with(['sku.product:id,name', 'sku.variants', 'location:id,name'])->where('stock_location_id', $id);
            // ->where(function ($query) {
                // $query->where('quantity', '>', 0)->orWhere('quantity_reserved', '>', 0);
            // });

        if ($search = $request->search ?? null) {
            $stocks->where(function ($query) use ($search) {
                $query->whereHas('sku', function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->sort_by) {
            $stocks->orderBy($request->sort_by, $request->sort_direction ?? 'asc');
        } else {
            $stocks->latest();
        }

        return StockResource::collection($stocks->paginate($request->per_page ?? 12)->onEachSide(1));
    }
}
