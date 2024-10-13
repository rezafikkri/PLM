<?php

namespace RezaFikkri\PLM\Controller;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Repository\UserRepository;

class UserControllerTest extends TestCase
{
    private UserController $controller;

    public function setUp(): void
    {
        $this->controller = new UserController;

        $userRepository = new UserRepository(Database::getConnection());
        $userRepository->deleteAll();
    }

    #[Test]
    public function register(): void
    {
        $this->controller->register();

        // using lookahead assertion
        $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)#s');
    }

    #[Test]
    public function postRegisterSuccess(): void
    {
        $this->markTestIncomplete();
    }

    #[Test]
    public function postRegisterValidationError(): void
    {
        $_POST['username'] = 're';
        $_POST['password'] = 'password';

        $this->controller->postRegister();

        $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)#s');
    }

    #[Test]
    public function postRegisterDuplicate(): void
    {
        $this->markTestIncomplete();
    }
}
