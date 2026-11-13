<?php

namespace App\Models;

use App\Traits\HasSlug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'formatted_price',
    ];

    protected $with = [
        'categories',
    ];

    protected $withCount = [
        'skus'
    ];
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp' . number_format(($this->price ?? 0), 2, ',', '.')
        );
    }

    public function categories() {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }

    public function tags() {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag');
    }

    public function likedByUsers() {
        return $this->belongsToMany(User::class, 'liked_products')->withTimestamps();
    }

    public function skus() {
        return $this->hasMany(ProductSku::class)->where('is_active', true);
    }

    public function discounts() {
        return $this->belongsToMany(Discount::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    public function scopeSort(Builder $query, ?string $sortBy = 'name', ?string $sortDirection = 'asc'): Builder
    {
        if (empty($sortBy)) {
            return $query->orderBy('name');
        }

        $direction = in_array(strtolower($sortDirection ?? 'asc'), ['asc', 'desc']) ? $sortDirection : 'asc';
        return $query->orderBy($sortBy, $direction);
    }

    public function scopeWhereHasCategory(Builder $query, $categories): Builder
    {
        $categories = is_string($categories) ? explode(',', $categories) : $categories;

        return $query->when(!empty($categories), function (Builder $q) use ($categories) {
            $q->whereHas('categories', function (Builder $subQuery) use ($categories) {
                $subQuery->whereIn('name', (array) $categories);
            });
        });
    }
 }
