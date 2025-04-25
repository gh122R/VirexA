<?php

namespace App;

class View
{
    /**
     * Метод render позволяет отрисовать html-страничку и передать в неё какие-либо данные.
     * В render передаём путь до html-странички относительно папки Views и массив, который в последующем
     * разбивается на переменные по ключам, позволяя удобнее взаимодействовать с ними в самой html' ке.
     * Если файл не найден, то скрипт прекращает работу, а router выкидывает страницу ExceptionsPage.html с ошибкой.
    */
    public static function render(string $view, array $data = [])
    {
        extract($data);
        ob_start();
        $path =  __DIR__ . '/../Views/' . $view . '.html';
        if (file_exists($path)) {
            include $path;
        }else{
            exit();
        }
        return ob_get_clean();
    }
}