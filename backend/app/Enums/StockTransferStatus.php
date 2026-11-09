<?php

namespace App\Enums;

enum StockTransferStatus: string
{
    case DRAFT = 'draft';
    case APPROVED = 'approved';
    case TRANSIT = 'transit';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
