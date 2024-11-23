<?php

namespace RezaFikkri\PLM\Repository;

use DI\Container;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\Session;
use RezaFikkri\PLM\Entity\User;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $container = new Container([
            PDO::class => Database::getConnection(),
        ]);
        $this->sessionRepository = $container->get(SessionRepository::class);
        $this->userRepository = $container->get(UserRepository::class);

        $this->sessionRepository->deleteAll();// clear all session data before each test
        $this->userRepository->deleteAll();
    }

    #[Test]
    public function saveSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId($user->getId());

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->getId());
        $this->assertEquals($session->getId(), $result->getId());
        $this->assertEquals($session->getUserId(), $result->getUserId());
    }

    #[Test]
    public function findByIdNotFound(): void
    {
        $result = $this->sessionRepository->findById(0);
        $this->assertNull($result);
    }

    #[Test]
    public function deleteByIdSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('test');
        $this->userRepository->save($user);

        $session = new Session;
        $session->setId(uniqid());
        $session->setUserId($user->getId());
        $this->sessionRepository->save($session);

        $delete = $this->sessionRepository->deleteById($session->getId());
        $this->assertTrue($delete);
    }

    #[Test]
    public function deleteByIdNotFound(): void
    {
        $delete = $this->sessionRepository->deleteById(0);
        $this->assertFalse($delete);
    }
}
