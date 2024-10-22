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

    public function getName(string $key): string
    {
        return self::NAME . '_' . $key;
    }

    public function setData(string $key, iterable|string $value): void
    {
        if ($value instanceof ConstraintViolationList) {
            $cookieValues = array_map(
                fn($cv) => $cv->getMessage(),
                $value->getIterator()->getArrayCopy(),
            );
            setcookie(
                $this->getName($key),
                json_encode($cookieValues),
                $this->getExpire(),
                self::PATH,
            );
        } else {
            setcookie(
                $this->getName($key),
                json_encode($value),
                $this->getExpire(),
                self::PATH,
            );
        }
    }

    public function getData(string $key): array|string|null
    {
        return json_decode($_COOKIE[$this->getName($key)] ?? "", true);
    }

    public function clear(): void
    {
        $flashes = ['form','errors'];
        foreach ($flashes as $flash) {
            setcookie($this->getName($flash), expires_or_options: 1, path: self::PATH);
        }
    }
}
