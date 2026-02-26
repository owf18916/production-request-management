<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Pagination;
use App\Models\MasterChecksheet as MasterChecksheetModel;

/**
 * MasterChecksheet Controller
 * Handles Checksheet management - Admin only
 */
class MasterChecksheet extends Controller
{
    /**
     * List all Checksheets
     */
    public function index(): void
    {
        $search = $this->input('search');
        $page = (int) ($this->input('page') ?? 1);
        $perPage = 10;
        
        if ($search) {
            $checksheets = MasterChecksheetModel::search($search);
        } else {
            $checksheets = MasterChecksheetModel::getAll();
        }

        // Prepare checksheets array for pagination
        $checksheets = array_values((array)$checksheets);
        $totalChecksheets = count($checksheets);

        // Create pagination object
        $pagination = new Pagination($totalChecksheets, $perPage, $page);

        // Paginate the results
        $paginatedChecksheets = $pagination->paginate($checksheets);

        $this->setTitle('Master Checksheet Management');
        $this->view('admin/master/checksheet/index', [
            'checksheets' => $paginatedChecksheets,
            'pagination' => $pagination,
            'search' => $search,
            'totalCount' => $totalChecksheets,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->setTitle('Create Master Checksheet');
        $this->view('admin/master/checksheet/create');
    }

    /**
     * Store new Checksheet
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/master/checksheet/create'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/master/checksheet/create'));
        }

        // Validate input
        $errors = [];
        $kodeChecksheet = Security::sanitize($this->input('kode_checksheet'), 'string');
        $namaChecksheet = Security::sanitize($this->input('nama_checksheet'), 'string');

        // Validate kode_checksheet
        if (empty($kodeChecksheet)) {
            $errors['kode_checksheet'] = 'Kode checksheet is required';
        } elseif (strlen($kodeChecksheet) > 50) {
            $errors['kode_checksheet'] = 'Kode checksheet must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kodeChecksheet)) {
            $errors['kode_checksheet'] = 'Kode checksheet must contain only uppercase letters, numbers, and hyphens';
        } elseif (MasterChecksheetModel::kodeChecksheetExists($kodeChecksheet)) {
            $errors['kode_checksheet'] = 'Kode checksheet already exists';
        }

        // Validate nama_checksheet
        if (empty($namaChecksheet)) {
            $errors['nama_checksheet'] = 'Nama checksheet is required';
        } elseif (strlen($namaChecksheet) > 150) {
            $errors['nama_checksheet'] = 'Nama checksheet must not exceed 150 characters';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('/admin/master/checksheet/create'));
        }

        // Create Checksheet
        $userId = Session::get('user_id');
        $data = [
            'kode_checksheet' => $kodeChecksheet,
            'nama_checksheet' => $namaChecksheet,
            'created_by' => $userId,
        ];

        $created = MasterChecksheetModel::create($data);

        if (!$created) {
            Session::flash('error', 'Failed to create checksheet. Please try again.');
            $this->redirect(url('/admin/master/checksheet/create'));
        }

        Session::flash('success', 'Checksheet created successfully');
        $this->redirect(url('/admin/master/checksheet'));
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $checksheet = MasterChecksheetModel::findById($id);

        if (!$checksheet) {
            Session::flash('error', 'Checksheet not found');
            $this->redirect(url('/admin/master/checksheet'));
        }

        $this->setTitle('Edit Master Checksheet');
        $this->view('admin/master/checksheet/edit', [
            'checksheet' => $checksheet,
        ]);
    }

    /**
     * Update Checksheet
     */
    public function update(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url("/admin/master/checksheet/edit/{$id}"));
        }

        $checksheet = MasterChecksheetModel::findById($id);

        if (!$checksheet) {
            Session::flash('error', 'Checksheet not found');
            $this->redirect(url('/admin/master/checksheet'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("/admin/master/checksheet/edit/{$id}"));
        }

        // Validate input
        $errors = [];
        $kodeChecksheet = Security::sanitize($this->input('kode_checksheet'), 'string');
        $namaChecksheet = Security::sanitize($this->input('nama_checksheet'), 'string');

        // Validate kode_checksheet
        if (empty($kodeChecksheet)) {
            $errors['kode_checksheet'] = 'Kode checksheet is required';
        } elseif (strlen($kodeChecksheet) > 50) {
            $errors['kode_checksheet'] = 'Kode checksheet must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kodeChecksheet)) {
            $errors['kode_checksheet'] = 'Kode checksheet must contain only uppercase letters, numbers, and hyphens';
        } elseif (MasterChecksheetModel::kodeChecksheetExists($kodeChecksheet, $id)) {
            $errors['kode_checksheet'] = 'Kode checksheet already exists';
        }

        // Validate nama_checksheet
        if (empty($namaChecksheet)) {
            $errors['nama_checksheet'] = 'Nama checksheet is required';
        } elseif (strlen($namaChecksheet) > 150) {
            $errors['nama_checksheet'] = 'Nama checksheet must not exceed 150 characters';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url("/admin/master/checksheet/edit/{$id}"));
        }

        // Update Checksheet
        $data = [
            'kode_checksheet' => $kodeChecksheet,
            'nama_checksheet' => $namaChecksheet,
        ];

        $updated = MasterChecksheetModel::update($id, $data);

        if (!$updated) {
            Session::flash('error', 'Failed to update checksheet. Please try again.');
            $this->redirect(url("/admin/master/checksheet/edit/{$id}"));
        }

        Session::flash('success', 'Checksheet updated successfully');
        $this->redirect(url('/admin/master/checksheet'));
    }

    /**
     * Delete Checksheet
     */
    public function delete(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/master/checksheet'));
        }

        $checksheet = MasterChecksheetModel::findById($id);

        if (!$checksheet) {
            Session::flash('error', 'Checksheet not found');
            $this->redirect(url('/admin/master/checksheet'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/master/checksheet'));
        }

        $deleted = MasterChecksheetModel::delete($id);

        if (!$deleted) {
            Session::flash('error', 'Failed to delete checksheet. Please try again.');
            $this->redirect(url('/admin/master/checksheet'));
        }

        Session::flash('success', 'Checksheet deleted successfully');
        $this->redirect(url('/admin/master/checksheet'));
    }

    /**
     * Search Checksheet (AJAX for dropdown)
     */
    public function search(): void
    {
        $keyword = $this->input('q', '');
        
        if (strlen($keyword) < 2) {
            $this->json(['results' => []]);
        }

        $results = MasterChecksheetModel::search($keyword);
        
        $formatted = array_map(fn($checksheet) => [
            'id' => $checksheet->id,
            'text' => "{$checksheet->kode_checksheet} - {$checksheet->nama_checksheet}",
            'kode' => $checksheet->kode_checksheet,
            'nama' => $checksheet->nama_checksheet,
        ], $results);

        $this->json(['results' => $formatted]);
    }

    /**
     * Set page title - MUST RETURN SELF
     */
    protected function setTitle(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }
}
