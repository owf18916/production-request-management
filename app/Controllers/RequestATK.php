<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\RequestATK as RequestATKModel;
use App\Models\MasterATK as MasterATKModel;
use App\Models\Conveyor as ConveyorModel;
use App\Models\User as UserModel;

class RequestATK extends Controller
{
    /**
     * Index - List user's requests (PIC)
     */
    public function index(): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        $search = $this->input('search');
        $statusFilter = $this->input('status');

        if ($userRole === 'admin') {
            // Admin sees all requests
            if ($search) {
                $requests = RequestATKModel::search($search);
            } else {
                $requests = RequestATKModel::getAll();
            }
        } else {
            // PIC sees only their requests
            $requests = RequestATKModel::getByUser($userId);
            
            // Apply search on their requests
            if ($search) {
                $requests = array_filter($requests, function($request) use ($search) {
                    return stripos($request->request_number, $search) !== false || 
                           stripos($request->nama_barang ?? '', $search) !== false;
                });
            }
        }

        // Apply status filter
        if ($statusFilter && $statusFilter !== 'all') {
            $requests = array_filter($requests, function($request) use ($statusFilter) {
                return $request->status === $statusFilter;
            });
        }

        $this->setTitle('Request ATK');
        $this->view('request_atk/index', [
            'requests' => array_values($requests),
            'search' => $search,
            'statusFilter' => $statusFilter,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * Create - Show create form (PIC)
     */
    public function create(): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        // Admin should not access PIC create view
        if ($userRole === 'admin') {
            $this->redirect(url('admin/requests/atk'), 'error', 'Admin cannot create requests from PIC view');
        }

        // Get active conveyors for dropdown
        $conveyors = ConveyorModel::getActive();
        
        // Shift options
        $shifts = ['Shift A', 'Shift B'];

        $this->setTitle('Create Request ATK');
        $this->view('request_atk/create', [
            'csrf_token' => Session::generateToken(),
            'conveyors' => $conveyors,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Store - Save new request (PIC)
     */
    public function store(): void
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            $this->redirect(url('requests/atk/create'), 'error', 'Invalid request');
        }

        $atkId = $this->input('atk_id');
        $conveyorId = $this->input('conveyor_id');
        $shift = $this->input('shift');
        $qty = $this->input('qty');
        $notes = $this->input('notes', '');

        $errors = [];

        // Validation
        if (!$atkId || !is_numeric($atkId)) {
            $errors['atk_id'] = 'ATK item is required';
        } else {
            $atk = MasterATKModel::findById((int)$atkId);
            if (!$atk) {
                $errors['atk_id'] = 'Invalid ATK item';
            }
        }

        if (!$qty || !is_numeric($qty)) {
            $errors['qty'] = 'Quantity is required and must be numeric';
        } elseif ((int)$qty < 1) {
            $errors['qty'] = 'Quantity must be at least 1';
        } elseif ((int)$qty > 9999) {
            $errors['qty'] = 'Quantity cannot exceed 9999';
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
            $conveyors = ConveyorModel::getActive();
            $shifts = ['Shift A', 'Shift B'];
            
            $this->setTitle('Create Request ATK');
            $this->view('request_atk/create', [
                'errors' => $errors,
                'csrf_token' => Session::generateToken(),
                'atk_id' => $atkId,
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'qty' => $qty,
                'notes' => $notes,
                'conveyors' => $conveyors,
                'shifts' => $shifts,
            ]);
            return;
        }

        // Generate request number
        $requestNumber = RequestATKModel::generateRequestNumber();

        // Create request
        $success = RequestATKModel::create([
            'request_number' => $requestNumber,
            'atk_id' => (int)$atkId,
            'conveyor_id' => $conveyorId ? (int)$conveyorId : null,
            'shift' => $shift ?: null,
            'qty' => (int)$qty,
            'status' => 'pending',
            'requested_by' => $userId,
            'notes' => $notes ?: null,
        ]);

        if ($success) {
            $this->redirect(url('requests/atk'), 'success', 'Request created successfully');
        } else {
            $this->redirect(url('requests/atk/create'), 'error', 'Failed to create request');
        }
    }

    /**
     * Show - View detail with history (PIC)
     */
    public function show(int $id): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        // Admin should use admin view instead
        if ($userRole === 'admin') {
            $this->redirect(url("admin/requests/atk/{$id}"));
        }

        $request = RequestATKModel::findById($id);
        
        if (!$request) {
            $this->redirect(url('requests/atk'), 'error', 'Request not found');
        }

        // Check access: PIC can only view their own
        if ($request->requested_by != $userId) {
            $this->redirect(url('requests/atk'), 'error', 'Unauthorized access');
        }

        $history = RequestATKModel::getHistory($id);

        $this->setTitle('Request ATK Detail');
        $this->view('request_atk/show', [
            'request' => $request,
            'history' => $history,
        ]);
    }

    /**
     * Admin Index - List all requests (Admin only)
     */
    public function adminIndex(): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId || $userRole !== 'admin') {
            $this->redirect(url('login'));
        }

