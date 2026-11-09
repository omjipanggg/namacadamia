<?php

namespace App\Models;

use App\Enums\StockTransferStatus;
use App\Traits\Blamable;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use Blamable;
    
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => StockTransferStatus::class,
        ];
    }

    public function sourceLocation() {
        return $this->belongsTo(StockLocation::class, 'source_location_id');
    }

    public function targetLocation() {
        return $this->belongsTo(StockLocation::class, 'target_location_id');
    }

    public function items() {
        return $this->hasMany(StockTransferItem::class);
    }

    public function movements() {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
