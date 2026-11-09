<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use App\Traits\Blamable;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use Blamable, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'formatted_grand_total',
    ];
    
    protected $casts = [
        'status' => PurchaseStatus::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    protected $with = ['branch', 'supplier', 'items', 'creator'];

    protected $withCount = ['items'];

    protected function formattedGrandTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp' . number_format(($this->grand_total ?? 0), 0, ',', '.')
        );
    }
    
    public function branch() {
        return $this->belongsTo(Branch::class);
    }
    
    public function location() {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }
    
    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function items() {
        return $this->hasMany(PurchaseItem::class);
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function punisher() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
