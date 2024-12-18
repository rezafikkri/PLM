<?php

namespace RezaFikkri\PLM\Service;

use DI\Container;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Entity\User;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
use RezaFikkri\PLM\Model\UserPasswordUpdateRequest;
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
        $container = new Container([
            PDO::class => Database::getConnection(),
        ]);
        $this->userRepository = $container->get(UserRepository::class);
        $sessionRepository = $container->get(SessionRepository::class);
        $this->userService = $container->get(UserService::class);

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
    public function registerValidationError(): void
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

        $this->userService->updateProfile($request);

        $userUpdated = $this->userRepository->findById($user->getId());
        $this->assertEquals($request->getUsername(), $userUpdated->getUsername());
        $this->assertTrue(password_verify('password12345', $userUpdated->getPassword()));
    }

    #[Test]
    public function updateProfileValidationError(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest;
        $request->setId(0);
        $request->setUsername('rez');

        $this->userService->updateProfile($request);
    }

    #[Test]
    public function updateProfileNotFound(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest;
        $request->setId(0);
        $request->setUsername('rezafikkrinewyeshehe');

        $this->userService->updateProfile($request);
    }

    #[Test]
    public function updatePasswordSuccess(): void
    {
        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest;
        $request->setId($user->getId());
        $request->setOldPassword('password12345');
        $request->setNewPassword('joiuoje90r8-19321jisfpqnviajs-fd0qwie;');

        $this->userService->updatePassword($request);

        $userUpdated = $this->userRepository->findById($user->getId());
        $this->assertEquals($user->getUsername(), $userUpdated->getUsername());
        $this->assertTrue(password_verify($request->getNewPassword(), $userUpdated->getPassword()));
    }

    #[Test]
    public function updatePasswordValidationError(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest;
        $request->setId(0);
        $request->setOldPassword('');
        $request->setNewPassword('');

        $this->userService->updatePassword($request);
    }

    #[Test]
    public function updatePasswordWithWrongOldPassword(): void
    {
        $this->expectException(ValidationException::class);

        $user = new User;
        $user->setUsername('rezafikkri');
        $user->setPassword(password_hash('password12345', PASSWORD_BCRYPT));

        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest;
        $request->setId($user->getId());
        $request->setOldPassword('wrong');
        $request->setNewPassword('kljalskdnas09diawejoqijawnasijdafnm9uad1-23');

        $this->userService->updatePassword($request);
    }

    #[Test]
    public function updatePasswordNotFound(): void
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest;
        $request->setId(0);
        $request->setOldPassword('unknown');
        $request->setNewPassword('poiqwe90123op[aofnaisfmasfopp-1230123efgfg]');

        $this->userService->updatePassword($request);
    }
}
