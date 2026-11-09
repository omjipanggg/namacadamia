<?php

namespace App\Services\Document;

use App\Enums\DocumentPrefix;
use App\Models\DocumentSequence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class NumberingService
{
    public function generate(DocumentPrefix $prefix): string
    {
        return DB::transaction(function () use ($prefix) {
            $today = today();

            $sequence = DocumentSequence::query()
                ->where('prefix', $prefix->value)
                ->whereDate('date', $today)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = DocumentSequence::create([
                    'prefix' => $prefix->value,
                    'date' => $today,
                    'last_number' => 0,
                ]);
            }

            $sequence->increment('last_number');
            $sequence->refresh();

            return sprintf('%s-%s-%05d', $prefix->value, $today->format('Ymd'), $sequence->last_number);
        });
    }
}