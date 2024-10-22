<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
use RezaFikkri\PLM\Model\UserRegisterRequest;
use RezaFikkri\PLM\Repository\SessionRepository;
use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Service\SessionService;
use RezaFikkri\PLM\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $dbc = Database::getConnection();
        $userRepository = new UserRepository($dbc);
        $this->userService = new UserService($userRepository);
        $sessionRepository = new SessionRepository($dbc);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register(): void
    {
        $model = [
            'title' => 'Register User',
        ];

        $flash = flash();
        if ($errors = $flash->getData('errors')) {
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
            flash()->setData('errors', $e->getMessages());
            redirect()->withInput()->to('/users/register');
        }
    }

    public function login(): void
    {
        $model = [
            'title' => 'Login User',
        ];

        $flash = flash();
        if ($errors = $flash->getData('errors')) {
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
            $response = $this->userService->login($request);
            $this->sessionService->create($response->getUser()->getId());
            redirect()->to('/');
        } catch (ValidationException $e) {
            flash()->setData('errors', $e->getMessages());
            redirect()->withInput()->to('/users/login');
        }
    }
}
