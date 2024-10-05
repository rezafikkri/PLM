<?php

namespace RezaFikkri\MVC\App;

use Exception;
use RezaFikkri\MVC\Middleware\Middleware;

class Router
{
    private static array $routes = [];

    public static function add(
        string $httpMethod,
        string $path,
        string $controller,
        string $method,
        array $middleware = [],
    ): void {
        self::$routes[] = [
            'httpMethod' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware,
        ];
    }

    public static function run(): void
    {
        $path = '/';
        if (isset($_SERVER['PATH_INFO'])) $path = $_SERVER['PATH_INFO'];
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            $pattern = "#^$route[path]$#";

            if (preg_match($pattern, $path, $variables) && $httpMethod == $route['httpMethod']) {
                foreach ($route['middleware'] as $middleware) {
                    // run middleware
                    $middlewareObj = new $middleware;
                    if (!$middlewareObj instanceof Middleware) {
                        throw new Exception("Middleware $middleware must be implement Middleware interface!");
                    }
                    $middlewareObj->before();
                }

                // run controller
                $controller = new $route['controller'];
                array_shift($variables);
                call_user_func_array([$controller, $route['method']], $variables);
                return;
            }
        }

        http_response_code(404);
        echo 'Controller Not Found';
    }
}
