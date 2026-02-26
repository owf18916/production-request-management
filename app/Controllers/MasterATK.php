<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Pagination;
use App\Models\MasterATK as MasterATKModel;
use App\Models\ATKStock as ATKStockModel;

/**
 * MasterATK Controller
 * Handles ATK (Alat Tulis Kantor) management - Admin only
 */
class MasterATK extends Controller
{
    /**
     * List all ATK
     */
    public function index(): void
    {
        $search = $this->input('search');
        $page = (int) ($this->input('page') ?? 1);
        $perPage = 10;
        
        if ($search) {
            $atks = MasterATKModel::search($search);
        } else {
            $atks = MasterATKModel::getAll();
        }

        // Prepare atks array for pagination
        $atks = array_values((array)$atks);
        $totalAtks = count($atks);

        // Create pagination object
        $pagination = new Pagination($totalAtks, $perPage, $page);

        // Paginate the results
        $paginatedAtks = $pagination->paginate($atks);

        $this->setTitle('Master ATK Management');
        $this->view('admin/master/atk/index', [
            'atks' => $paginatedAtks,
            'pagination' => $pagination,
            'search' => $search,
            'totalCount' => $totalAtks,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->setTitle('Create Master ATK');
        $this->view('admin/master/atk/create');
    }

    /**
     * Store new ATK
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/master/atk/create'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/master/atk/create'));
        }

        // Validate input
        $errors = [];
        $kodeBarang = Security::sanitize($this->input('kode_barang'), 'string');
        $namaBarang = Security::sanitize($this->input('nama_barang'), 'string');

        // Validate kode_barang
        if (empty($kodeBarang)) {
            $errors['kode_barang'] = 'Kode barang is required';
        } elseif (strlen($kodeBarang) > 50) {
            $errors['kode_barang'] = 'Kode barang must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kodeBarang)) {
            $errors['kode_barang'] = 'Kode barang must contain only uppercase letters, numbers, and hyphens';
        } elseif (MasterATKModel::kodeBarangExists($kodeBarang)) {
            $errors['kode_barang'] = 'Kode barang already exists';
        }

        // Validate nama_barang
        if (empty($namaBarang)) {
            $errors['nama_barang'] = 'Nama barang is required';
        } elseif (strlen($namaBarang) > 150) {
            $errors['nama_barang'] = 'Nama barang must not exceed 150 characters';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('/admin/master/atk/create'));
        }

        // Create ATK
        $userId = Session::get('user_id');
        $data = [
            'kode_barang' => $kodeBarang,
            'nama_barang' => $namaBarang,
            'created_by' => $userId,
        ];

        $created = MasterATKModel::create($data);

        if (!$created) {
            Session::flash('error', 'Failed to create ATK. Please try again.');
            $this->redirect(url('/admin/master/atk'));
        }

        // Initialize stock for new ATK
        $newAtk = MasterATKModel::findByKodeBarang($kodeBarang);
        if ($newAtk) {
            ATKStockModel::initializeStock($newAtk->id, $userId);
        }

        Session::flash('success', 'ATK created successfully with stock initialized');
        $this->redirect(url('/admin/master/atk'));
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $atk = MasterATKModel::findById($id);

        if (!$atk) {
            Session::flash('error', 'ATK not found');
            $this->redirect(url('/admin/master/atk'));
        }

        $this->setTitle('Edit Master ATK');
        $this->view('admin/master/atk/edit', [
            'atk' => $atk,
        ]);
    }

    /**
     * Update ATK
     */
    public function update(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url("/admin/master/atk/edit/{$id}"));
        }

        $atk = MasterATKModel::findById($id);

        if (!$atk) {
            Session::flash('error', 'ATK not found');
            $this->redirect(url('/admin/master/atk'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("/admin/master/atk/edit/{$id}"));
        }

        // Validate input
        $errors = [];
        $kodeBarang = Security::sanitize($this->input('kode_barang'), 'string');
        $namaBarang = Security::sanitize($this->input('nama_barang'), 'string');

        // Validate kode_barang
        if (empty($kodeBarang)) {
            $errors['kode_barang'] = 'Kode barang is required';
        } elseif (strlen($kodeBarang) > 50) {
            $errors['kode_barang'] = 'Kode barang must not exceed 50 characters';
        } elseif (!preg_match('/^[A-Z0-9\-]+$/', $kodeBarang)) {
            $errors['kode_barang'] = 'Kode barang must contain only uppercase letters, numbers, and hyphens';
        } elseif (MasterATKModel::kodeBarangExists($kodeBarang, $id)) {
            $errors['kode_barang'] = 'Kode barang already exists';
        }

        // Validate nama_barang
        if (empty($namaBarang)) {
            $errors['nama_barang'] = 'Nama barang is required';
        } elseif (strlen($namaBarang) > 150) {
            $errors['nama_barang'] = 'Nama barang must not exceed 150 characters';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url("/admin/master/atk/edit/{$id}"));
        }

        // Update ATK
        $data = [
            'kode_barang' => $kodeBarang,
            'nama_barang' => $namaBarang,
        ];

        $updated = MasterATKModel::update($id, $data);

        if (!$updated) {
            Session::flash('error', 'Failed to update ATK. Please try again.');
            $this->redirect(url("/admin/master/atk/edit/{$id}"));
        }

        Session::flash('success', 'ATK updated successfully');
        $this->redirect(url('/admin/master/atk'));
    }

    /**
     * Delete ATK
     */
    public function delete(int $id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('/admin/master/atk'));
        }

        $atk = MasterATKModel::findById($id);

        if (!$atk) {
            Session::flash('error', 'ATK not found');
            $this->redirect(url('/admin/master/atk'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/master/atk'));
        }

        $deleted = MasterATKModel::delete($id);

        if (!$deleted) {
            Session::flash('error', 'Failed to delete ATK. Please try again.');
            $this->redirect(url('/admin/master/atk'));
        }

        Session::flash('success', 'ATK deleted successfully');
        $this->redirect(url('/admin/master/atk'));
    }

    /**
     * Search ATK (AJAX for dropdown)
     */
    public function search(): void
    {
        $keyword = $this->input('q', '');
        
        if (strlen($keyword) < 2) {
            $this->json(['results' => []]);
        }

        $results = MasterATKModel::search($keyword);
        
        $formatted = array_map(fn($atk) => [
            'id' => $atk->id,
            'text' => "{$atk->kode_barang} - {$atk->nama_barang}",
            'kode' => $atk->kode_barang,
            'nama' => $atk->nama_barang,
        ], $results);

        $this->json(['results' => $formatted]);
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
