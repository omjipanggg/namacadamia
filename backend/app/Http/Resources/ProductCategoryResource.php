<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'products' => CatalogResource::collection($this->whenLoaded('products')),
            'products_count' => $this->whenCounted('products'),
            'is_active' => $this->is_active,
            'description' => $this->description,
        ];
    }
}
