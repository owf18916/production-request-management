<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Pagination;
use App\Models\ATKStock as ATKStockModel;
use App\Models\MasterATK as MasterATKModel;

/**
 * ATKStockManagement Controller
 * Handles ATK stock management operations (Admin only)
 */
class ATKStockManagement extends Controller
{
    /**
     * Index - Show all ATK stocks
     */
    public function index(): void
    {
        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect(url('/dashboard'));
        }

        $search = $this->input('search', '');
        $page = (int) ($this->input('page') ?? 1);
        $perPage = 10;
        $stocks = ATKStockModel::getAllWithATKInfo();

        // Apply search filter
        if ($search) {
            $stocks = array_filter($stocks, function($stock) use ($search) {
                return stripos($stock->kode_barang ?? '', $search) !== false ||
                       stripos($stock->nama_barang ?? '', $search) !== false;
            });
        }

        // Prepare stocks array for pagination
        $stocks = array_values($stocks);
        $totalStocks = count($stocks);

        // Create pagination object
        $pagination = new Pagination($totalStocks, $perPage, $page);

        // Paginate the results
        $paginatedStocks = $pagination->paginate($stocks);

        $this->setTitle('Manajemen Stock ATK');
        $this->view('admin/atk_stock/index', [
            'stocks' => $paginatedStocks,
            'pagination' => $pagination,
            'search' => $search,
            'totalCount' => $totalStocks,
        ]);
    }

    /**
     * Show add incoming stock form
     */
    public function addIncoming(int $atkId): void
    {
        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect(url('/admin/atk-stock'));
        }

        $atk = MasterATKModel::findById($atkId);
        if (!$atk) {
            Session::flash('error', 'Item ATK tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $stock = ATKStockModel::findByAtkId($atkId);
        if (!$stock) {
            Session::flash('error', 'Data stock tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $this->setTitle('Tambah Stock Masuk - ' . $atk->nama_barang);
        $this->view('admin/atk_stock/add_incoming', [
            'atk' => $atk,
            'stock' => $stock,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * Store incoming stock
     */
    public function storeIncoming(int $atkId): void
    {
        // Check CSRF
        if (!$this->validateCSRF()) {
            Session::flash('error', 'Token keamanan tidak valid');
            $this->redirect(url("/admin/atk-stock/add-incoming/{$atkId}"));
        }

        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses');
            $this->redirect(url('/admin/atk-stock'));
        }

        $qty = $this->input('qty', 0);
        $notes = $this->input('notes', '');

        // Validate input
        if (!is_numeric($qty) || $qty <= 0) {
            Session::flash('error', 'Jumlah stok tidak valid');
            $this->redirect(url("/admin/atk-stock/add-incoming/{$atkId}"));
        }

        $atk = MasterATKModel::findById($atkId);
        if (!$atk) {
            Session::flash('error', 'Item ATK tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $userId = Session::get('user_id');
        $success = ATKStockModel::addIncoming($atkId, (int)$qty, $userId, $notes);

        if ($success) {
            Session::flash('success', "Stock masuk berhasil ditambahkan: +{$qty} unit");
            $this->redirect(url('/admin/atk-stock'));
        } else {
            Session::flash('error', 'Gagal menambahkan stock');
            $this->redirect(url("/admin/atk-stock/add-incoming/{$atkId}"));
        }
    }

    /**
     * Show adjustment form
     */
    public function showAdjustmentForm(int $atkId): void
    {
        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect(url('/admin/atk-stock'));
        }

        $atk = MasterATKModel::findById($atkId);
        if (!$atk) {
            Session::flash('error', 'Item ATK tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $stock = ATKStockModel::findByAtkId($atkId);
        if (!$stock) {
            Session::flash('error', 'Data stock tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $this->setTitle('Penyesuaian Stock - ' . $atk->nama_barang);
        $this->view('admin/atk_stock/adjustment', [
            'atk' => $atk,
            'stock' => $stock,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * Store adjustment
     */
    public function storeAdjustment(int $atkId): void
    {
        // Check CSRF
        if (!$this->validateCSRF()) {
            Session::flash('error', 'Token keamanan tidak valid');
            $this->redirect(url("/admin/atk-stock/adjustment/{$atkId}"));
        }

        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses');
            $this->redirect(url('/admin/atk-stock'));
        }

        $qty = $this->input('qty', 0);
        $notes = $this->input('notes', '');

        // Validate input
        if (!is_numeric($qty)) {
            Session::flash('error', 'Jumlah penyesuaian tidak valid');
            $this->redirect(url("/admin/atk-stock/adjustment/{$atkId}"));
        }

        $atk = MasterATKModel::findById($atkId);
        if (!$atk) {
            Session::flash('error', 'Item ATK tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        if ($qty == 0) {
            Session::flash('error', 'Masukkan nilai penyesuaian (positif atau negatif)');
            $this->redirect(url("/admin/atk-stock/adjustment/{$atkId}"));
        }

        if (!$notes) {
            Session::flash('error', 'Catatan penyesuaian harus diisi (alasan penyesuaian)');
            $this->redirect(url("/admin/atk-stock/adjustment/{$atkId}"));
        }

        $userId = Session::get('user_id');
        $success = ATKStockModel::addAdjustment($atkId, (int)$qty, $userId, $notes);

        if ($success) {
            $sign = $qty > 0 ? '+' : '';
            Session::flash('success', "Penyesuaian stok berhasil disimpan: {$sign}{$qty} unit");
            $this->redirect(url('/admin/atk-stock'));
        } else {
            Session::flash('error', 'Gagal menyimpan penyesuaian');
            $this->redirect(url("/admin/atk-stock/adjustment/{$atkId}"));
        }
    }

    /**
     * View transaction history
     */
    public function transactionHistory(int $atkId): void
    {
        // Check admin role
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect(url('/admin/atk-stock'));
        }

        $atk = MasterATKModel::findById($atkId);
        if (!$atk) {
            Session::flash('error', 'Item ATK tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $stock = ATKStockModel::findByAtkId($atkId);
        if (!$stock) {
            Session::flash('error', 'Data stock tidak ditemukan');
            $this->redirect(url('/admin/atk-stock'));
        }

        $history = ATKStockModel::getTransactionHistory($atkId, 100);

        $this->setTitle('Riwayat Transaksi Stock - ' . $atk->nama_barang);
        $this->view('admin/atk_stock/transaction_history', [
            'atk' => $atk,
            'stock' => $stock,
            'history' => $history,
        ]);
    }

    /**
     * CSRF Validation helper
     */
    private function validateCSRF(): bool
    {
        $csrfToken = $this->input('_csrf_token');
        return Session::verifyToken($csrfToken);
    }
}
