<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['author'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    
    // protected function casts(): array
    // {
    //     return [
    //         'created_at' => 'datetime:Y-m-d H:i:s',
    //     ];
    // }

    public function author() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
