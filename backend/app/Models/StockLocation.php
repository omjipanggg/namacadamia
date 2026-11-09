<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLocation extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    
    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->code;
    }

    public function getLookupMetaAttribute(): Array
    {
        return [
            'branch_id' => $this->branch_id,
        ];
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function stocks() {
        return $this->hasMany(Stock::class);
    }

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }
}
