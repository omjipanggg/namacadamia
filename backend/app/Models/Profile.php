<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\Gender;

class Profile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
