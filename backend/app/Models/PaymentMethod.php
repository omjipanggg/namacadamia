<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
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
