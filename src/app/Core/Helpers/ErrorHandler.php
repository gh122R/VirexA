<?php

declare(strict_types=1);
namespace App\Core\Helpers;

/**
 * Это обработчик ошибок. Он использует метод render класса View, обращаясь, по умолчанию, к ExceptionsPage.html.
 * Обработчик ошибок очень гибок, поскольку позволяет вызывать любые странички и данные, которые вы укажете.
 * $view - страница для вывода ошибка, по умолчанию рендериться ExceptionsPage. Чтобы указать страничку, достаточно указать
 * её название относительно папки Views, например '404'.
 * $message - Сообщение, которое мы хотим передать представлению (html' ке),
 * $dangerLevel - Тип ошибки (необязательно указывать),
 * $description - Описание ошибки.
 *
 * Как задавать вывод ошибок вы можете посмотреть в классе "ListOfErrors".
 * */
class ErrorHandler extends ListOfErrors
{
    public static function error(string $message, string $view = '', string $dangerLevel = 'Критическая ошибка'  ,string $description = ''): string
    {
        return View::render($view ?: "ExceptionsPage", [
            'Exception' => $message,
            'DangerLevel' => $dangerLevel,
            'Description' => $description
        ]);
    }
}