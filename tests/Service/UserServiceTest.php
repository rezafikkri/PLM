<?php

namespace RezaFikkri\PLM\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserRegisterRequest;
use RezaFikkri\PLM\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $dbc = Database::getConnection();
        $this->userRepository = new UserRepository($dbc);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll(); // clear all user from db before each test
    }

    #[Test]
    public function registerSuccess(): void
    {
        $request = new UserRegisterRequest;
        $request->setUsername('rezafikkri');
        $request->setPassword('eddkhjrzafikrid&');

        $response = $this->userService->register($request);

        $this->assertEquals($request->getUsername(), $response->getUser()->getUsername());
        $this->assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }

    #[Test]
    public function registerFailed(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest;
        $request->setUsername('');
        $request->setPassword('');

        $this->userService->register($request);
    }

    #[Test]
    public function registerDuplicate(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword('eddkhjrzafikrid&');

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest;
        $request->setUsername('rezafikkri');
        $request->setPassword('eddkhjrzafikrid&');

        $this->userService->register($request);
    }
}
