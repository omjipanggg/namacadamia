<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];

    public function measureUnit() {
        return $this->belongsTo(MeasureUnit::class);
        }
        
    public function measureUnitReceived() {
        return $this->belongsTo(MeasureUnit::class, 'received_measure_unit_id');
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function sku() {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }
}
