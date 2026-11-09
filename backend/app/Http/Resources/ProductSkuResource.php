<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuResource extends JsonResource
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
            'variants' => ProductAttributeValueResource::collection($this->whenLoaded('variants')),
            'product' => ProductIndexResource::make($this->whenLoaded('product')),
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            'barcode' => $this->barcode,
            'selling_price' => $this->selling_price,
            'formatted_selling_price' => $this->formatted_selling_price,
            'weight' => $this->weight,
            'description' => $this->description,
        ];
    }
}
