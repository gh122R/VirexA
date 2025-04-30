<?php

namespace App\Controllers;

use App\Helpers\View;

class HomeController
{
    /**
     * Метод index просто возвращает страничку.
    */
    public function index():string
    {
        return View::render('home');
    }
}
