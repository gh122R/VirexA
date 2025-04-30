<?php

namespace tests;

use App\Middleware\TestMiddleware;
use PHPUnit\Framework\TestCase;
use App\Router;

class RouterTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function testRouteNotFound()
    {
        $router = new Router();
        $router->get('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/somethingElse');
        $this->assertStringContainsString('404', $result);
    }

    public function testGetRequest()
    {
        $router = new Router();
        $router->get('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/test');
        $this->assertEquals('success', $result);
    }

    public function testPostRequest()
    {
        $router = new Router();
        $router->post('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->handler('/test');
        $this->assertEquals('success', $result);
    }

    public function testPutRequest()
    {
        $router = new Router();
        $router->put('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $result = $router->handler('/test');
        $this->assertEquals('success', $result);
    }

    public function testDeleteRequest()
    {
        $router = new Router();
        $router->delete('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $result = $router->handler('/test');
        $this->assertEquals('success', $result);
    }

    public function testMixedRequestsPositive()
    {
        $router = new Router();
        $router->post('/post', function () {
            return 'post request';
        })
        ->get('/get', function () {
            return 'get request';
        });
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->handler('/post');
        $this->assertEquals('post request', $result);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/get');
        $this->assertEquals('get request', $result);
    }

    public function testMixedRequestsNegative()
    {
        $router = new Router();
        $router->post('/post', function () {
            return 'post request';
        })
            ->get('/get', function () {
                return 'get request';
            });
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->handler('/post');
        $this->assertEquals('post request', $result);
        $result = $router->handler('/get');
        $this->assertStringContainsString('Данный метод низя применить на маршруте', $result);
    }

    public function testRequestMethod()
    {
        $router = new Router();
        $router->post('/test', function () {
            return 'success';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/test');
        $this->assertStringContainsString('Данный метод низя применить на маршруте', $result);
    }

    public function testMiddlewarePositive()
    {
        $router = new Router();
        $router->get('/test', function () { return 'success';}, [[TestMiddleware::class, 'index']]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/test');
        $this->assertEquals('success', $result);
    }

    public function testMiddlewareNegative()
    {
        $router = new Router();
        $router->get('/test', function () { return 'success';}, [[TestMiddleware::class, 'forTest']]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/test');
        $this->assertNotEquals('test', $result);
        $this->assertStringContainsString('Middleware сработал!', $result);
    }

    public function testMiddlewareMixed()
    {
        $router = new Router();
        $router->get('/test', function (){
            return 'test';
        },
            [
                [TestMiddleware::class, 'index'],
                [TestMiddleware::class, 'forTest'],
            ]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->handler('/test');
        $this->assertNotEquals('test', $result);
        $this->assertStringContainsString('Middleware сработал!', $result);
    }
}