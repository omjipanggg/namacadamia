<?php

namespace App\Services\Search\Contracts;

use App\Services\Search\Data\SearchContext;
use Illuminate\Support\Collection;

interface SearchProvider
{
    /**
     * Search for results based on the given query.
     *
     * @param SearchContext $context
     * @return Collection
     */
    public function search(SearchContext $context): Collection;
}