<?php

namespace App\Exceptions;

use Exception;

class NotFoundSKUException extends Exception
{
    protected $message = 'SKU not found.';
}
