<?php

namespace App\Models;

use App\Traits\HasSlug;

use Illuminate\Database\Eloquent\Model;

class SalesChannel extends Model
{
    use HasSlug;
    
    protected $guarded = [];

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return $this->description ?? '';
    }
}
