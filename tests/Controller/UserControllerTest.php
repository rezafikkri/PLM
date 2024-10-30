<?php

namespace RezaFikkri\PLM\Library {
    if (!function_exists(__NAMESPACE__ . '\header')) {
        function header(string $value) {
            echo $value;
        }
    }
};

namespace RezaFikkri\PLM\Library {
    if (!function_exists(__NAMESPACE__ . '\setcookie')) {
        function setcookie(
            string $name,
            string $value = '',
            int $expires_or_options = 0,
            string $path = '',
        ): void {
            echo "$name: $value";
        }
    }
};

namespace RezaFikkri\PLM\Service {
    if (!function_exists(__NAMESPACE__ . '\setcookie')) {
        function setcookie(
            string $name,
            string $value = '',
            int $expires_or_options = 0,
            string $path = '',
        ): void {
            echo "$name: $value";
        }
    }
};


namespace RezaFikkri\PLM\Controller {
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;
    use RezaFikkri\PLM\{
        Config\Database,
        Entity\User,
        Repository\UserRepository,
    };
    use RezaFikkri\PLM\Entity\Session;
    use RezaFikkri\PLM\Library\Flash;
    use RezaFikkri\PLM\Repository\SessionRepository;

    class UserControllerTest extends TestCase
    {
        private UserController $controller;
        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;
        private Flash $flash;

        protected function setUp(): void
        {
            $this->controller = new UserController;
            $this->flash = flash();

            // clear users
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            // reset $_POST and $_COOKIE
            $_POST = [];
            $_COOKIE = [];
        }

        #[Test]
        public function register(): void
        {
            $this->controller->register();

            // using lookahead assertion
            $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)#s');
        }

        #[Test]
        public function registerError(): void
        {
            $errors = ['Username should not be blank.'];
            $_COOKIE[$this->flash->getName('errors')] = json_encode($errors);

            $this->controller->register();

            $this->expectOutputRegex(
                "#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)(?=.*$errors[0])#s"
            );
        }

        #[Test]
        public function postRegisterSuccess(): void
        {
            $_POST['username'] = 'rezafikkriqwdhnma;vlasdpjapsdj';
            $_POST['password'] = 'passwordkjlaskhdlashdalskdasdahsd';
            $this->controller->postRegister();

            $userRegistered = $this->userRepository->findByUsername($_POST['username']);
            $this->assertNotNull($userRegistered);
            $this->expectOutputString('Location: /users/login');
        }

