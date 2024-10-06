<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use RezaFikkri\PLM\Middleware\AuthMiddleware;
use RezaFikkri\PLM\App\Router;
use RezaFikkri\PLM\Controller\{HomeController, ProductController};

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/product/([\d\w]+)/category/([\d\w]*)', ProductController::class, 'category', [
    AuthMiddleware::class,
]);

Router::run();
