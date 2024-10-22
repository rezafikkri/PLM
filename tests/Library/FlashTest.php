<?php

namespace RezaFikkri\PLM\Library;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

function setcookie(
    string $name,
    string $value = '',
    int $expires_or_options = 0,
    string $path = '',
): void {
    echo "$name: $value";
}

class FlashTest extends TestCase
{
    private Flash $flash;

    protected function setUp(): void
    {
        $this->flash = new Flash;
        $_COOKIE = [];
    }

    #[Test]
    public function setFlashData(): void
    {
        $this->flash->setFlashData('email', 'rezafikkri@gmail.com');
        $this->expectOutputString(
            Flash::NAME . ": " . json_encode(['email' => 'rezafikkri@gmail.com'])
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

        $this->expectOutputString(
            Flash::NAME . ": " . json_encode(['errors' => $violations->getIterator()->getArrayCopy()])
        );
    }

    #[Test]
    public function getFlashData(): void
    {
        $_COOKIE[Flash::NAME] = json_encode(['email' => 'adelina@gmail.com']);

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
        $this->expectOutputString(Flash::NAME . ': ');
    }
}
