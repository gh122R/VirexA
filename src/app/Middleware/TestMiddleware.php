<?php

declare(strict_types=1);
namespace App\Middleware;

use App\ErrorHandler;

class TestMiddleware
{

    /**
     * Вы можете проверить работоспособность этого middleware' а, раскомментировав код ниже, и убрав последний "return $next();"
     * */
    public function index(callable|string $next):callable|string
    {
/*        if (!empty($_COOKIE['token']))
        {
            return $next();
        }else
        {
           return ErrorHandler::error("Middleware сработал!");
        }*/
        return $next();
    }

    public function empty(callable|string $next):callable|string
    {
        return $next();
    }

    public function forTest(callable $next):callable|string
    {
        if (!empty($_COOKIE['token']))
        {
            return $next();
        }else
        {
           return ErrorHandler::error("Middleware сработал!");
        }
    }
}