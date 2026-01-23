<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\User;

/**
 * Auth Controller
 * Handles authentication operations
 */
class Auth extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(): void
    {
        if (Session::has('user_id')) {
            $this->redirect(url('dashboard'));
        }

        $this->setTitle('Login - Production Request Management');
        $this->view('auth/login', [], false);
    }

    /**
     * Handle login
     */
    public function login(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('login'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        
        if (!$csrfToken) {
            Session::flash('error', 'Security token missing. Please try again.');
            $this->redirect(url('login'));
        }
        
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('login'));
        }

        // Validate input
        $errors = $this->validate([
            'identifier' => 'required|min:2',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('login'));
        }

        $identifier = Security::sanitize($this->input('identifier'), 'string');
        $password = $this->input('password');
        $rememberMe = $this->input('remember_me') === 'on';

        // Attempt authentication
        $user = User::authenticate($identifier, $password);

        if (!$user) {
            Session::flash('error', 'Invalid username/NIK or password');
            $this->redirect(url('login'));
        }

        // Regenerate session for security
        session_regenerate_id(true);

        // Store user data in session
        Session::put('user_id', $user->id);
        Session::put('user_nik', $user->nik);
        Session::put('user_username', $user->username);
        Session::put('user_full_name', $user->full_name);
        Session::put('user_role', $user->role);

        // Remember me functionality
        if ($rememberMe) {
            Security::setCookie('remember_user_id', (string)$user->id, 30);
        }

        // Redirect based on role
        $redirectUrl = $user->role === 'admin' ? url('dashboard/admin') : url('dashboard');

        Session::flash('success', 'Welcome back, ' . $user->full_name);
        $this->redirect($redirectUrl);
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        $userId = Session::get('user_id');
        
        // Clear session
        Session::forget('user_id');
        Session::forget('user_nik');
        Session::forget('user_username');
        Session::forget('user_full_name');
        Session::forget('user_role');

        // Clear remember me cookie
        Security::setCookie('remember_user_id', '', -1);

        Session::flash('success', 'You have been logged out successfully');
        $this->redirect(url('login'));
    }
}
