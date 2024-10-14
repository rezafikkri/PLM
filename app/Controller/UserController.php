<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserRegisterRequest;
use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $dbc = Database::getConnection();
        $userRepository = new UserRepository($dbc);
        $this->userService = new UserService($userRepository);
    }

    public function register(): void
    {
        View::render('User/register', [
            'title' => 'Register User',
        ]);
    }

    public function postRegister(): void
    {
        $userRegisterRequest = new UserRegisterRequest;
        $userRegisterRequest->setUsername($_POST['username']);
        $userRegisterRequest->setPassword($_POST['password']);

        try {
            $this->userService->register($userRegisterRequest);
            header('Location: /login');
            if ($_ENV['APP_ENV'] != 'development') {
                exit();
            }
        } catch (ValidationException $e) {
            View::render('User/register', [
                'title' => 'Register User',
                'errors' => $e->getMessages(),
            ]);
        }
    }
}
