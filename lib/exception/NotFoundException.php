<?php

namespace lib\exception;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}