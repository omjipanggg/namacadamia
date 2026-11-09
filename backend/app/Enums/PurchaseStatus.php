<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case ORDERED = 'ordered';
    case PARTIAL = 'partial';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}