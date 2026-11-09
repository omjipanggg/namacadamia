<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case RETURN = 'return';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case ADJUSTMENT = 'adjustment';
    case RESERVE = 'reserve';
    case RELEASE = 'release';
    case INITIAL = 'initial';
}
