<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    protected mixed $skus;
    
    public function __construct($resource, $skus = null) {
        parent::__construct($resource);
        $this->skus = $skus;
    }

    public function toArray(Request $request): array
    {
        return [
            'product_sku_id' => $this->product_sku_id,
            'product_name'   => $this->name,
            'sku_code'       => $this->sku_code,
            'orders_count'   => (int) $this->orders_count,
            'quantity_sold'  => (int) $this->quantity_sold,
            'variant_label'  => $this->sku?->variants?->pluck('value')->implode(' • ') ?? 'N/A',
            'revenue'        => (float) $this->revenue,
        ];
    }
}
