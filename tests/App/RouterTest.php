<?php

namespace RezaFikkri\PLM\App;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Controller\UserController;
use RezaFikkri\PLM\Middleware\MustLoginMiddleware;
use RezaFikkri\PLM\Middleware\MustNotLoginMiddleware;
use TypeError;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        Router::clearRoutes();
    }

    #[Test]
    public function addWithoutMiddleware(): void
    {
        Router::add('post', '/users/login', [ UserController::class, 'login' ]);

        $this->assertEquals([
            'httpMethod' => 'post',
            'path' => '/users/login',
            'controller' => [ UserController::class, 'login' ],
            'middlewares' => [],
        ], Router::getRoutes()[0]);
    }

    #[Test]
    public function addWithMiddleware(): void
    {
        Router::add('get', '/users/profile', [ UserController::class, 'profile' ], [
            MustLoginMiddleware::class,
        ]);

        $this->assertEquals([
            'httpMethod' => 'get',
            'path' => '/users/profile',
            'controller' => [ UserController::class, 'profile' ],
            'middlewares' => [ MustLoginMiddleware::class ],
        ], Router::getRoutes()[0]);
    }

    #[Test]
    public function groupWithMiddleware(): void
    {
        Router::group('users', [ MustNotLoginMiddleware::class ], function () {
            Router::add('post', '/profile/edit', [ UserController::class, 'editProfile' ]);
            Router::add('post', '/profile/delete', [ UserController::class, 'deleteProfile' ]);
        });

        $this->assertEquals([
            [
                'httpMethod' => 'post',
                'path' => '/users/profile/edit',
                'controller' => [ UserController::class, 'editProfile' ],
                'middlewares' => [ MustNotLoginMiddleware::class ],
            ],
            [
                'httpMethod' => 'post',
                'path' => '/users/profile/delete',
                'controller' => [ UserController::class, 'deleteProfile' ],
                'middlewares' => [ MustNotLoginMiddleware::class ],
            ],
        ], Router::getRoutes());
    }

    #[Test]
    public function groupWithoutMiddleware(): void
    {
        Router::group('/users/', callback: function () {
            Router::add('post', '/password/edit', [ UserController::class, 'editPassword' ]);
            Router::add('post', '/delete', [ UserController::class, 'deleteUser' ]);
        });

        $this->assertEquals([
            [
                'httpMethod' => 'post',
                'path' => '/users/password/edit',
                'controller' => [ UserController::class, 'editPassword' ],
                'middlewares' => [],
            ],
            [
                'httpMethod' => 'post',
                'path' => '/users/delete',
                'controller' => [ UserController::class, 'deleteUser' ],
                'middlewares' => [],
            ],
        ], Router::getRoutes());
    }

    #[Test]
    public function groupTypeError(): void
    {
        $this->expectException(TypeError::class);

        Router::group('/users/', callback: null);
    }
}
