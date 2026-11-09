<?php
namespace App\Traits;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Blamable
{
    public static function bootBlamable(): void 
    {
        static::creating(function (Model $model) {
            try {
                if (Schema::hasColumn($model->getTable(), 'created_by')) {
                    $model->created_by = Auth::id() ?? 0;
                }
                if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = Auth::id() ?? 0;
                }
            } catch (\Exception $e) {
                Log::error('Error setting created_by for ' . get_class($model) . ': ' . $e->getMessage());
            }
        });

        static::updating(function (Model $model) {
            try {
                if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = Auth::id() ?? 0;
                }
            } catch (\Exception $e) {
                Log::error('Error setting updated_by for ' . get_class($model) . ': ' . $e->getMessage());
            }
        });
        
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function (Model $model) {
                try {
                    if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                        if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                            $model->timestamps = false; 
                            $model->updateQuietly(['deleted_by' => Auth::id() ?? 0]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error setting deleted_by for ' . get_class($model) . ': ' . $e->getMessage());
                }
            });
            
            if (method_exists(static::class, 'restoring')) {
                static::restoring(function (Model $model) {
                    if ($model->hasColumn('deleted_by')) {
                        $model->deleted_by = null;
                    }
                });
            }
        }
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}