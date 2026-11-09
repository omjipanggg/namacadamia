<?php

namespace App\Models;

use App\Enums\SaleStatus;
use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use Blamable, SoftDeletes;
    
    protected $guarded = [];
    
    protected $casts = [
        'status' => SaleStatus::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }
        
    public function items() {
        return $this->hasMany(SaleItem::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function salesChannel() {
        return $this->belongsTo(SalesChannel::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }
    
    public function discounts() {
        return $this->belongsToMany(Discount::class, 'applied_discounts');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function punisher() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
