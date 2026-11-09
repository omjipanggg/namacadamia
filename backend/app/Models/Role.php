<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{

    public function getLookupTitleAttribute(): string
    {
        return $this->name;
    }

    public function getLookupSubtitleAttribute(): string
    {
        return '';
    }

    public function getLookupMetaAttribute(): Array
    {
        return [];
    }
}
