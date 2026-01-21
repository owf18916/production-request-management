<?php

namespace App\Controllers;

use App\Controller;

/**
 * Home Controller
 */
class Home extends Controller
{
    /**
     * Show home page
     */
    public function index(): void
    {
        $this->setTitle('Welcome');
        $this->view('home/index');
    }
}
