<?php

namespace App\Exceptions;

use Exception;

class CompletedOrExpiredSaleException extends Exception
{
    protected $message = 'Sale has already been completed.';
}
