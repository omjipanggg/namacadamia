<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->code;
    }
    
    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function locations() {
        return $this->hasMany(StockLocation::class);
    }
}
