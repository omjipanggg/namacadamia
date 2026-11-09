<?php

namespace App\Services\Search\Data;

class SearchContext
{
    public function __construct(
        public readonly string $query,
        public readonly ?int $limit = 999,
        public readonly ?string $locale = null,
        public readonly mixed $user = null,
        public readonly array $filters = [],
    ) {}
}