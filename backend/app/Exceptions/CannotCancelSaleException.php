<?php

namespace App\Exceptions;

use Exception;

class CannotCancelSaleException extends Exception
{
    protected $message = 'This particular sale cannot be cancelled.';
}
