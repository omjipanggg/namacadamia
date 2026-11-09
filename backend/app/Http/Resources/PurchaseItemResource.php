<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
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
            'received_quantity' => $this->received_quantity,
            'cost_price' => $this->cost_price,
            'subtotal' => $this->subtotal,
            'sku' => ProductSkuResource::make($this->whenLoaded('sku')),
            'measure_unit' => MeasureUnitResource::make($this->whenLoaded('measureUnit')),
            'notes' => $this->notes,
        ];
    }
}
