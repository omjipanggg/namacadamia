<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'is_active' => $this->is_active,
            'stock_locations' => $this->whenLoaded('locations', function () {
                return StockLocation::collection($this->locations);
            }),
            'description' => $this->description,
            'sales' => SaleResource::collection($this->whenLoaded('sales')),
            'sales_count' => $this->whenCounted('sales'),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'users_count' => $this->whenCounted('users'),
        ];
    }
}
