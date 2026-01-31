<?php

namespace App\Exceptions;

use Exception;

class SameAccountTransferException extends Exception
{
    public function __construct(string $message = 'Cannot transfer to the same account')
    {
        parent::__construct($message);
    }
}
