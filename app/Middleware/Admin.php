<?php

namespace App\Middleware;

use App\Session;

/**
 * Admin Middleware
 * Checks if user has admin role
 */
class Admin
{
    /**
     * Handle the middleware
     */
    public static function handle(): bool
    {
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'You do not have permission to access this page');
            return false;
        }

        return true;
    }
}
