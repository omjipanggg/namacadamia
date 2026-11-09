<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->description ?? "";
    }
    
    protected function casts() {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }
}
