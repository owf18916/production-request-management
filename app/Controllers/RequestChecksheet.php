<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\RequestChecksheet as RequestChecksheetModel;
use App\Models\MasterChecksheet;
use App\Models\Conveyor as ConveyorModel;
use App\Models\User;

class RequestChecksheet extends Controller
{
    /**
     * PIC Index - List user's requests
     */
    public function index(): void
    {
        $userId = session('user_id');
        $search = $this->input('search');
        $status = $this->input('status');
        
        $requests = RequestChecksheetModel::getByUser($userId);

        // Filter by status if provided
        if ($status && $status !== '') {
            $requests = array_filter($requests, function ($req) use ($status) {
                return $req->status === $status;
            });
        }

        // Filter by search if provided
        if ($search) {
            $requests = array_filter($requests, function ($req) use ($search) {
                $query = strtolower($search);
                return strpos(strtolower($req->request_number), $query) !== false ||
                       strpos(strtolower($req->judul_checksheet), $query) !== false;
            });
        }

        $this->setTitle('My Requests - Checksheet');
        $this->view('request_checksheet/index', [
            'requests' => array_values($requests),
            'search' => $search,
            'status' => $status,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * PIC Create - Show create form
     */
    public function create(): void
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        // Check if user has setup conveyor and shift
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('dashboard'), 'error', 'Harap setup Conveyor dan Shift terlebih dahulu di Dashboard');
        }

        $activeConveyorId = Session::getActiveConveyorId();
        $activeConveyorName = Session::getActiveConveyorName();
        $activeShift = Session::getActiveShift();

        $this->setTitle('Create Request Checksheet');
        $this->view('request_checksheet/create', [
            'csrf_token' => Session::generateToken(),
            'active_conveyor_id' => $activeConveyorId,
            'active_conveyor_name' => $activeConveyorName,
            'active_shift' => $activeShift,
        ]);
    }

    /**
     * Store - Save new request with multiple items
     */
    public function store(): void
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        // Get request body - could be JSON or form-encoded
        $rawInput = file_get_contents('php://input');
        $input = [];
        
        // Try to decode as JSON first
        if (!empty($rawInput) && $rawInput[0] === '{') {
            $input = json_decode($rawInput, true) ?: [];
        } else {
            // Fall back to $_POST
            $input = $_POST;
        }

        $csrfToken = $input['_csrf_token'] ?? null;
        if (!Session::verifyToken($csrfToken)) {
            $this->redirect(url('request_checksheet/create'), 'error', 'Invalid request');
        }

