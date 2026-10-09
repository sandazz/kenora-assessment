<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class WorkshopFullException extends Exception
{
    public function __construct(string $message = 'This workshop is full — 0 seats left.')
    {
        parent::__construct($message);
    }
}
