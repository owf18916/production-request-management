<?php

namespace App\Middleware;

use App\Session;

/**
 * PIC Middleware
 * Checks if user has PIC role
 */
class Pic
{
    /**
     * Handle the middleware
     */
    public static function handle(): bool
    {
        if (Session::get('user_role') !== 'pic') {
            Session::flash('error', 'You do not have permission to access this page');
            return false;
        }

        return true;
    }
}
