<?php

namespace App\Services\Search\Providers;

use App\Models\ProductCategory;
use App\Services\Search\Data\SearchContext;
use App\Services\Search\Data\SearchResult;

use App\Services\Search\Providers\BaseSearchProvider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchCategories extends BaseSearchProvider
{
    protected function buildQuery(SearchContext $context): Builder
    {
        return ProductCategory::query()
            ->where(function ($builder) use ($context) {
                $builder
                    ->where('name', 'like', "%{$context->query}%")
                    ->orWhere('description', 'like', "%{$context->query}%");
            });
    }

    protected function toSearchResult(Model $model): SearchResult
    {
        $category = $model;
        
        return new SearchResult(
            type: 'category',
            name: $category->name,
            id:$category->id,
            description: $category->description,
            slug: $category->slug,
            url: route('category.show', $category->slug),
            meta: [],
        );
    }
}