<?php

namespace App\Services\Search\Providers;

use App\Models\Product;
use App\Services\Search\Data\SearchContext;
use App\Services\Search\Data\SearchResult;

use App\Services\Search\Providers\BaseSearchProvider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchProducts extends BaseSearchProvider
{
    protected function buildQuery(SearchContext $context): Builder
    {
        return Product::query()
            ->where(function ($builder) use ($context) {
                $builder
                    ->where('name', 'like', "%{$context->query}%")
                    ->orWhere('description', 'like', "%{$context->query}%");
            })
            ->where('is_active', true)
            ->with('categories');
    }

    protected function toSearchResult(Model $model): SearchResult
    {
        $product = $model;

        return new SearchResult(
            type: 'products',
            name: $product->name,
            id: $product->id,
            description: $product->description,
            slug: $product->slug,
            url: route('products.show', $product->slug),
            meta: [
                'price' => $product->price,
                'image' => $product->image_url ?? null,
                'categories' => $product->categories->pluck('name')->toArray(),
            ],
        );
    }
}