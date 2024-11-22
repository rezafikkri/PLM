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
Router::add('GET', '/', [HomeController::class, 'index']);

// User Controller
Router::group('users', [ MustNotLoginMiddleware::class ], function () {
    Router::add('GET', '/register', [ UserController::class, 'register' ]);
    Router::add('POST', '/register', [ UserController::class, 'postRegister' ]);
    Router::add('GET', '/login', [ UserController::class, 'login' ]);
    Router::add('POST', '/login', [ UserController::class, 'postLogin' ]);
});

Router::group('users', [ MustLoginMiddleware::class ], function () {
    Router::add('GET', '/logout', [ UserController::class, 'logout' ]);
    Router::add('GET', '/profile', [ UserController::class, 'updateProfile' ]);
    Router::add('POST', '/profile', [ UserController::class, 'postUpdateProfile' ]);
    Router::add('GET', '/password', [ UserController::class, 'updatePassword' ]);
    Router::add('POST', '/password', [ UserController::class, 'postUpdatePassword' ]);
});

Router::run();

// clear session (flash, form)
flash()->clear();
