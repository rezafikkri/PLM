<?php

namespace RezaFikkri\PLM\Library;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\header')) {
    function header(string $value) {
        echo $value;
    }
}

class RedirectTest extends TestCase
{
    private Redirect $redirect;

    public static function setUpBeforeClass(): void
    {
        session()->startSession();
    }

    protected function setUp(): void
    {
        $this->redirect = new Redirect();
        // clear session (flash, form)
        session()->clear();

        // reset $_POST
        $_POST = [];
    }

    #[Test]
    public function redirectTo(): void
    {
        $this->redirect->to('/register');
        $this->expectOutputString('Location: /register');
    }

    #[Test]
    public function redirectWithInput(): void
    {
        $_POST['username'] = 'rezafikkriusernametest';
        $_POST['password'] = 'thisispasswordlong';

        $this->redirect->withInput()->to('/login');

        $this->expectOutputString('Location: /login');
        $this->assertEquals('rezafikkriusernametest', $_SESSION['form']['username']);
        $this->assertNull($_SESSION['form']['password']);
    }

    #[Test]
    public function redirectWithInputWithoutFormData(): void
    {
        $this->redirect->withInput()->to('/logout');

        $this->expectOutputString('Location: /logout');
        $this->assertNull($_SESSION['form']);
    }
}
