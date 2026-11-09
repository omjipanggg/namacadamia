<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'branch' => $this->whenLoaded('branch', function () {
                return $this->branch ? BranchResource::make($this->branch) : null;
            }),
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            'stocks_count' => $this->whenCounted('stocks'),
            'stocks_sum_quantity' => $this->whenHas('stocks_sum_quantity'),
            'purchases' => PurchaseResource::collection($this->whenLoaded('purchases')),
            'purchases_count' => $this->whenCounted('purchases'),
            'branch_users_count' => $this->when(
                $this->branch_users_count !== null, $this->branch_users_count
            ),
            'branch_sales_count' => $this->when(
                $this->branch_sales_count !== null, $this->branch_sales_count
            ),
        ];
    }
}
