<?php

use Andi\ProjectWebPeche\Config\View;

class HomeController {

    public function index(): void {

        $view = new View();
        $view->render('home');
    }
}