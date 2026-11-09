<?php

namespace App\Filters;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Builder;

class SaleFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, $search) => $query->where('code', 'like', "%{$search}%")
            )
            ->when(
                $filters['status'] ?? null,
                fn (Builder $query, $status) => $status === 'all' ? $query : $query->where('status', $status instanceof SaleStatus ? $status->value : $status)
            )
            ->when(
                $filters['branch_id'] ?? null,
                fn (Builder $query, $branchId) => $query->where('branch_id', $branchId)
            )
            ->when(
                $filters['sales_channel_id'] ?? null,
                fn (Builder $query, $channelId) => $query->where('sales_channel_id', $channelId)
            )
            ->when(
                $filters['customer_id'] ?? null,
                fn (Builder $query, $customerId) => $query->where('customer_id', $customerId)
            )
            ->when(
                $filters['date_from'] ?? null,
                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date)
            )
            ->when(
                $filters['date_to'] ?? null,
                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date)
            )
            ->when(
                $filters['sort_by'] ?? null,
                fn (Builder $query, $column) => $query->orderBy($column, $filters['sort_direction']),
                fn (Builder $query) => $query->orderBy('created_at', 'desc')
            );
    }
}