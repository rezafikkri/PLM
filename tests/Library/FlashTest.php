<?php

namespace RezaFikkri\PLM\Library;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

if (!function_exists(__NAMESPACE__ . '\setcookie')) {
    function setcookie(
        string $name,
        string $value = '',
        int $expires_or_options = 0,
        string $path = '',
    ): void {
        echo "$name: $value";
    }
}

class FlashTest extends TestCase
{
    private Flash $flash;

    protected function setUp(): void
    {
        $this->flash = flash();
        $_COOKIE = [];
    }

    #[Test]
    public function setFlashData(): void
    {
        $this->flash->setFlashData('email', 'rezafikkri@gmail.com');
        $this->expectOutputString(
            $this->flash->getName('email') . ": " . json_encode('rezafikkri@gmail.com')
        );
    }

    #[Test]
    public function setFlashDataWithIterableValue(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'Username cannot be blank.',
                messageTemplate: null,
                parameters: [],
                root: '',
                propertyPath: null,
                invalidValue: '',
            ),
        ]);

        $this->flash->setFlashData('errors', $violations);

        $errors = array_map(
            fn($cv) => $cv->getMessage(),
            $violations->getIterator()->getArrayCopy(),
        );
        $this->expectOutputString(
            $this->flash->getName('errors')  . ": " . json_encode($errors)
        );
    }

    #[Test]
    public function getFlashData(): void
    {
        $_COOKIE[$this->flash->getName('email')] = json_encode('adelina@gmail.com');

        $data = $this->flash->getFlashData('email');
        $this->assertEquals('adelina@gmail.com', $data);
    }

    #[Test]
    public function getFlashDataNotFound(): void
    {
        $data = $this->flash->getFlashData('email');
        $this->assertNull($data);
    }

    #[Test]
    public function clear(): void
    {
        $this->flash->clear();
        $this->expectOutputString(
            $this->flash->getName('form') . ': ' . $this->flash->getName('errors') . ': '
        );
    }
}
