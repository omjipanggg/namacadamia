<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $sourceColumn = $model->getSlugSourceColumn();
            $slugColumn = $model->getSlugTargetColumn();

            if (empty($model->{$slugColumn}) && ! empty($model->{$sourceColumn})) {
                $model->{$slugColumn} = static::generateUniqueSlug($model->{$sourceColumn}, $slugColumn);
            }
        });

        static::updating(function ($model) {
            $sourceColumn = $model->getSlugSourceColumn();
            $slugColumn = $model->getSlugTargetColumn();

            if ($model->isDirty($sourceColumn) && ! $model->isDirty($slugColumn)) {
                $model->{$slugColumn} = static::generateUniqueSlug(
                    $model->{$sourceColumn},
                    $slugColumn,
                    $model->getKey()
                );
            }
        });
    }

    protected static function generateUniqueSlug(string $value, string $slugColumn, mixed $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $count = 1;

        while (static::where($slugColumn, $slug)->where((new static)->getKeyName(), '!=', $ignoreId)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    public function getSlugTargetColumn(): string
    {
        return property_exists($this, 'slugTarget') ? $this->slugTarget : 'slug';
    }
}