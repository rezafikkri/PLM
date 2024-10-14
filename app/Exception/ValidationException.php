<?php

namespace RezaFikkri\PLM\Exception;

use Exception;

class ValidationException extends Exception
{
    public function __construct(
        private array $messages,
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct('Validation failed', $code, $previous);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
