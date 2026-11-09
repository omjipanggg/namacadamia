<?php

namespace App\Exceptions;

use Exception;

class CannotReleaseStockException extends Exception
{
    protected $message = 'Cannot release more stock than currently reserved.';
}
