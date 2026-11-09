<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeValue extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    // protected $with = ['productAttribute'];

    public function getLookupTitleAttribute(): string
    {
        return $this->value;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->productAttribute->name;
    }

    public function getLookupMetaAttribute(): Array
    {
        return [
            'product_attribute_id' => $this->product_attribute_id,
            'abbr' => $this->abbr,
        ];
    }
    
    public function skus() {
        return $this->belongsToMany(ProductSku::class, 'product_details');
    }

    public function productAttribute() {
        return $this->belongsTo(ProductAttribute::class);
    }
}
