<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getLookupTitleAttribute(): string
    {
        return $this->phone;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->name ?? "";
    }    
}
