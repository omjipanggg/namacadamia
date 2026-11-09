<?php

namespace App\Models;

use App\Enums\StockMovementType;
use App\Traits\Blamable;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use Blamable;
    
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
        ];
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
