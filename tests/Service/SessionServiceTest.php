<?php

namespace RezaFikkri\PLM\Service;

require_once __DIR__ . '/../helper.php';

use DI\Container;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\Session;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private User $user;

    protected function setUp(): void
    {
        $container = new Container([
            PDO::class => Database::getConnection(),
        ]);
        $this->sessionRepository = $container->get(SessionRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->sessionService = $container->get(SessionService::class);

        $this->sessionRepository->deleteAll();// clear all session data before each test
        $this->userRepository->deleteAll();

        $_COOKIE = [];

        $this->user = new User;
        $this->user->setUsername('rezafikkri');
        $this->user->setPassword('password');
        $this->userRepository->save($this->user);
    }

    #[Test]
    public function create(): void
    {
        $session = $this->sessionService->create($this->user->getId());

        $result = $this->sessionRepository->findById($session->getId());
        $this->assertEquals($this->user->getId(), $result->getUserId());
        $this->expectOutputString("$_ENV[SESSION_NAME]: {$session->getId()}");
        $this->assertEquals($this->user->getId(), $session->getUserId());
    }

    #[Test]
    public function destroy(): void
    {
        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId($this->user->getId());
        $this->sessionRepository->save($session);
        $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

        $destroy = $this->sessionService->destroy();

        $result = $this->sessionRepository->findById($session->getId());
        $this->assertNull($result);
        $this->expectOutputString("$_ENV[SESSION_NAME]: ");
        $this->assertTrue($destroy);
    }

    #[Test]
    public function destroyNotFound(): void
    {
        $destroy = $this->sessionService->destroy();
        $this->assertFalse($destroy);
    }

    #[Test]
    public function current(): void
    {
        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId($this->user->getId());
        $this->sessionRepository->save($session);
        $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

        $current = $this->sessionService->current();

        $this->assertEquals($this->user->getId(), $current->getId());
        $this->assertEquals($this->user->getUsername(), $current->getUsername());
    }
}
