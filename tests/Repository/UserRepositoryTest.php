<?php

namespace RezaFikkri\PLM\Repository;

use DI\Container;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        $container = new Container([
            PDO::class => Database::getConnection(),
        ]);
        $this->repository = $container->get(UserRepository::class);
        $sessionRepository = $container->get(SessionRepository::class);

        $sessionRepository->deleteAll();
        $this->repository->deleteAll(); // clear all users before each test
    }

    #[Test]
    public function saveSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');

        $this->repository->save($user);

        $result = $this->repository->findById($user->getId());

        $this->assertEquals($user->getUsername(), $result->getUsername());
        $this->assertEquals($user->getPassword(), $result->getPassword());
    }

    #[Test]
    public function findByIdNotFound(): void
    {
        $user = $this->repository->findById(0);
        $this->assertNull($user);
    }

    #[Test]
    public function findByUsernameSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->repository->save($user);

        $result = $this->repository->findByUsername($user->getUsername());
        $this->assertEquals($user->getUsername(), $result->getUsername());
        $this->assertEquals($user->getPassword(), $result->getPassword());
    }

    #[Test]
    public function findByUsernameNotFound(): void
    {
        $user = $this->repository->findByUsername('notfound');
        $this->assertNull($user);
    }

    #[Test]
    public function updateSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('password');
        $this->repository->save($user);

        $newUser = new User;
        $newUser->setId($user->getId());
        $newUser->setUsername('rezafikkrinew');
        $newUser->setPassword('passwordnew');

        $this->repository->update($newUser);

        $userUpdated = $this->repository->findById($user->getId());
        $this->assertEquals($newUser->getUsername(), $userUpdated->getUsername());
        $this->assertEquals($newUser->getPassword(), $userUpdated->getPassword());
    }
}
