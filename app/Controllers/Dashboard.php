<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;

/**
 * Dashboard Controller
 */
class Dashboard extends Controller
{
    /**
     * Show dashboard
     */
    public function index(): void
    {
        // Check if user is logged in
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $this->setTitle('Dashboard');
        $this->with('user_name', Session::get('user_name'))
             ->with('user_role', Session::get('user_role'));

        $this->view('dashboard/index');
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard(): void
    {
        // Check if user is logged in
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // Check if user is admin
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'You do not have permission to access this page');
            $this->redirect(url('dashboard'));
        }

        $this->setTitle('Admin Dashboard');
        $this->with('user_name', Session::get('user_name'))
             ->with('user_role', Session::get('user_role'));

        $this->view('dashboard/admin');
    }
}
