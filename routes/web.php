<?php

/**
 * Application Routes
 * Define all application routes here
 */

// Home routes
$router->get('/', 'Home@index');

// Auth routes - NO middleware
$router->get('/login', 'Auth@showLoginForm');
$router->post('/login', 'Auth@login');
$router->get('/logout', 'Auth@logout');

// Dashboard routes - WITH authentication middleware
$router->get('/dashboard', 'Dashboard@index', ['middleware' => 'Authenticate']);

// Production Requests routes - WITH authentication middleware
$router->get('/requests', 'Request@index', ['middleware' => 'Authenticate']);
$router->get('/requests/create', 'Request@create', ['middleware' => 'Authenticate']);
$router->post('/requests', 'Request@store', ['middleware' => 'Authenticate']);
$router->get('/requests/{id}', 'Request@show', ['middleware' => 'Authenticate']);
$router->get('/requests/{id}/edit', 'Request@edit', ['middleware' => 'Authenticate']);
$router->post('/requests/{id}', 'Request@update', ['middleware' => 'Authenticate']);
$router->delete('/requests/{id}', 'Request@delete', ['middleware' => 'Authenticate']);

// Admin routes - WITH admin middleware
$router->get('/dashboard/admin', 'Dashboard@adminDashboard', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/conveyors', 'Admin@conveyors', ['middleware' => ['Authenticate', 'Admin']]);

// User Management routes - WITH admin middleware
$router->get('/admin/users', 'User@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/users/create', 'User@create', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/users/store', 'User@store', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/users/edit/{id}', 'User@edit', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/users/update/{id}', 'User@update', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/users/delete/{id}', 'User@delete', ['middleware' => ['Authenticate', 'Admin']]);

// User Profile routes - WITH authentication middleware
$router->get('/profile', 'User@profile', ['middleware' => 'Authenticate']);
$router->get('/edit-profile', 'User@editProfile', ['middleware' => 'Authenticate']);
$router->post('/update-profile', 'User@updateProfile', ['middleware' => 'Authenticate']);
$router->get('/change-password', 'User@changePassword', ['middleware' => 'Authenticate']);
$router->post('/update-password', 'User@updatePassword', ['middleware' => 'Authenticate']);

// Master Conveyor routes - WITH admin middleware
$router->get('/admin/master/conveyor', 'MasterConveyor@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/conveyor/create', 'MasterConveyor@create', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/store', 'MasterConveyor@store', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/conveyor/edit/{id}', 'MasterConveyor@edit', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/update/{id}', 'MasterConveyor@update', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/delete/{id}', 'MasterConveyor@delete', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/conveyor/manage-users/{id}', 'MasterConveyor@manageUsers', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/assign-users/{id}', 'MasterConveyor@assignUsers', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/remove-user/{conveyorId}/{userId}', 'MasterConveyor@removeUser', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/conveyor/toggle-status/{id}', 'MasterConveyor@toggleStatus', ['middleware' => ['Authenticate', 'Admin']]);

// Master ATK routes - WITH admin middleware
$router->get('/admin/master/atk', 'MasterATK@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/atk/create', 'MasterATK@create', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/atk/store', 'MasterATK@store', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/atk/edit/{id}', 'MasterATK@edit', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/atk/update/{id}', 'MasterATK@update', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/atk/delete/{id}', 'MasterATK@delete', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/atk/search', 'MasterATK@search', ['middleware' => ['Authenticate', 'Admin']]);

// API routes (if needed) - WITH authentication middleware
$router->get('/api/requests', 'Api\Request@index', ['middleware' => 'Authenticate']);
$router->post('/api/requests', 'Api\Request@store', ['middleware' => 'Authenticate']);
