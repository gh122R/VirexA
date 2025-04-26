<?php

declare(strict_types=1);
namespace App;
/**
 * Это обработчик ошибок. Он использует метод render класса View, обращаясь к ExceptionsPage.html.
 * Поскольку метод render принимает массив данных, то тут в качестве ключа массива использую "Exception",
 * а в качестве значения $message - ошибку, которую хотим вывести.
 * */
class ErrorHandler
{
    public static function error(string $message): string
    {
        return View::render('ExceptionsPage', [
            'Exception' => $message
        ]);
    }
}