<?php

namespace RezaFikkri\PLM\Library;

require_once __DIR__ . '/../helper.php';

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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
