<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $guarded = [];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    public function unit() {
        return $this->belongsTo(MeasureUnit::class, 'measure_unit_id');
    }    
    
    public function sku() {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function location() {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }
}
