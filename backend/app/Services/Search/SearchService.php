<?php
namespace App\Services\Search;

use App\Services\Search\Manager\SearchManager;
use App\Services\Search\Data\SearchContext;

use Illuminate\Support\Collection;

class SearchService
{
    public function __construct(protected SearchManager $manager) {}
    
    public function search(SearchContext $context): Collection
    {
        return $this->manager->providers()->flatMap(
            fn ($provider) => $provider->search($context)
        )->values();
    }
}