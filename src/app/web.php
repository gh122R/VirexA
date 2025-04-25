<?php

use App\Controllers\HomeController;
use App\Middleware\TestMiddleware;
use App\Router;

/**
 * Чтобы создать маршрут, достаточно указать путь и массив, состоящий из контроллера и его метода.
 * Так же можно указать middleware, который может быть, как callback' ом, так и массивом из middleware' ов.
 * Middleware обязательно должен быть передан в массиве, можно указать несколько middleware' ов.
 * Ниже есть примеры объявления маршрутов.
 * */

Router::get('/home', [HomeController::class, 'index'],
    [
        [TestMiddleware::class, 'index', 'Hello World!']
    ]
);

Router::get('/test', [HomeController::class, 'index'],
    function ($next)
    {
        return $next();
    }
);