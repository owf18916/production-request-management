<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\User as UserModel;
use App\Models\Conveyor;

/**
 * User Controller
 * Handles user management (admin) and user profile (self)
 */
class User extends Controller
{
    /**
     * List all users (Admin only)
     */
    public function index(): void
    {
        $search = $this->input('search');
        $roleFilter = $this->input('role');

        if ($search) {
            $users = UserModel::search($search);
        } else {
            $users = UserModel::getAll();
        }

        // Apply role filter
        if ($roleFilter && in_array($roleFilter, ['admin', 'pic'])) {
            $users = array_filter($users, fn($user) => $user->role === $roleFilter);
        }

        // Get users with conveyors
        $usersWithConveyors = [];
        foreach ($users as $user) {
            $user->conveyors = UserModel::getUserConveyors($user->id);
            $usersWithConveyors[] = $user;
        }

        $this->setTitle('User Management - Production Request Management');
        $this->view('admin/users/index', [
            'users' => $usersWithConveyors,
            'search' => $search,
            'roleFilter' => $roleFilter,
        ]);
    }

    /**
     * Show create user form (Admin only)
     */
    public function create(): void
    {
        $conveyors = Conveyor::getActive();

        $this->setTitle('Create User - Production Request Management');
        $this->view('admin/users/create', [
            'conveyors' => $conveyors,
        ]);
    }

    /**
     * Store new user (Admin only)
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/users/create'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/users/create'));
        }

        // Validate input
        $errors = [];
        $nik = Security::sanitize($this->input('nik'), 'string');
        $username = Security::sanitize($this->input('username'), 'string');
        $fullName = Security::sanitize($this->input('full_name'), 'string');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        $role = $this->input('role');
        $conveyorIds = $this->input('conveyors') ?? [];

        // Validate NIK
        if (empty($nik)) {
            $errors['nik'] = 'NIK is required';
        } elseif (strlen($nik) > 50) {
            $errors['nik'] = 'NIK must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $nik)) {
            $errors['nik'] = 'NIK must be alphanumeric';
        } elseif (UserModel::nikExists($nik)) {
            $errors['nik'] = 'NIK already exists';
        }

        // Validate Username
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username must be alphanumeric with underscores only';
        } elseif (UserModel::usernameExists($username)) {
            $errors['username'] = 'Username already exists';
        }

        // Validate Full Name
        if (empty($fullName)) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must not exceed 100 characters';
        }

        // Validate Password
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }

        // Validate Role
        if (empty($role) || !in_array($role, ['admin', 'pic'])) {
            $errors['role'] = 'Valid role is required';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('/admin/users/create'));
        }

        // Create user
        $userData = [
            'nik' => $nik,
            'username' => $username,
            'full_name' => $fullName,
            'password' => $password,
            'role' => $role,
        ];

        $created = UserModel::createUser($userData);

        if (!$created) {
            Session::flash('error', 'Failed to create user. Please try again.');
            $this->redirect(url('/admin/users/create'));
        }

        // Get the newly created user ID
        $newUser = UserModel::findByUsername($username);
        if ($newUser && !empty($conveyorIds)) {
            UserModel::syncConveyors($newUser->id, $conveyorIds);
        }

        Session::flash('success', 'User created successfully');
        $this->redirect(url('/admin/users'));
    }

    /**
     * Show edit user form (Admin only)
     */
    public function edit(int $id): void
    {
        $user = UserModel::getUserById($id);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/admin/users'));
        }

        $conveyors = Conveyor::getActive();
        $userConveyors = UserModel::getUserConveyors($id);
        $userConveyorIds = array_map(fn($c) => $c->id, $userConveyors);

