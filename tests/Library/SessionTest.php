<?php

namespace RezaFikkri\PLM\Library;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class SessionTest extends TestCase
{
    private Session $session;

    public static function setUpBeforeClass(): void
    {
        session()->startSession();
    }

    protected function setUp(): void
    {
        $this->session = new Session;
        $this->session->clear();
    }

    #[Test]
    public function setFlashData(): void
    {
        $this->session->setFlashData('email', 'rezafikkri@gmail.com');
        $this->assertEquals('rezafikkri@gmail.com', $_SESSION['flash']['email']);
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

        $this->session->setFlashData('errors', $violations);

        $this->assertIsArray($_SESSION['flash']['errors']);
        $this->assertContainsOnly('string', $_SESSION['flash']['errors']);
        $this->assertContains('Username cannot be blank.', $_SESSION['flash']['errors']);
    }

    #[Test]
    public function getFlashData(): void
    {
        $_SESSION['flash']['email'] = 'adelina@gmail.com';
        $data = $this->session->getFlashData('email');
        $this->assertEquals('adelina@gmail.com', $data);
    }

    #[Test]
    public function getFlashDataNotFound(): void
    {
        $data = $this->session->getFlashData('email');
        $this->assertNull($data);
    }


    #[Test]
    public function clearWithExtraKeys(): void
    {
        $_SESSION['other'];
        $_SESSION['union'];
        $this->session->clear(['other', 'union']);

        $this->assertNull($_SESSION['other']);
        $this->assertNull($_SESSION['union']);
    }
}
