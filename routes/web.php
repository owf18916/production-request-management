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
$router->get('/dashboard/admin', 'Dashboard@adminDashboard', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/dashboard/pic', 'Dashboard@picDashboard', ['middleware' => 'Authenticate']);
$router->post('/dashboard/setup-conveyor-shift', 'Dashboard@setupConveyorShift', ['middleware' => 'Authenticate']);
$router->post('/dashboard/clear-conveyor-shift', 'Dashboard@clearConveyorShift', ['middleware' => 'Authenticate']);
$router->get('/dashboard/get-active-conveyor-shift', 'Dashboard@getActiveConveyorShift', ['middleware' => 'Authenticate']);
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

// Master Checksheet routes - WITH admin middleware
$router->get('/admin/master/checksheet', 'MasterChecksheet@index', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/checksheet/create', 'MasterChecksheet@create', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/checksheet/store', 'MasterChecksheet@store', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/checksheet/edit/{id}', 'MasterChecksheet@edit', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/checksheet/update/{id}', 'MasterChecksheet@update', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/master/checksheet/delete/{id}', 'MasterChecksheet@delete', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/master/checksheet/search', 'MasterChecksheet@search', ['middleware' => ['Authenticate', 'Admin']]);

// Request ATK routes (PIC) - WITH authentication middleware - MUST BE BEFORE /requests/atk/{id}
$router->get('/requests/atk', 'RequestATK@index', ['middleware' => 'Authenticate']);
$router->get('/requests/atk/create', 'RequestATK@create', ['middleware' => 'Authenticate']);
$router->post('/requests/atk/store', 'RequestATK@store', ['middleware' => 'Authenticate']);
$router->get('/requests/atk/show/{id}', 'RequestATK@show', ['middleware' => 'Authenticate']);
$router->post('/requests/atk/cancel/{id}', 'RequestATK@cancel', ['middleware' => 'Authenticate']);
$router->get('/requests/atk/export', 'RequestATK@export', ['middleware' => 'Authenticate']);

// Request ATK routes (Admin) - WITH admin middleware
$router->get('/admin/requests/atk', 'RequestATK@adminIndex', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/requests/atk/{id}', 'RequestATK@adminShow', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/requests/atk/{id}/update-status', 'RequestATK@updateStatus', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/requests/atk/export', 'RequestATK@export', ['middleware' => ['Authenticate', 'Admin']]);

// Request ID routes (PIC) - WITH authentication middleware - MUST BE BEFORE /request-id/{id}
$router->get('/request-id', 'RequestID@index', ['middleware' => 'Authenticate']);
$router->get('/request-id/create', 'RequestID@create', ['middleware' => 'Authenticate']);
$router->post('/request-id/store', 'RequestID@store', ['middleware' => 'Authenticate']);
$router->get('/request-id/export', 'RequestID@export', ['middleware' => 'Authenticate']);
$router->get('/request-id/{id}', 'RequestID@show', ['middleware' => 'Authenticate']);
$router->post('/request-id/cancel/{id}', 'RequestID@cancel', ['middleware' => 'Authenticate']);

// Request Checksheet routes (PIC) - WITH authentication middleware - MUST BE BEFORE /request_checksheet/{id}
$router->get('/request_checksheet', 'RequestChecksheet@index', ['middleware' => 'Authenticate']);
$router->get('/request_checksheet/create', 'RequestChecksheet@create', ['middleware' => 'Authenticate']);
$router->post('/request_checksheet/store', 'RequestChecksheet@store', ['middleware' => 'Authenticate']);
$router->get('/request_checksheet/show/{id}', 'RequestChecksheet@show', ['middleware' => 'Authenticate']);
$router->post('/request_checksheet/cancel/{id}', 'RequestChecksheet@cancel', ['middleware' => 'Authenticate']);
$router->get('/request_checksheet/export', 'RequestChecksheet@export', ['middleware' => 'Authenticate']);
$router->get('/request_checksheet/search-checksheet', 'RequestChecksheet@searchChecksheet', ['middleware' => 'Authenticate']);

// Request Checksheet routes (Admin) - WITH admin middleware
$router->get('/admin/request_checksheet', 'RequestChecksheet@adminIndex', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/request_checksheet/show/{id}', 'RequestChecksheet@adminShow', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/request_checksheet/update_status/{id}', 'RequestChecksheet@updateStatus', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/request_checksheet/export', 'RequestChecksheet@export', ['middleware' => ['Authenticate', 'Admin']]);

// Request ID routes (Admin) - WITH admin middleware
$router->get('/admin/request-id', 'RequestID@adminIndex', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/request-id/export', 'RequestID@export', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/request-id/{id}', 'RequestID@adminShow', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/request-id/{id}/update-status', 'RequestID@updateStatus', ['middleware' => ['Authenticate', 'Admin']]);

// Request Memo routes (PIC) - WITH authentication middleware - MUST BE BEFORE /requests/memo/{id}
$router->get('/requests/memo', 'RequestMemo@index', ['middleware' => 'Authenticate']);
$router->get('/requests/memo/create', 'RequestMemo@create', ['middleware' => 'Authenticate']);
$router->post('/requests/memo/store', 'RequestMemo@store', ['middleware' => 'Authenticate']);
$router->get('/requests/memo/show/{id}', 'RequestMemo@show', ['middleware' => 'Authenticate']);
$router->post('/requests/memo/cancel/{id}', 'RequestMemo@cancel', ['middleware' => 'Authenticate']);

// Request Memo routes (Admin) - WITH admin middleware
$router->get('/admin/requests/memo', 'RequestMemo@adminIndex', ['middleware' => ['Authenticate', 'Admin']]);
$router->get('/admin/requests/memo/show/{id}', 'RequestMemo@adminShow', ['middleware' => ['Authenticate', 'Admin']]);
$router->post('/admin/requests/memo/update-status/{id}', 'RequestMemo@updateStatus', ['middleware' => ['Authenticate', 'Admin']]);

// API routes (if needed) - WITH authentication middleware
$router->get('/api/requests', 'Api\Request@index', ['middleware' => 'Authenticate']);
$router->post('/api/requests', 'Api\Request@store', ['middleware' => 'Authenticate']);
$router->get('/api/atk/search', 'Api\Request@searchATK', ['middleware' => 'Authenticate']);
$router->get('/api/checksheet/search', 'Api\Request@searchChecksheet', ['middleware' => 'Authenticate']);
$router->get('/api/id-types/search', 'Api\Request@searchIDTypes', ['middleware' => 'Authenticate']);
