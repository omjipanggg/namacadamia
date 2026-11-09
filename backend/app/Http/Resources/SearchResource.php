<?php

namespace App\Http\Resources;

use App\Services\Search\Data\SearchResult;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->resource->type,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'slug' => $this->resource->slug,
            'url' => $this->resource->url,
            'meta' => $this->resource->meta,
        ];
    }
}
