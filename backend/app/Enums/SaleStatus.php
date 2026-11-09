<?php

namespace App\Enums;

enum SaleStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}