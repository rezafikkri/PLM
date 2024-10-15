<?php

namespace RezaFikkri\PLM\Library;

use Symfony\Component\Validator\ConstraintViolationList;

class Session
{
    public function startSession(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function setFlashData(string $key, iterable|string $value): void
    {
        if ($value instanceof ConstraintViolationList) {
            foreach ($value as $v) {
                $_SESSION['flash'][$key][] = $v->getMessage();
            }
        } else {
            $_SESSION['flash'][$key] = $value;
        }
    }

    public function getFlashData(string $key): array|string|null
    {
        return $_SESSION['flash'][$key] ?? null;
    }

    public function clear(array $keys = []): void
    {
        $keys = ['flash', 'form', ...$keys];
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }
}
