<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'status' => $this->status,
            'notes' => $this->notes,
            'ordered_at' => $this->ordered_at,
            'received_at' => $this->received_at,
            'grand_total' => $this->grand_total,
            'formatted_grand_total' => $this->formattedGrandTotal,
            'location' => StockLocationResource::make($this->whenLoaded('location')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'supplier' => SupplierResource::make($this->whenLoaded('supplier')),
            'items' => PurchaseItemResource::collection($this->whenLoaded('items')),
            'items_sum_quantity' => $this->items_sum_quantity,
            'items_count' => $this->items_count,
            'created_at' => $this->created_at,
        ];
    }
}
