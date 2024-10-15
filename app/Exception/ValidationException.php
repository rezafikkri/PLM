<?php

namespace RezaFikkri\PLM\Exception;

use Exception;

class ValidationException extends Exception
{
    public function __construct(
        private iterable $messages,
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct('Validation failed', $code, $previous);
    }

    public function getMessages(): iterable
    {
        return $this->messages;
    }
}
