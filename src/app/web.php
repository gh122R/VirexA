<?php

use App\Controllers\HomeController;
use App\Middleware\TestMiddleware;
use App\Router;

/**
 * Чтобы создать маршрут, достаточно указать путь и массив, состоящий из контроллера и его метода.
 * Так же можно указать middleware, который может быть, как callback' ом, так и массивом из middleware' ов.
 * Middleware обязательно должен быть передан в массиве, можно указать несколько middleware' ов.
 * Кстати, middleware объявляем также, как и контроллер, но ещё можем передать 3-й параметр, который будет
 * передан в конструктор middleware' а. Его необязательно указывать.
 * Ниже есть примеры объявления маршрутов.
 * */

Router::get('/home', [HomeController::class, 'index'],
    [
        [TestMiddleware::class, 'index', [
            'Hi!' => 'Hello World!'
        ]],
        /**
         * Вы можете указать ещё сколько угодно middleware'ов.
         * Но учитывайте, что middleware' ы вызывают в обратном порядке, например вы задали:
         * A, B, С, то по факту они будут вызваны так: C, B, A.
        */
    ]
);

Router::get('/test', [HomeController::class, 'index'],
    function ($next)
    {
        return $next();
    }
);