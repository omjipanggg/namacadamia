<?php

namespace App\Services\Search\Providers;

use App\Models\Blog;
use App\Services\Search\Data\SearchContext;
use App\Services\Search\Data\SearchResult;

use App\Services\Search\Providers\BaseSearchProvider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SearchBlogs extends BaseSearchProvider
{
    protected function buildQuery(SearchContext $context): Builder
    {
        return Blog::query()
            ->where(function ($builder) use ($context) {
                $builder
                    ->where('name', 'like', "%{$context->query}%")
                    ->orWhere('description', 'like', "%{$context->query}%");
            })
            ->where('is_active', true);
    }

    protected function toSearchResult(Model $model): SearchResult
    {
        $blog = $model;
        
        return new SearchResult(
            type: 'blog',
            name: $blog->name,
            id: $blog->id,
            description: $blog->description,
            slug: $blog->slug,
            url: route('articles.show', $blog->slug),
            meta: [
                'image' => $blog->image_url ?? null,
            ],
        );
    }
}