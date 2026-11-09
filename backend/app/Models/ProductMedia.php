<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $guarded = [];

    public function sku() {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }
}
