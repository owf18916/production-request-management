<?php

namespace App\Middleware;

use App\Session;

/**
 * Authenticate Middleware
 * Checks if user is authenticated
 */
class Authenticate
{
    /**
     * Handle the middleware
     */
    public static function handle(): bool
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login first');
            return false;
        }

        return true;
    }
}
