<?php

namespace App\Controllers;

use App\Controller;
use App\Session;

/**
 * Request Controller
 */
class Request extends Controller
{
    /**
     * Show all requests
     */
    public function index(): void
    {
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $this->setTitle('Production Requests');
        $this->view('requests/index');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $this->setTitle('Create Request');
        $this->view('requests/create');
    }

    /**
     * Store request
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('requests/create'));
        }

        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'CSRF token validation failed');
            $this->redirect(url('requests/create'));
        }

        // TODO: Implement request creation
        Session::flash('success', 'Request created successfully');
        $this->redirect(url('requests'));
    }

    /**
     * Show request details
     */
    public function show(int $id): void
    {
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $this->setTitle('Request Details');
        $this->with('request_id', $id);
        $this->view('requests/show');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $this->setTitle('Edit Request');
        $this->with('request_id', $id);
        $this->view('requests/edit');
    }

    /**
     * Update request
     */
    public function update(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url("requests/$id/edit"));
        }

        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // TODO: Implement request update
        Session::flash('success', 'Request updated successfully');
        $this->redirect(url("requests/$id"));
    }

    /**
     * Delete request
     */
    public function delete(int $id): void
    {
        if ($this->method() !== 'DELETE') {
            $this->json(['error' => 'Invalid method'], 405);
        }

        if (!Session::has('user_id')) {
            $this->json(['error' => 'Unauthorized'], 401);
        }

        // TODO: Implement request deletion
        $this->json(['message' => 'Request deleted successfully']);
    }
}
