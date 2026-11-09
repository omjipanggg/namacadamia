<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Carbon;

class BlogResource extends JsonResource
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
            'name' => $this->name,
            'content' => $this->content,
            'slug' => $this->slug,
            'meta' => $this->meta,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'author' => $this->whenLoaded('author'),
            // 'created_at' => Carbon::parse($this->created_at)->format('F dS, Y H:i a'),
            'created_at' => $this->created_at,
        ];
    }
}
