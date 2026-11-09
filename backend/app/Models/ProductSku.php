<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductSku extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    
    protected $appends = [
        'formatted_selling_price',
    ];
    
    protected function formattedSellingPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp' . number_format(($this->selling_price ?? 0), 2, ',', '.')
        );
    }
    
    public function getLookupTitleAttribute(): string
    {
        return $this->product->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        $variants = $this->variants
            ->filter()
            ->pluck('value')
            ->implode(' · ');

        if (!empty($variants)) {
            return $variants;
        }

        return 'N/A';
        return $this->code;
    }

    public function getLookupMetaAttribute(): Array
    {
        return [
            'price' => (int) $this->selling_price ?? 0,
            'stock_locations' => $this->stocks->pluck('stock_location_id')->unique()->values()->toArray(),
        ];
    }

    public function getStockAttribute(): int
    {
        return $this->stocks()->sum('quantity');
    }
    
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function variants() {
        return $this->belongsToMany(ProductAttributeValue::class, 'product_details');
    }

    public function stocks() {
        return $this->hasMany(Stock::class);
    }

    public function media() {
        return $this->hasMany(ProductMedia::class);
    }

    public function coverImage() {
        return $this->hasOne(ProductMedia::class)->where('is_primary', true);
    }
}
