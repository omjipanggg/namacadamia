<?php

namespace App\Services\Search\Manager;

use App\Services\Search\Contracts\SearchProvider;
use Illuminate\Support\Collection;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class SearchManager implements IteratorAggregate
{
    protected Collection $providers;

    public function __construct()
    {
        $this->providers = collect(config('search.providers'))
            ->map(function (string $provider) {
                $instance = app($provider);
                if (!$instance instanceof SearchProvider) {
                    throw new InvalidArgumentException(sprintf(
                        '%s must implement the %s interface.',
                        $provider,
                        SearchProvider::class
                    ));
                }
                return $instance;
            }
        );
    }
    
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->providers->all());
    }

    public function providers(): Collection
    {
        return $this->providers;
    }
}