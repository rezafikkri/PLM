<?php

namespace RezaFikkri\PLM\Controller;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\Session;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;

class HomeControllerTest extends TestCase
{
    private HomeController $controller;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->controller = new HomeController;
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        $_COOKIE = [];
    }

    #[Test]
    public function guest(): void
    {
        $this->controller->index();
        $this->expectOutputRegex('#(?=.*Register)(?=.*Login)#s');
    }

    #[Test]
    public function loggedIn(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->userRepository->save($user);

        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId($user->getId());
        $this->sessionRepository->save($session);

        $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

        $this->controller->index();

        $this->expectOutputRegex(
            "#(?=.*Hello {$user->getUsername()})(?=.*Profile)(?=.*Password)(?=.*Logout)#s"
        );
    }
}