        // Validate that conveyor and shift are setup
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('request_checksheet/create'), 'error', 'Conveyor dan Shift belum di-setup');
        }

        // Get items from form (array)
        $items = $input['items'] ?? null;

        $errors = [];

        // Validate items array
        if (!$items || !is_array($items) || empty($items)) {
            $errors['items'] = 'Minimal harus ada 1 item';
        } else {
            // Validate each item
            foreach ($items as $index => $item) {
                if (empty($item['checksheet_id']) || !is_numeric($item['checksheet_id'])) {
                    $errors["items.{$index}.checksheet_id"] = "Item " . ($index + 1) . ": Checksheet harus dipilih";
                } else {
                    $checksheet = MasterChecksheet::findById((int)$item['checksheet_id']);
                    if (!$checksheet) {
                        $errors["items.{$index}.checksheet_id"] = "Item " . ($index + 1) . ": Checksheet tidak valid";
                    }
                }

                if (empty($item['qty']) || !is_numeric($item['qty'])) {
                    $errors["items.{$index}.qty"] = "Item " . ($index + 1) . ": Quantity harus diisi";
                } elseif ((int)$item['qty'] < 1 || (int)$item['qty'] > 9999) {
                    $errors["items.{$index}.qty"] = "Item " . ($index + 1) . ": Quantity harus antara 1-9999";
                }
            }
        }

        if (!empty($errors)) {
            // Check if AJAX request (JSON)
            if (!empty($rawInput) && $rawInput[0] === '{') {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'errors' => $errors
                ]);
                return;
            }
            
            $this->setTitle('Create Request Checksheet');
            $this->view('request_checksheet/create', [
                'errors' => $errors,
                'csrf_token' => Session::generateToken(),
                'active_conveyor_id' => Session::getActiveConveyorId(),
                'active_conveyor_name' => Session::getActiveConveyorName(),
                'active_shift' => Session::getActiveShift(),
                'items' => $items,
            ]);
            return;
        }

        // Generate request number
        $requestNumber = RequestChecksheetModel::generateRequestNumber();

        $conveyorId = Session::getActiveConveyorId();
        $shift = Session::getActiveShift();
        $allSuccess = true;
        $insertErrors = [];

        // Insert each item
        foreach ($items as $index => $item) {
            $success = RequestChecksheetModel::create([
                'request_number' => $requestNumber,
                'checksheet_id' => (int)$item['checksheet_id'],
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'qty' => (int)$item['qty'],
                'status' => 'pending',
                'requested_by' => $userId,
                'notes' => $item['notes'] ?? null,
            ]);

            if (!$success) {
                $allSuccess = false;
                $insertErrors[] = "Item " . ($index + 1) . " gagal disimpan";
                error_log("RequestChecksheet store - Failed to insert item " . ($index + 1) . " for request " . $requestNumber);
            }
        }

        if ($allSuccess) {
            // Check if AJAX request (JSON)
            if (!empty($rawInput) && $rawInput[0] === '{') {
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Request dengan ' . count($items) . ' item berhasil dibuat',
                    'redirect' => url('request_checksheet')
                ]);
                return;
            }
            
            $this->redirect(url('request_checksheet'), 'success', 'Request dengan ' . count($items) . ' item berhasil dibuat');
        } else {
            if (!empty($rawInput) && $rawInput[0] === '{') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Gagal membuat request: ' . implode(', ', $insertErrors),
                    'details' => $insertErrors
                ]);
                return;
            }
            
            $this->redirect(url('request_checksheet/create'), 'error', 'Gagal membuat request: ' . implode(', ', $insertErrors));
        }
    }

    /**
     * PIC Show - View request detail
     */
    public function show(int $id): void
    {
        $userId = session('user_id');
        $request = RequestChecksheetModel::findById($id);

        if (!$request) {
            $this->redirect('/request_checksheet', 'error', 'Request not found');
        }

        // Check authorization - PIC can only view own requests
        if ($request->requested_by != $userId) {
            $this->redirect('/request_checksheet', 'error', 'Unauthorized access');
        }

        $history = RequestChecksheetModel::getHistory($id);
        
        $this->setTitle('Request Detail - Checksheet');
        $this->view('request_checksheet/show', [
            'request' => $request,
            'history' => $history,
        ]);
    }

    /**
     * Admin Index - List all requests
     */
    public function adminIndex(): void
    {
        $search = $this->input('search');
        $status = $this->input('status');
        
        $requests = RequestChecksheetModel::getAll();

        // Filter by status if provided
        if ($status && $status !== '') {
            $requests = array_filter($requests, function ($req) use ($status) {
                return $req->status === $status;
            });
        }

        // Filter by search if provided
        if ($search) {
            $requests = array_filter($requests, function ($req) use ($search) {
                $query = strtolower($search);
                return strpos(strtolower($req->request_number), $query) !== false ||
                       strpos(strtolower($req->judul_checksheet), $query) !== false ||
                       strpos(strtolower($req->full_name), $query) !== false;
            });
        }

        $this->setTitle('Request Checksheet Management');
        $this->view('admin/request_checksheet/admin_index', [
            'requests' => array_values($requests),
            'search' => $search,
            'status' => $status,
            'totalCount' => count($requests),
            'pendingCount' => RequestChecksheetModel::countByStatus('pending'),
            'approvedCount' => RequestChecksheetModel::countByStatus('approved'),
            'completedCount' => RequestChecksheetModel::countByStatus('completed'),
        ]);
    }

    /**
     * Admin Show - View request detail with status update form
     */
    public function adminShow(int $id): void
    {
        $request = RequestChecksheetModel::findById($id);

        if (!$request) {
            $this->redirect('/admin/request_checksheet', 'error', 'Request not found');
        }

        $history = RequestChecksheetModel::getHistory($id);
        
        $this->setTitle('Request Detail - Checksheet');
        $this->view('admin/request_checksheet/admin_show', [
            'request' => $request,
            'history' => $history,
        ]);
    }

    /**
     * Admin Update Status
     */
    public function updateStatus(int $id): void
    {
        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("/admin/request_checksheet/show/{$id}"));
        }

        $request = RequestChecksheetModel::findById($id);
        if (!$request) {
            $this->redirect(url('/admin/request_checksheet'), 'error', 'Request not found');
        }

        $newStatus = $this->input('status');
        $notes = $this->input('notes');

        $errors = [];

        // Validate status transition
        $validStatuses = $this->getValidStatusTransitions($request->status);
        if (!in_array($newStatus, $validStatuses)) {
            $errors['status'] = 'Invalid status transition';
        }

        // Validate notes for rejection
        if ($newStatus === 'rejected' && !$notes) {
            $errors['notes'] = 'Rejection reason is required';
        }

        if (!empty($errors)) {
            $this->setTitle('Request Detail - Checksheet');
            $this->view('admin/request_checksheet/admin_show', [
                'request' => $request,
                'history' => RequestChecksheetModel::getHistory($id),
                'errors' => $errors,
            ]);
            return;
        }

        // Update status
        $success = RequestChecksheetModel::updateStatus(
            $id,
            $newStatus,
            session('user_id'),
            $notes
        );

        if ($success) {
            $this->redirect(url("/admin/request_checksheet/show/{$id}"), 'success', 'Status updated successfully');
        } else {
            $this->redirect(url("/admin/request_checksheet/show/{$id}"), 'error', 'Failed to update status');
        }
    }

    /**
     * Get valid status transitions based on current status
     */
    protected function getValidStatusTransitions(string $currentStatus): array
    {
        $transitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['completed', 'rejected'],
            'rejected' => [],
            'completed' => [],
        ];

        return $transitions[$currentStatus] ?? [];
    }

    /**
     * Search checksheets - AJAX response
     */
    public function searchChecksheet(): void
    {
        header('Content-Type: application/json');
        $query = $this->input('q', '');
        
        if (strlen($query) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }

        $allChecksheets = MasterChecksheet::getAll();
        $results = array_filter($allChecksheets, function ($cs) use ($query) {
            $q = strtolower($query);
            return strpos(strtolower($cs->kode_checksheet ?? ''), $q) !== false ||
                   strpos(strtolower($cs->judul_checksheet ?? ''), $q) !== false;
        });

        echo json_encode(['results' => array_values($results)]);
        exit;
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
