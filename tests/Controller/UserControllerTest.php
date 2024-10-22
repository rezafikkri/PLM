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


namespace RezaFikkri\PLM\Controller {
    use PHPUnit\Framework\Attributes\Test;
    use PHPUnit\Framework\TestCase;
    use RezaFikkri\PLM\{
        Config\Database,
        Entity\User,
        Repository\UserRepository,
    };
    use RezaFikkri\PLM\Library\Flash;

    class UserControllerTest extends TestCase
    {
        private UserController $controller;
        private UserRepository $userRepository;
        private Flash $flash;

        protected function setUp(): void
        {
            $this->controller = new UserController;
            $this->flash = flash();

            // clear users
            $this->userRepository = new UserRepository(Database::getConnection());
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

            $this->expectOutputString('Location: /');
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
    }
};
