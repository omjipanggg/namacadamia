<?php

namespace App\Exceptions;

use Exception;

class PurchaseReceivedException extends Exception
{
    protected $message = 'Purchase has completed just once.';
}