        #[Test]
        public function postRegisterValidationError(): void
        {
            $_POST['username'] = '';
            $_POST['password'] = '';

            $this->controller->postRegister();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username should not be blank.',
                'Password should not be blank.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => '',
            ]);
            $this->expectOutputString(
                $errors . $form . 'Location: /users/register'
            );
        }

        #[Test]
        public function postRegisterDuplicate(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword('password');
            $this->userRepository->save($user);

            $_POST['username'] = 'rezafikkri';
            $_POST['password'] = 'passwordkjlaskhdlashdalskdasdahsd';
            $this->controller->postRegister();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username already exist. Please choose another Username.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => $_POST['username'],
            ]);
            $this->expectOutputString(
                $errors . $form . 'Location: /users/register'
            );
        }

        #[Test]
        public function login(): void
        {
            $this->controller->login();

            $this->expectOutputRegex('#(?=.*Login)(?=.*Username)(?=.*Password)(?=.*Login User)#s');
        }

        #[Test]
        public function loginError(): void
        {
            $errors = ['Username should not be blank.'];
            $_COOKIE[$this->flash->getName('errors')] = json_encode($errors);

            $this->controller->login();

            $this->expectOutputRegex('#(?=.*Username should not be blank.)#s');
        }

        #[Test]
        public function postLoginSuccess(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword('password');

            $_POST['username'] = $user->getUsername();
            $_POST['password'] = $user->getPassword();

            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $this->controller->postLogin();

            $this->expectOutputRegex("#^$_ENV[SESSION_NAME](?=.*Location: /)#");
        }

        #[Test]
        public function postLoginValidationError(): void
        {
            $_POST['username'] = '';
            $_POST['password'] = '';

            $this->controller->postLogin();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username should not be blank.',
                'Password should not be blank.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => '',
            ]);

            $this->expectOutputString(
                $errors . $form . 'Location: /users/login'
            );
        }

        #[Test]
        public function postLoginUsernameNotFound(): void
        {
            $_POST['username'] = 'RezaF';
            $_POST['password'] = 'rezal';

            $this->controller->postLogin();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username or password is wrong.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => $_POST['username'],
            ]);

            $this->expectOutputString(
                $errors . $form . 'Location: /users/login'
            );
        }

        #[Test]
        public function postLoginWrongPassword(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password123123', PASSWORD_BCRYPT));
            $this->userRepository->save($user);

            $_POST['username'] = $user->getUsername();
            $_POST['password'] = 'wrong';

            $this->controller->postLogin();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username or password is wrong.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => $_POST['username'],
            ]);

            $this->expectOutputString(
                $errors . $form . 'Location: /users/login'
            );
        }

        #[Test]
        public function logout(): void
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

            $this->controller->logout();

            $this->expectOutputString($_ENV['SESSION_NAME'] . ': ' . 'Location: /');
        }

        #[Test]
        public function updateProfile(): void
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

            $this->controller->updateProfile();

            $this->expectOutputRegex('#(?=.*Update Profile)(?=.*rezafikkri)#s');
        }

        #[Test]
        public function updateProfileError(): void
        {
            $user = new User;
            $user->setUsername('rezafikkrihahayeye');
            $user->setPassword('password');
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $errors = ['Username is too short. It should have 4 characters or more.'];
            $_COOKIE[$this->flash->getName('errors')] = json_encode($errors);
            $_COOKIE[$this->flash->getName('form')] = json_encode(['username' => 'geg']);

            $this->controller->updateProfile();

            $this->expectOutputRegex(
                "#(?=.*Update Profile)(?=.*$errors[0])(?=.*geg)#s"
            );
        }

        #[Test]
        public function updateProfileSuccess(): void
        {
            $user = new User;
            $user->setUsername('rezafikkrihahayeye');
            $user->setPassword('password');
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $success = 'Profile updated.';
            $_COOKIE[$this->flash->getName('success')] = json_encode($success);

            $this->controller->updateProfile();

            $this->expectOutputRegex(
                "#(?=.*Update Profile)(?=.*$success)#s"
            );
        }

        #[Test]
        public function postUpdateProfileSuccess(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $_POST['username'] = 'halsjdhfnkjashdfnsadjhsiv';

            $this->controller->postUpdateProfile();

            $userUpdated = $this->userRepository->findById($session->getUserId());
            $this->assertEquals($_POST['username'], $userUpdated->getUsername());
            $this->assertTrue(password_verify('password', $userUpdated->getPassword()));
            
            $success = $this->flash->getName('success') . ': ' . json_encode('Profile updated.');
            $this->expectOutputString($success . 'Location: /users/profile');
        }

        #[Test]
        public function postUpdateProfileValidationError(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $_POST['username'] = '';

            $this->controller->postUpdateProfile();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Username should not be blank.',
            ]);
            $form = $this->flash->getName('form') . ': ' . json_encode([
                'username' => '',
            ]);
            $this->expectOutputString($errors . $form . 'Location: /users/profile');
        }

        #[Test]
        public function updatePassword(): void
        {
            $this->controller->updatePassword();
            $this->expectOutputRegex('#(?=.*Update Password)(?=.*oldPassword)(?=.*newPassword)#s');
        }

        #[Test]
        public function updatePasswordError(): void
        {
            $errors = ['Old Password should not be blank.'];
            $_COOKIE[$this->flash->getName('errors')] = json_encode($errors);

            $this->controller->updatePassword();

            $this->expectOutputRegex(
                "#(?=.*Update Password)(?=.*$errors[0])#s"
            );
        }

        #[Test]
        public function updatePasswordSuccess(): void
        {
            $success = 'Password updated.';
            $_COOKIE[$this->flash->getName('success')] = json_encode($success);

            $this->controller->updatePassword();

            $this->expectOutputRegex(
                "#(?=.*Update Password)(?=.*$success)#s"
            );
        }

        #[Test]
        public function postUpdatePasswordSuccess(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $_POST['oldPassword'] = 'password';
            $_POST['newPassword'] = 'kjawdi91-0293jpawndmasocm(98masd';

            $this->controller->postUpdatePassword();

            $userUpdated = $this->userRepository->findById($session->getUserId());
            $this->assertEquals($user->getUsername(), $userUpdated->getUsername());
            $this->assertTrue(password_verify($_POST['newPassword'], $userUpdated->getPassword()));
            
            $success = $this->flash->getName('success') . ': ' . json_encode('Password updated.');
            $this->expectOutputString($success . 'Location: /users/password');
        }

        #[Test]
        public function postUpdatePasswordValidationError(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->controller->postUpdatePassword();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Old Password should not be blank.',
                'New Password should not be blank.',
            ]);
            $this->expectOutputString($errors . 'Location: /users/password');
        }

        #[Test]
        public function postUpdatePasswordWithWrongOldPassword(): void
        {
            $user = new User;
            $user->setUsername('rezafikkri');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $this->userRepository->save($user);
            
            $session = new Session;
            $session->setId(uniqid());
            $session->setUserId($user->getId());
            $this->sessionRepository->save($session);
            $_COOKIE[$_ENV['SESSION_NAME']] = $session->getId();

            $_POST['oldPassword'] = 'wrong';
            $_POST['newPassword'] = 'i-09123japsodasnm;c=01-09-09kojamsdpascm';

            $this->controller->postUpdatePassword();

            $errors = $this->flash->getName('errors') . ': ' . json_encode([
                'Old Password is wrong.',
            ]);
            $this->expectOutputString($errors . 'Location: /users/password');
        }
    }
};
