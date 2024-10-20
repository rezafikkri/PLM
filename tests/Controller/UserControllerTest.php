<?php

namespace RezaFikkri\PLM\Library {
    if (!function_exists(__NAMESPACE__ . '\header')) {
        function header(string $value) {
            echo $value;
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

    class UserControllerTest extends TestCase
    {
        private UserController $controller;
        private UserRepository $userRepository;

        public static function setUpBeforeClass(): void
        {
            session()->startSession();
        }

        protected function setUp(): void
        {
            $this->controller = new UserController;

            // clear users
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
            // clear sessions (flash, form)
            session()->clear();

            // reset $_POST
            $_POST = [];
        }

        #[Test]
        public function register(): void
        {
            $this->controller->register();

            // using lookahead assertion
            $this->expectOutputRegex('#(?=.*Register)(?=.*Username)(?=.*Password)(?=.*Register User)#s');
        }

        #[Test]
        public function postRegisterSuccess(): void
        {
            $_POST['username'] = 'rezafikkri';
            $_POST['password'] = 'passwordkjlaskhdlashdalskdasdahsd';
            $this->controller->postRegister();

            $this->expectOutputString('Location: /users/login');
        }

        #[Test]
        public function postRegisterValidationError(): void
        {
            $_POST['username'] = '';
            $_POST['password'] = '';

            $this->controller->postRegister();

            $this->expectOutputString('Location: /users/register');
            $this->assertNotNull($_SESSION['flash']['errors']);
            $this->assertEquals('Username should not be blank.', $_SESSION['flash']['errors'][0]);
            $this->assertEquals('Password should not be blank.', $_SESSION['flash']['errors'][1]);
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

            $this->expectOutputString('Location: /users/register');
            $this->assertNotNull($_SESSION['flash']['errors']);
            $this->assertEquals('Username already exist. Please choose another Username.', $_SESSION['flash']['errors'][0]);
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
            session()->setFlashData('errors', [
                'Username should not be blank.',
                'Password should not be blank.',
            ]);
            $this->controller->login();

            $this->expectOutputRegex('#(?=.*Username should not be blank.)(?=.*Password should not be blank.)#s');
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

            $this->expectOutputString('Location: /');
        }


        #[Test]
        public function postLoginValidationError(): void
        {
            $_POST['username'] = '';
            $_POST['password'] = '';

            $this->controller->postLogin();

            $this->expectOutputString('Location: /users/login');
            $this->assertNotNull($_SESSION['flash']['errors']);
            $this->assertContains('Username should not be blank.', $_SESSION['flash']['errors']);
            $this->assertContains('Password should not be blank.', $_SESSION['flash']['errors']);
        }

        #[Test]
        public function postLoginUsernameNotFound(): void
        {
            $_POST['username'] = 'RezaF';
            $_POST['password'] = 'reza';

            $this->controller->postLogin();

            $this->expectOutputString('Location: /users/login');
            $this->assertNotNull($_SESSION['flash']['errors']);
            $this->assertContains('Username or password is wrong.', $_SESSION['flash']['errors']);
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

            $this->expectOutputString('Location: /users/login');
            $this->assertNotNull($_SESSION['flash']['errors']);
            $this->assertContains('Username or password is wrong.', $_SESSION['flash']['errors']);
        }
    }
};
