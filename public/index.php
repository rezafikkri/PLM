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
Router::add('GET', '/users/register', UserController::class, 'register');
Router::add('POST', '/users/register', UserController::class, 'postRegister');
Router::add('GET', '/users/login', UserController::class, 'login');
Router::add('POST', '/users/login', UserController::class, 'postLogin');
Router::add('GET', '/users/logout', UserController::class, 'logout');

Router::run();

// clear session (flash, form)
flash()->clear();
