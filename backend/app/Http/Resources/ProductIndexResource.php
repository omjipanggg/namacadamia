<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'categories' => ProductCategoryResource::collection($this->whenLoaded('categories')),
            'skus_count' => $this->skus_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}