<?php

namespace RezaFikkri\PLM\Library;

class Session
{
    public function startSession(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function setFlashData(string $key, array|string $value): void
    {
        $_SESSION['flash'][$key] = $value;
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
