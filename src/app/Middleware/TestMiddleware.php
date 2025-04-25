<?php

declare(strict_types=1);
namespace App\Middleware;

class TestMiddleware
{
    /**
     * Вы можете проверить работоспособность этого middleware' а, раскомментировав код ниже, и убрав последний "return $next();"
     * */
    public function index(callable $next)
    {
/*        if (!empty($_COOKIE['token']))
        {
            return $next();
        }else
        {
            return ErrorHandler::error("Test Middleware успешно выполнен!");
        }*/
        return $next();
    }
}