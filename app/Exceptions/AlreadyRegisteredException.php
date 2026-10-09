<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AlreadyRegisteredException extends Exception
{
    public function __construct(string $message = 'This email is already registered for this workshop.')
    {
        parent::__construct($message);
    }
}
