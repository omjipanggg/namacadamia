<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'title' => $this->data['title'] ?? null,
            'description' => $this->data['description'] ?? null,
            'icon' => $this->data['icon'] ?? 'bell',
            'content' => $this->data['content'] ?? null,
            'variant' => $this->data['variant'] ?? 'primary',
            'category' => $this->data['category'] ?? null,
            'url' => $this->data['url'] ?? null,
            'meta' => $this->data['meta'] ?? [],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'actions' => [
                'read' => true,
                'delete' => true,
            ],
        ];
    }
}
