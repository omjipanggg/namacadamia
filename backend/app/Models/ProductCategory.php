<?php

namespace App\Models;

use App\Traits\HasSlug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->slug;
    }
    
    public function products() {
        return $this->belongsToMany(Product::class);
    }
}
