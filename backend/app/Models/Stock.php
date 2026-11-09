<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function location() {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function sku() {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function movements() {
        return $this->hasMany(StockMovement::class, 'stock_id');
    }
}
