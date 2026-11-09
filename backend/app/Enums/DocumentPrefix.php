<?php

namespace App\Enums;

enum DocumentPrefix: string
{
    case PURCHASE = 'POR';
    case SALE = 'SOR';
    case TRANSFER = 'TRF';
    case OPNAME = 'OPN';
    case PAYMENT = 'PAY';
    case S_RETURN = 'SRT';
    case P_RETURN = 'PRT';
}