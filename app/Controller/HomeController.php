<?php

namespace RezaFikkri\PLM\Controller;

use RezaFikkri\PLM\App\View;

class HomeController
{
    public function index(): void
    {
        // model response
        $response = [
            'title' => 'PHP Login Management System',
        ];
        View::render('Home/index', $response);
    }
}
