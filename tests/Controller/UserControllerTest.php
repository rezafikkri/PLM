<?php

namespace RezaFikkri\PLM\Controller;

function header(string $value) {
    echo $value;
}

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\UserRepository;

class UserControllerTest extends TestCase
{
    private UserController $controller;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->controller = new UserController;

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
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
        $_POST['username'] = 'rezafikkri';
        $_POST['password'] = 'passwordkjlaskhdlashdalskdasdahsd';
        $this->controller->postRegister();

        $this->expectOutputString('Location: /login');
    }

    #[Test]
    public function postRegisterValidationError(): void
    {
        $_POST['username'] = '';
        $_POST['password'] = '';

        $this->controller->postRegister();

        $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)(?=.*Username should not be blank\.)#s');
    }

    #[Test]
    public function postRegisterDuplicate(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->userRepository->save($user);

        $_POST['username'] = 'rezafikkri';
        $_POST['password'] = 'passwordkjlaskhdlashdalskdasdahsd';
        $this->controller->postRegister();

        $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)(?=.*Username already exist\. Please choose another username\.)#s');
    }
}
