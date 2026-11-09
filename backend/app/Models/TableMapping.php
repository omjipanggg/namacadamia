<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableMapping extends Model
{
    /** @use HasFactory<\Database\Factories\TableMappingFactory> */
    use HasFactory;

    protected $guarded = [];
}
