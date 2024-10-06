<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use RezaFikkri\PLM\App\Router;
use RezaFikkri\PLM\Controller\{HomeController};

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Router::add('GET', '/', HomeController::class, 'index');

Router::run();
