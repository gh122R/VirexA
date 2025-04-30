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
 * ВАЖНО! Учтите, что middleware' ы обрабатываются в обратном порядке и в конце вызывают контроллер.
 * Например, вы задали Controller, Middleware1, Middleware2, их вызов произойдет так:
 * Middleware2 -> Middleware1 -> Controller :D
 * */
$router = new Router();

$router->get('/', [HomeController::class, 'index'])
       ->get('/test', [HomeController::class, 'index'], [
           [TestMiddleware::class, 'forTest']
       ])
       ->get('/test2', [HomeController::class, 'index'], [
           [TestMiddleware::class, 'index'],
           [TestMiddleware::class, 'empty']
       ]);