        $search = $this->input('search');
        $statusFilter = $this->input('status');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        if ($search) {
            $requests = RequestATKModel::search($search);
        } elseif ($statusFilter && $statusFilter !== 'all' && $startDate && $endDate) {
            $requests = RequestATKModel::getByStatusAndDateRange($statusFilter, $startDate, $endDate);
        } else {
            $requests = RequestATKModel::getAll();
        }

        // Apply additional filters
        if ($statusFilter && $statusFilter !== 'all') {
            $requests = array_filter($requests, function($request) use ($statusFilter) {
                return $request->status === $statusFilter;
            });
        }

        $stats = [
            'pending' => RequestATKModel::countByStatus('pending'),
            'approved' => RequestATKModel::countByStatus('approved'),
            'rejected' => RequestATKModel::countByStatus('rejected'),
            'completed' => RequestATKModel::countByStatus('completed'),
        ];

        $this->setTitle('Request ATK Management');
        $this->view('admin/request_atk/admin_index', [
            'requests' => array_values($requests),
            'search' => $search,
            'statusFilter' => $statusFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * Admin Show - View detail with history and update form (Admin only)
     */
    public function adminShow(int $id): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId || $userRole !== 'admin') {
            $this->redirect(url('login'));
        }

        $request = RequestATKModel::findById($id);
        
        if (!$request) {
            $this->redirect(url('admin/requests/atk'), 'error', 'Request not found');
        }

        $history = RequestATKModel::getHistory($id);

        $this->setTitle('Request ATK Detail - Admin');
        $this->view('admin/request_atk/admin_show', [
            'request' => $request,
            'history' => $history,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * Update Status - Update request status (Admin only)
     */
    public function updateStatus(int $id): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId || $userRole !== 'admin') {
            $this->redirect(url('login'));
        }

        $csrfToken = $this->input('_csrf_token');
        if (!Session::verifyToken($csrfToken)) {
            $this->redirect(url("admin/requests/atk/{$id}"), 'error', 'CSRF token validation failed');
        }

        $request = RequestATKModel::findById($id);
        
        if (!$request) {
            $this->redirect(url('admin/requests/atk'), 'error', 'Request not found');
        }

        $status = $this->input('status');
        $notes = $this->input('notes', '');

        $errors = [];

        // Validate status based on current status
        $currentStatus = $request->status;
        $validNextStatuses = [];
        
        if ($currentStatus === 'pending') {
            $validNextStatuses = ['accepted', 'rejected'];
        } elseif ($currentStatus === 'accepted') {
            $validNextStatuses = ['completed', 'rejected'];
        }

        if (!$status || !in_array($status, $validNextStatuses)) {
            $errors['status'] = 'Invalid status transition. Current: ' . ucfirst($currentStatus);
        }

        // Validate notes requirement
        if ($status === 'rejected' && !$notes) {
            $errors['notes'] = 'Notes are required for rejection';
        }

        if (!empty($errors)) {
            $this->setTitle('Request ATK Detail - Admin');
            $this->view('admin/request_atk/admin_show', [
                'request' => $request,
                'history' => RequestATKModel::getHistory($id),
                'errors' => $errors,
                'csrf_token' => Session::generateToken(),
            ]);
            return;
        }

        // Update status
        $success = RequestATKModel::updateStatus($id, $status, $userId, $notes ?: null);

        if ($success) {
            $this->redirect(url("admin/requests/atk/{$id}"), 'success', 'Request status updated successfully');
        } else {
            $this->redirect(url("admin/requests/atk/{$id}"), 'error', 'Failed to update request status');
        }
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
