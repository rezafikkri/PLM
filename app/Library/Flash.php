<?php

namespace RezaFikkri\PLM\Library;

use Symfony\Component\Validator\ConstraintViolationList;

class Flash
{
    const string NAME = 'FLASH';
    const string PATH = '/';

    private function getExpire(): int
    {
        return time() + 60 * 30;
    }

    public function getFlashName(string $key): string
    {
        return self::NAME . '.' . $key;
    }

    public function setFlashData(string $key, iterable|string $value): void
    {
        if ($value instanceof ConstraintViolationList) {
            $cookieValues = array_map(
                fn($cv) => $cv->getMessage(),
                $value->getIterator()->getArrayCopy(),
            );
            setcookie(
                $this->getFlashName($key),
                json_encode($cookieValues),
                $this->getExpire(),
                self::PATH,
            );
        } else {
            setcookie(
                $this->getFlashName($key),
                json_encode($value),
                $this->getExpire(),
                self::PATH,
            );
        }
    }

    public function getFlashData(string $key): array|string|null
    {
        return json_decode($_COOKIE[$this->getFlashName($key)] ?? "", true);
    }

    public function clear(): void
    {
        $flashes = ['form','errors'];
        foreach ($flashes as $flash) {
            setcookie($this->getFlashName($flash), expires_or_options: 1, path: self::PATH);
        }
    }
}
