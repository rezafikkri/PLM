<?php

namespace RezaFikkri\PLM\Library;

use Symfony\Component\Validator\ConstraintViolationList;

class Flash
{
    const string NAME = 'FLASH';
    const string PATH = '/';

    private function getExpire(): int
    {
        return time() * 60 * 30;
    }

    public function setFlashData(string $key, iterable|string $value): void
    {
        if ($value instanceof ConstraintViolationList) {
            setcookie(
                self::NAME,
                json_encode([$key => $value->getIterator()->getArrayCopy()]),
                $this->getExpire(),
                self::PATH,
            );
        } else {
            setcookie(
                self::NAME,
                json_encode([$key => $value]),
                $this->getExpire(),
                self::PATH,
            );
        }
    }

    public function getFlashData(string $key): array|string|null
    {
        return json_decode($_COOKIE[self::NAME] ?? "", true)[$key] ?? null;
    }

    public function clear(): void
    {
        setcookie(self::NAME, expires_or_options: 1, path: self::PATH);
    }
}