        $this->setTitle('Edit User - Production Request Management');
        $this->view('admin/users/edit', [
            'user' => $user,
            'conveyors' => $conveyors,
            'userConveyorIds' => $userConveyorIds,
        ]);
    }

    /**
     * Update user (Admin only)
     */
    public function update(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url("/admin/users/edit/{$id}"));
        }

        $user = UserModel::getUserById($id);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/admin/users'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("/admin/users/edit/{$id}"));
        }

        // Validate input
        $errors = [];
        $nik = Security::sanitize($this->input('nik'), 'string');
        $username = Security::sanitize($this->input('username'), 'string');
        $fullName = Security::sanitize($this->input('full_name'), 'string');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        $conveyorIds = $this->input('conveyors') ?? [];

        // Validate NIK
        if (empty($nik)) {
            $errors['nik'] = 'NIK is required';
        } elseif (strlen($nik) > 50) {
            $errors['nik'] = 'NIK must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $nik)) {
            $errors['nik'] = 'NIK must be alphanumeric';
        } elseif (UserModel::nikExists($nik, $id)) {
            $errors['nik'] = 'NIK already exists';
        }

        // Validate Username
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username must be alphanumeric with underscores only';
        } elseif (UserModel::usernameExists($username, $id)) {
            $errors['username'] = 'Username already exists';
        }

        // Validate Full Name
        if (empty($fullName)) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must not exceed 100 characters';
        }

        // Validate Password (optional on update)
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            } elseif ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Passwords do not match';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url("/admin/users/edit/{$id}"));
        }

        // Update user
        $userData = [
            'nik' => $nik,
            'username' => $username,
            'full_name' => $fullName,
        ];

        if (!empty($password)) {
            $userData['password'] = $password;
        }

        $updated = UserModel::update($id, $userData);

        if (!$updated) {
            Session::flash('error', 'Failed to update user. Please try again.');
            $this->redirect(url("/admin/users/edit/{$id}"));
        }

        // Sync conveyors
        UserModel::syncConveyors($id, $conveyorIds);

        Session::flash('success', 'User updated successfully');
        $this->redirect(url('/admin/users'));
    }

    /**
     * Delete user (Admin only)
     */
    public function delete(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/users'));
        }

        $user = UserModel::getUserById($id);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/admin/users'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/users'));
        }

        // Prevent deleting own account
        if ($user->id === Session::get('user_id')) {
            Session::flash('error', 'You cannot delete your own account');
            $this->redirect(url('/admin/users'));
        }

        $deleted = UserModel::delete($id);

        if (!$deleted) {
            Session::flash('error', 'Failed to delete user. Please try again.');
            $this->redirect(url('/admin/users'));
        }

        Session::flash('success', 'User deleted successfully');
        $this->redirect(url('/admin/users'));
    }

    /**
     * Show user profile (All authenticated users)
     */
    public function profile(): void
    {
        $userId = Session::get('user_id');
        $user = UserModel::getUserById($userId);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/dashboard'));
        }

        $conveyors = UserModel::getUserConveyors($userId);

        $this->setTitle('My Profile - Production Request Management');
        $this->view('users/profile', [
            'user' => $user,
            'conveyors' => $conveyors,
        ]);
    }

    /**
     * Show edit profile form (All authenticated users)
     */
    public function editProfile(): void
    {
        $userId = Session::get('user_id');
        $user = UserModel::getUserById($userId);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/dashboard'));
        }

        $this->setTitle('Edit Profile - Production Request Management');
        $this->view('users/edit_profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile (All authenticated users)
     */
    public function updateProfile(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/edit-profile'));
        }

        $userId = Session::get('user_id');
        $user = UserModel::getUserById($userId);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/dashboard'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/edit-profile'));
        }

        // Validate input
        $errors = [];
        $fullName = Security::sanitize($this->input('full_name'), 'string');

        // Validate Full Name
        if (empty($fullName)) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name must not exceed 100 characters';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('/edit-profile'));
        }

        // Update user
        $userData = [
            'full_name' => $fullName,
        ];

        $updated = UserModel::update($userId, $userData);

        if (!$updated) {
            Session::flash('error', 'Failed to update profile. Please try again.');
            $this->redirect(url('/edit-profile'));
        }

        // Update session
        Session::put('user_full_name', $fullName);

        Session::flash('success', 'Profile updated successfully');
        $this->redirect(url('/profile'));
    }

    /**
     * Show change password form (All authenticated users)
     */
    public function changePassword(): void
    {
        $this->setTitle('Change Password - Production Request Management');
        $this->view('users/change_password');
    }

    /**
     * Update password (All authenticated users)
     */
    public function updatePassword(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/change-password'));
        }

        $userId = Session::get('user_id');
        $user = UserModel::find($userId);

        if (!$user) {
            Session::flash('error', 'User not found');
            $this->redirect(url('/dashboard'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/change-password'));
        }

        // Validate input
        $errors = [];
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $newPasswordConfirm = $this->input('new_password_confirm');

        // Validate current password
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        } elseif (!Security::verifyPassword($currentPassword, $user->password)) {
            $errors['current_password'] = 'Current password is incorrect';
        }

        // Validate new password
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters';
        } elseif ($newPassword === $currentPassword) {
            $errors['new_password'] = 'New password must be different from current password';
        } elseif ($newPassword !== $newPasswordConfirm) {
            $errors['new_password_confirm'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('/change-password'));
        }

        // Update password
        $updated = UserModel::updatePassword($userId, $newPassword);

        if (!$updated) {
            Session::flash('error', 'Failed to update password. Please try again.');
            $this->redirect(url('/change-password'));
        }

        Session::flash('success', 'Password changed successfully');
        $this->redirect(url('/profile'));
    }

    /**
     * Set page title
     */
    protected function setTitle(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }
}
