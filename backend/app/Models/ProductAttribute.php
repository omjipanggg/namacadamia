<?php

namespace App\Models;

use App\Traits\HasSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model
{
    use HasSlug, SoftDeletes;
    
    protected $guarded = [];

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->slug;
    }

    protected static function booted(): void
    {
        static::deleting(function (ProductAttribute $attribute) {
            $attribute->values()->delete();
        });
    }
    
    public function values() {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
