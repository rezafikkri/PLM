<?php

namespace RezaFikkri\PLM\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
use RezaFikkri\PLM\Model\UserRegisterRequest;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $sessionRepository = new SessionRepository(Database::getConnection());
        $this->userService = new UserService($this->userRepository);

        $sessionRepository->deleteAll();
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

    #[Test]
    public function loginNotFound(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest;
        $request->setUsername('rezafikkri');
        $request->setPassword('rezafikkri');

        $this->userService->login($request);
    }

    #[Test]
    public function loginWrongPassword(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest;
        $request->setUsername('rezafikkri');
        $request->setPassword('password12');

        $this->userService->login($request);
    }

    #[Test]
    public function loginSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserLoginRequest;
        $request->setUsername('rezafikkri');
        $request->setPassword('password12345');

        $response = $this->userService->login($request);       

        $this->assertEquals($request->getUsername(), $response->getUser()->getUsername());
        $this->assertTrue(password_verify($request->getPassword(), $response->getUser()->getPassword()));
    }
}
