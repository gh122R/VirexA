<?php

declare(strict_types=1);
namespace App\Middleware;

use App\ErrorHandler;

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
           return ErrorHandler::error("Middleware сработал!");
        }*/
        return $next();
    }
}