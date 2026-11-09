<?php

namespace App\Services\Search\Providers;

use App\Services\Search\Contracts\SearchProvider;
use App\Services\Search\Data\SearchContext;
use App\Services\Search\Data\SearchResult;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseSearchProvider implements SearchProvider
{
    /**
     * Search for results based on the given query.
     *
     * @param SearchContext $context
     * @return Collection
     */
    final public function search(SearchContext $context): Collection
    {
        $query = $this->buildQuery($context);
        $query = $this->applyConstraints($query, $context);

        if ($context->limit !== null) {
            $query->limit($this->limit($context));
        }
        
        return $query->get()->map(fn (Model $model) => $this->toSearchResult($model))->values();
    }

    /**
     * Build the query for searching.
     *
     * @param SearchContext $context
     * @return Builder
     */
    abstract protected function buildQuery(SearchContext $context): Builder;

    /**
     * Convert a model instance to a SearchResult.
     *
     * @param Model $model
     * @return SearchResult
     */
    abstract protected function toSearchResult(Model $model): SearchResult;

    protected function applyConstraints(Builder $builder, SearchContext $context): Builder
    {
        return $builder;
    }

    protected function limit(SearchContext $context): int
    {
        return $context->limit;
    }
}