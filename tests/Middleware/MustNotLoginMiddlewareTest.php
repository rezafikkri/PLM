<?php

namespace RezaFikkri\PLM\Middleware;

require_once __DIR__ . '/../helper.php';

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\Session;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;

class MustNotLoginMiddlewareTest extends TestCase
{
    private MustNotLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->middleware = new MustNotLoginMiddleware;
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $_COOKIE = [];
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    #[Test]
    public function beforeGuest(): void
    {
        $this->middleware->before();
        $this->expectOutputString('');
    }

    #[Test]
    public function beforeLogin(): void
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

        $this->middleware->before();
        $this->expectOutputString('Location: /');
    }
}
