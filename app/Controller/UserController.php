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
        $model = [
            'title' => 'Register User',
        ];

        $session = session();
        if ($errors = $session->getFlashData('errors')) {
            $model['errors'] = $errors;
        }

        View::render('User/register', $model);
    }

    public function postRegister(): void
    {
        $userRegisterRequest = new UserRegisterRequest;
        $userRegisterRequest->setUsername($_POST['username']);
        $userRegisterRequest->setPassword($_POST['password']);

        try {
            $this->userService->register($userRegisterRequest);
            redirect()->to('/login');
        } catch (ValidationException $e) {
            session()->setFlashData('errors', $e->getMessages());
            redirect()->withInput()->to('/register');
        }
    }
}
