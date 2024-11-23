<?php

namespace RezaFikkri\PLM\App;

use Closure;
use DI\Container;
use Exception;
use PDO;
use RezaFikkri\PLM\Config\Database;
use RezaFikkri\PLM\Middleware\Middleware;
use TypeError;

class Router
{
    // for manage group
    private static string $prefix = '';
    private static array $middlewares = [];

    private static array $routes = [];

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public static function add(
        string $httpMethod,
        string $path,
        array $controller,
        array $middlewares = [],
    ): void {
        self::$routes[] = [
            'httpMethod' => $httpMethod,
            'path' => preg_replace('#/+#', '/', '/' . self::$prefix . $path),
            'controller' => $controller,
            'middlewares' => [ ...$middlewares, ...self::$middlewares ],
        ];
    }

    public static function group(
        string $prefix,
        array $middlewares = [],
        ?Closure $callback = null,
    ): void {
        if (!($callback instanceof Closure)) {
            throw new TypeError('Argument #3 ($callback) must be type of Closure or Anonymous Function, ');
        }

        self::$prefix = $prefix;
        self::$middlewares = $middlewares;

        call_user_func($callback);
        self::endGroup();
    }

    private static function endGroup(): void
    {
        self::$prefix = '';
        self::$middlewares = [];
    }

    public static function clearRoutes(): void
    {
        self::$routes = [];
    }

    public static function run(): void
    {
        $path = '/';
        if (isset($_SERVER['PATH_INFO'])) $path = $_SERVER['PATH_INFO'];
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $container = new Container([
            PDO::class => Database::getConnection(),
        ]);

        foreach (self::$routes as $route) {
            $pattern = "#^$route[path]$#";

            if (preg_match($pattern, $path, $variables) && $httpMethod == $route['httpMethod']) {
                foreach ($route['middlewares'] as $middleware) {
                    // run middleware
                    $middlewareObj = $container->get($middleware);
                    if (!$middlewareObj instanceof Middleware) {
                        throw new Exception("Middleware $middleware must be implement Middleware interface!");
                    }
                    $middlewareObj->before();
                }

                // run controller
                array_shift($variables);
                $container->call($route['controller'], $variables);
                return;
            }
        }

        http_response_code(404);
        echo 'Controller Not Found';
    }
}
