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
        Router::get('/test', function () {
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/somethingElse');
        $this->assertStringContainsString('Путь /somethingElse не найден', $result);
    }

    public function testGetRequest()
    {
        Router::get('/test', function (){
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/test');
        $this->assertEquals('test', $result);
    }

    public function testPostRequest()
    {
        Router::post('/test', function (){
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = Router::handler('/test');
        $this->assertEquals('test', $result);
    }

    public function testPutRequest()
    {
        Router::put('/test', function (){
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $result = Router::handler('/test');
        $this->assertEquals('test', $result);
    }

    public function testDeleteRequest()
    {
        Router::delete('/test', function (){
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $result = Router::handler('/test');
        $this->assertEquals('test', $result);
    }

    public function testRequestMethod()
    {
        Router::post('/test', function (){
            return 'test';
        });
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/test');
        $this->assertStringContainsString('Данный метод низя применить на маршруте', $result);
    }

    public function testMiddlewarePositive()
    {
        Router::get('/test', function (){
            return 'test';
        },
        [
            [TestMiddleware::class, 'index'],
        ]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/test');
        $this->assertEquals('test', $result);
    }

    public function testMiddlewareNegative()
    {
        Router::get('/test', function (){
            return 'test';
        },
        [
            [TestMiddleware::class, 'forTest'],
        ]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/test');
        $this->assertNotEquals('test', $result);
        $this->assertStringContainsString('Middleware сработал!', $result);
    }

    public function testMiddlewareMixed()
    {
        Router::get('/test', function (){
            return 'test';
        },
            [
                [TestMiddleware::class, 'index'],
                [TestMiddleware::class, 'forTest'],
            ]);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = Router::handler('/test');
        $this->assertNotEquals('test', $result);
        $this->assertStringContainsString('Middleware сработал!', $result);
    }
}