<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
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
            'product_name' => $this->sku->product->name,
            'sku' => ProductSkuResource::make($this->whenLoaded('sku')),
            'measure_unit' => MeasureUnitResource::make($this->whenLoaded('unit')),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'notes' => $this->notes,
        ];
    }
}
