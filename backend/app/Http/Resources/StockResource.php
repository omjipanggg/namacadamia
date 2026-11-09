<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'quantity' => $this->quantity,
            'quantity_reserved' => $this->quantity_reserved,
            'quantity_available' => $this->quantity - $this->quantity_reserved,
            'quantity_minimum' => $this->quantity_minimum,
            'quantity_maximum' => $this->quantity_maximum,
            'sku' => ProductSkuResource::make($this->whenLoaded('sku')),
            'location' => StockLocationResource::make($this->whenLoaded('location')),
        ];
    }
}
