<?php

use App\Services\Search\Providers\SearchBlogs;
use App\Services\Search\Providers\SearchCategories;
use App\Services\Search\Providers\SearchProducts;
// use App\Services\Search\Providers\SearchUsers;

return [

    /*
    |--------------------------------------------------------------------------
    | Search Providers
    |--------------------------------------------------------------------------
    |
    | Every provider must implement App\Search\Contracts\SearchProvider.
    | SearchService will automatically execute each provider and merge
    | the results into one collection.
    |
    */

    'providers' => [
        SearchBlogs::class,
        SearchCategories::class,
        SearchProducts::class,
        // SearchUsers::class,
    ],

];