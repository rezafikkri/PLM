<?php

require_once __DIR__ . '/../vendor/autoload.php';

use RezaFikkri\PLM\Middleware\AuthMiddleware;
use RezaFikkri\PLM\App\Router;
use RezaFikkri\PLM\Controller\{HomeController, ProductController};

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/product/([\d\w]+)/category/([\d\w]*)', ProductController::class, 'category', [
    AuthMiddleware::class,
]);

Router::run();
