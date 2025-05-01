<?php

namespace App\Core\Helpers;

class ListOfErrors
{
    public static function routeNotFound($route): string
    {
        return ErrorHandler::error("Маршрута $route нет! 🤮", '404', '404');
    }

    public static function controllerNotFound($controller): string
    {
        return ErrorHandler::error("Контроллер $controller не найден!", description:  "Возможно контроллер не существует, либо его название или путь указаны неверно");
    }

    public static function methodNotFound($method, $controller): string
    {
        return ErrorHandler::error("Метод $method не найден в классе $controller", description: "Возможно метод или путь/название класса указаны неверно");
    }

    public static function brokenClassInstance($class): string
    {
        return ErrorHandler::error("Ошибка при создании экземпляра контроллера $class ");
    }

    public static function classNotFound($class): string
    {
        return ErrorHandler::error("Класс $class не найден!", description: "Возможно путь или имя класса указаны неверно 😵");
    }

    public static function failedRequestMethod($route, $controllerMethod, $pageMethod): string
    {
        return ErrorHandler::error("Эта страничка отправила $pageMethod, вместо $controllerMethod!",
            description: "Данный метод низя применить на маршруте: $route, поскольку запрос страницы $pageMethod не совпадает с методом маршрута $controllerMethod 😮");
    }

    public static function failedHandlerResponse(): string
    {
        return ErrorHandler::error('Ошибка маршрутизации', description: "Произошла ошибка при вызове замыкания 0_0. Виновник - handler роутера! Возможная причина - var_dump() 🧐");
    }
}