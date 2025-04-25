<?php

namespace App\Controllers;

use App\View;

class HomeController
{
    /**
     * Метод index просто возвращает страничку.
    */
    public function index()
    {
        return View::render('home');
    }
}
