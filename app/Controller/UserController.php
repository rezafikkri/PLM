<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
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
        $request = new UserRegisterRequest;
        $request->setUsername($_POST['username']);
        $request->setPassword($_POST['password']);

        try {
            $this->userService->register($request);
            redirect()->to('/users/login');
        } catch (ValidationException $e) {
            session()->setFlashData('errors', $e->getMessages());
            redirect()->withInput()->to('/users/register');
        }
    }

    public function login(): void
    {
        $model = [
            'title' => 'Login User',
        ];

        $session = session();
        if ($errors = $session->getFlashData('errors')) {
            $model['errors'] = $errors;
        }

        View::render('User/login', $model);
    }

    public function postLogin(): void
    {
        $request = new UserLoginRequest;
        $request->setUsername($_POST['username']);
        $request->setPassword($_POST['password']);

        try {
            $this->userService->login($request);
            redirect()->to('/');
        } catch (ValidationException $e) {
            session()->setFlashData('errors', $e->getMessages());
            redirect()->withInput()->to('/users/login');
        }
    }
}
