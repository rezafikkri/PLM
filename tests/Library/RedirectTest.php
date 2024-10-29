<?php

namespace RezaFikkri\PLM\Library;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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

if (!function_exists(__NAMESPACE__ . '\header')) {
    function header(string $value) {
        echo $value;
    }
}

class RedirectTest extends TestCase
{
    private Redirect $redirect;
    private Flash $flash;

    protected function setUp(): void
    {
        $this->flash = flash();
        $this->redirect = new Redirect();

        // reset $_POST
        $_POST = [];
        $_COOKIE = [];
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

        $this->expectOutputString(
            $this->flash->getName('form') . ": " . json_encode([
                'username' => $_POST['username']
            ]) . 'Location: /login');
    }

    #[Test]
    public function redirectWithInputWithoutFormData(): void
    {
        $this->redirect->withInput()->to('/logout');
        $this->expectOutputString('Location: /logout');
    }
}
