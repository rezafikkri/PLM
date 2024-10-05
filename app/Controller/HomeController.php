<?php

namespace RezaFikkri\MVC\Controller;

use RezaFikkri\MVC\App\View;

class HomeController
{
    public function index(): void
    {
        // model response
        $response = [
            'title' => 'Belajar PHP MVC',
            'content' => 'Selamat Belajar PHP MVC',
        ];
        View::render('Home/index', $response);
    }

    public function hello(): void
    {
        echo 'HomeController->hello()';
    }

    public function login(): void
    {
        // model request
        $request = [
            'username' => htmlspecialchars($_POST['username']),
            'password' => htmlspecialchars($_POST['password']),
        ];

        $response = [
            'message' => 'Login success',
        ];
    }
}
