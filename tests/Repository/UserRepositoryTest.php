<?php

namespace RezaFikkri\PLM\Repository;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new UserRepository(Database::getConnection());
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
}
