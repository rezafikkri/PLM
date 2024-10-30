<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use RezaFikkri\PLM\App\Router;
use RezaFikkri\PLM\Controller\{HomeController, UserController};
use RezaFikkri\PLM\Middleware\MustLoginMiddleware;
use RezaFikkri\PLM\Middleware\MustNotLoginMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Home Controller
Router::add('GET', '/', HomeController::class, 'index');

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [ MustNotLoginMiddleware::class ]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [ MustNotLoginMiddleware::class ]);
Router::add('GET', '/users/login', UserController::class, 'login', [ MustNotLoginMiddleware::class ]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [ MustNotLoginMiddleware::class ]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [ MustLoginMiddleware::class ]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [
    MustLoginMiddleware::class,
]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [
    MustLoginMiddleware::class,
]);


Router::run();

// clear session (flash, form)
flash()->clear();
