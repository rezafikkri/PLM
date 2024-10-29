<?php

namespace RezaFikkri\PLM\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
use RezaFikkri\PLM\Model\UserProfileUpdateRequest;
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

        $userInserted = $this->userRepository->findById($response->getUser()->getId());

        $this->assertEquals($request->getUsername(), $userInserted->getUsername());
        $this->assertTrue(password_verify($request->getPassword(), $userInserted->getPassword()));
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

    #[Test]
    public function updateProfileSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest;
        $request->setId($user->getId());
        $request->setUsername('rezafikkrinewyes');
        $request->setPassword('rezapasswordhaha12312398iuasoiudasodjh');

        $this->userService->updateProfile($request);

        $userUpdated = $this->userRepository->findById($request->getId());
        $this->assertEquals($request->getUsername(), $userUpdated->getUsername());
        $this->assertTrue(password_verify($request->getPassword(), $userUpdated->getPassword()));
    }

    #[Test]
    public function updateProfileSuccessWithSameUsernameAndNewPassword(): void
    {
        $user = new User;
        $user->setUsername('rezafikkrii');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest;
        $request->setId($user->getId());
        $request->setUsername('rezafikkrii');
        $request->setPassword('rezapasswordhaha12312398iuasoiudasodjh123kljasdkj');

        $this->userService->updateProfile($request);

        $userUpdated = $this->userRepository->findById($request->getId());
        $this->assertEquals($user->getUsername(), $userUpdated->getUsername());
        $this->assertTrue(password_verify($request->getPassword(), $userUpdated->getPassword()));
    }

    #[Test]
    public function updateProfileFailed(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest;
        $request->setId(0);
        $request->setUsername('rezafikkrinewyes');
        $request->setPassword('   ');

        $this->userService->updateProfile($request);
    }

    #[Test]
    public function updateProfileWithEmptyPassword(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('gegepassword12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest;
        $request->setId($user->getId());
        $request->setUsername('rezafikkrinewyesyes');
        $request->setPassword('');

        $this->userService->updateProfile($request);

        $userUpdated = $this->userRepository->findById($request->getId());
        $this->assertEquals($request->getUsername(), $userUpdated->getUsername());
        $this->assertTrue(password_verify('gegepassword12345', $userUpdated->getPassword()));
    }
}
