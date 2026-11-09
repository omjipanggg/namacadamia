<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasureUnit extends Model
{
    protected $guarded = [];
    
    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->ratio;
    }
    
    public function purchase_items() {
        return $this->hasMany(PurchaseItem::class);
    }

    public function sale_items() {
        return $this->hasMany(SaleItem::class);
    }
}
