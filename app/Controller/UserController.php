<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;
// use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Exception\ValidationException;
use RezaFikkri\PLM\Model\UserLoginRequest;
use RezaFikkri\PLM\Model\UserPasswordUpdateRequest;
use RezaFikkri\PLM\Model\UserProfileUpdateRequest;
use RezaFikkri\PLM\Model\UserRegisterRequest;
// use RezaFikkri\PLM\Repository\SessionRepository;
// use RezaFikkri\PLM\Repository\UserRepository;
use RezaFikkri\PLM\Service\SessionService;
use RezaFikkri\PLM\Service\UserService;

class UserController
{

    public function __construct(
        private UserService $userService,
        private SessionService $sessionService,
    ) {
        // $userRepository = new UserRepository(Database::getConnection());
        // $this->userService = new UserService($userRepository);
        // $sessionRepository = new SessionRepository(Database::getConnection());
        // $this->sessionService = new SessionService($sessionRepository, $userRepository);
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

    public function logout(): void
    {
        $this->sessionService->destroy();
        redirect()->to('/');
    }

    public function updateProfile(): void
    {
        $model = [
            'title' => 'Update Profile',
            'user' => $this->sessionService->current(),
        ];

        $flash = flash();
        if ($errors = $flash->getData('errors')) {
            $model['errors'] = $errors;
        }
        if ($success = $flash->getData('success')) {
            $model['success'] = $success;
        }

        View::render('User/profile', $model);
    }

    public function postUpdateProfile(): void
    {
        $request = new UserProfileUpdateRequest;
        $request->setId($this->sessionService->current()->getId());
        $request->setUsername($_POST['username']);

        try {
            $this->userService->updateProfile($request);
            flash()->setData('success', 'Profile updated.');
            redirect()->to('/users/profile');
        } catch (ValidationException $e) {
            flash()->setData('errors', $e->getMessages());
            redirect()->withInput()->to('/users/profile');
        }
    }

    public function updatePassword(): void
    {
        $model = [
            'title' => 'Update Password',
        ];

        $flash = flash();
        if ($errors = $flash->getData('errors')) {
            $model['errors'] = $errors;
        }
        if ($success = $flash->getData('success')) {
            $model['success'] = $success;
        }

        View::render('User/password', $model);
    }

    public function postUpdatePassword(): void
    {
        $request = new UserPasswordUpdateRequest;
        $request->setId($this->sessionService->current()->getId());
        $request->setOldPassword($_POST['oldPassword']);
        $request->setNewPassword($_POST['newPassword']);

        try {
            $this->userService->updatePassword($request);
            flash()->setData('success', 'Password updated.');
            redirect()->to('/users/password');
        } catch (ValidationException $e) {
            flash()->setData('errors', $e->getMessages());
            redirect()->to('/users/password');
        }
    }
}
