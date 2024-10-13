<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use RezaFikkri\PLM\App\Router;
use RezaFikkri\PLM\Controller\{HomeController, UserController};

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Home Controller
Router::add('GET', '/', HomeController::class, 'index');

// User Controller
Router::add('GET', '/register', UserController::class, 'register');
Router::add('POST', '/register', UserController::class, 'postRegister');

Router::run();
