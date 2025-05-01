<?php

namespace App\Core\Helpers;

class View
{
    /**
     * Метод render позволяет отрисовать html-страничку и передать в неё какие-либо данные.
     * В render передаём путь до html-странички относительно папки Views и массив, который в последующем
     * разбивается на переменные по ключам, позволяя удобнее взаимодействовать с ними в самой html' ке.
     * Если файл не найден, то скрипт прекращает работу, а router выкидывает страницу ExceptionsPage.html с ошибкой.
    */
    public static function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        $path =  dirname(__DIR__, 4) . '/src/Views/' . $view . '.html';

        if (file_exists($path))
        {
            include $path;
        }elseif (file_exists(dirname(__DIR__, 4) . '/src/Views/' . $view . '.php'))
        {
            include dirname(__DIR__, 4) . '/src/Views/' . $view . '.html';
        }else
        {
            return ErrorHandler::error("Файл представления не найден :(", description: "$view.html\(php) не найден");
        }
        return ob_get_clean();
    }
}