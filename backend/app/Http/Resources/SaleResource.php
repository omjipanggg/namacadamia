<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'channel' => SalesChannelResource::make($this->whenLoaded('salesChannel')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'applied_discounts' => DiscountResource::collection($this->whenLoaded('discounts')),
            'sale_discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'grand_total' => $this->grand_total,
            'notes' => $this->notes,
            'ordered_at' => $this->ordered_at,
            'created_at' => $this->created_at,
            'created_by' => UserResource::make($this->whenLoaded('creator')),
        ];
    }
}
