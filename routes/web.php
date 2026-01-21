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
$router->get('/admin/users', 'Admin@users', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/conveyors', 'Admin@conveyors', ['middleware' => ['Authenticate', 'Admin']]);

// API routes (if needed) - WITH authentication middleware
$router->get('/api/requests', 'Api\Request@index', ['middleware' => 'Authenticate']);
$router->post('/api/requests', 'Api\Request@store', ['middleware' => 'Authenticate']);
