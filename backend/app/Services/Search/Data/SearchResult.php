<?php

namespace App\Services\Search\Data;

class SearchResult
{
    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $slug,
        public readonly string $url,
        public readonly array $meta = [],
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'image' => $this->image,
            'url' => $this->url,
            'meta' => $this->meta,
        ];
    }
}