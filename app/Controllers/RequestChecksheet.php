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
        $checksheets = MasterChecksheet::getAll();
        $conveyors = ConveyorModel::getActive();
        $shifts = ['Shift A', 'Shift B'];

        $this->setTitle('Create Request Checksheet');
        $this->view('request_checksheet/create', [
            'checksheets' => $checksheets,
            'conveyors' => $conveyors,
            'shifts' => $shifts,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * PIC Store - Save new request
     */
    public function store(): void
    {
        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/request_checksheet/create'));
        }

        $checksheetId = $this->input('checksheet_id');
        $conveyorId = $this->input('conveyor_id');
        $shift = $this->input('shift');
        $qty = $this->input('qty');
        $notes = $this->input('notes', '');

        $errors = [];

        // Validation
        if (!$checksheetId) {
            $errors['checksheet_id'] = 'Checksheet is required';
        } else {
            $checksheet = MasterChecksheet::findById((int)$checksheetId);
            if (!$checksheet) {
                $errors['checksheet_id'] = 'Selected checksheet not found';
            }
        }

        if (!$qty) {
            $errors['qty'] = 'Quantity is required';
        } elseif (!is_numeric($qty) || (int)$qty < 1) {
            $errors['qty'] = 'Quantity must be a positive number';
        }

        // Validate conveyor (optional)
        if ($conveyorId && !is_numeric($conveyorId)) {
            $errors['conveyor_id'] = 'Invalid conveyor selected';
        } elseif ($conveyorId) {
            $conveyor = ConveyorModel::findById((int)$conveyorId);
            if (!$conveyor) {
                $errors['conveyor_id'] = 'Selected conveyor does not exist';
            }
        }

        // Validate shift (optional)
        if ($shift && !in_array($shift, ['Shift A', 'Shift B'])) {
            $errors['shift'] = 'Invalid shift selected';
        }

        if (!empty($errors)) {
            $this->setTitle('Create Request Checksheet');
            $this->view('request_checksheet/create', [
                'errors' => $errors,
                'checksheet_id' => $checksheetId,
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'qty' => $qty,
                'notes' => $notes,
                'checksheets' => MasterChecksheet::getAll(),
                'conveyors' => ConveyorModel::getActive(),
                'shifts' => ['Shift A', 'Shift B'],
                'csrf_token' => Session::generateToken(),
            ]);
            return;
        }

        // Generate request number and save
        $requestNumber = RequestChecksheetModel::generateRequestNumber();
        
        try {
            $success = RequestChecksheetModel::create([
                'request_number' => $requestNumber,
                'checksheet_id' => (int)$checksheetId,
                'conveyor_id' => $conveyorId ? (int)$conveyorId : null,
                'shift' => $shift ?: null,
                'qty' => (int)$qty,
                'status' => 'pending',
                'requested_by' => session('user_id'),
                'notes' => $notes ?: null,
            ]);
        } catch (\Exception $e) {
            error_log('RequestChecksheet::store() error: ' . $e->getMessage());
            $this->redirect(url('/request_checksheet/create'), 'error', 'Database error: ' . $e->getMessage());
            return;
        }

        if ($success) {
            $this->redirect(url('/request_checksheet'), 'success', 'Request created successfully');
        } else {
            $this->redirect(url('/request_checksheet/create'), 'error', 'Failed to create request');
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
