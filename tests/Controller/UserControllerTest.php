<?php

namespace RezaFikkri\PLM\Library {
    function header(string $value) {
        echo $value;
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

            $this->expectOutputString('Location: /login');
        }

        #[Test]
        public function postRegisterValidationError(): void
        {
            $_POST['username'] = '';
            $_POST['password'] = '';

            $this->controller->postRegister();

            $this->expectOutputString('Location: /register');
            $this->assertNotNull($_SESSION['flash']['errors'] ?? null);
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

            $this->expectOutputString('Location: /register');
            $this->assertNotNull($_SESSION['flash']['errors'] ?? null);
            $this->assertEquals('Username already exist. Please choose another Username.', $_SESSION['flash']['errors'][0]);
        }
    }
};
