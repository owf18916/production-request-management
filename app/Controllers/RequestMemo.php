<?php

namespace App\Controllers;

use App\Session;
use App\Database;
use App\Security;
use App\Controller;
use App\Models\RequestMemo as RequestMemoModel;
use App\Models\Conveyor as ConveyorModel;

class RequestMemo extends Controller
{
    /**
     * PIC: Index - List user's requests
     */
    public function index(): void
    {
        $search = $this->input('search');
        $status = $this->input('status');
        
        $requests = RequestMemoModel::getByUser(session('user_id'));
        
        if ($search) {
            $requests = array_filter($requests, function($req) use ($search) {
                return stripos($req->request_number, $search) !== false ||
                       stripos($req->memo_content, $search) !== false;
            });
        }
        
        if ($status) {
            $requests = array_filter($requests, function($req) use ($status) {
                return $req->status === $status;
            });
        }

        $this->setTitle('Internal Memo Requests');
        $this->view('request_memo/index', [
            'requests' => array_values($requests),
            'search' => $search,
            'status' => $status,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * PIC: Create - Show create form
     */
    public function create(): void
    {
        $conveyors = ConveyorModel::getActive();
        $shifts = ['Shift A', 'Shift B'];
        
        $this->setTitle('Create Internal Memo Request');
        $this->view('request_memo/create', [
            'conveyors' => $conveyors,
            'shifts' => $shifts,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * PIC: Store - Save new request
     */
    public function store(): void
    {
        if (!$this->validateCSRFToken()) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/requests/memo'));
        }

        $memoContent = $this->input('memo_content');
        $conveyorId = $this->input('conveyor_id');
        $shift = $this->input('shift');
        $errors = [];

        // Validation
        if (!$memoContent) {
            $errors['memo_content'] = 'Memo content is required';
        } elseif (strlen($memoContent) < 10) {
            $errors['memo_content'] = 'Memo content must be at least 10 characters';
        } elseif (strlen($memoContent) > 5000) {
            $errors['memo_content'] = 'Memo content must not exceed 5000 characters';
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
            $this->setTitle('Create Internal Memo Request');
            $this->view('request_memo/create', [
                'errors' => $errors,
                'memo_content' => $memoContent,
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'conveyors' => ConveyorModel::getActive(),
                'shifts' => ['Shift A', 'Shift B'],
                'csrf_token' => Session::generateToken(),
            ]);
            return;
        }

        // Generate request number and save
        $requestNumber = RequestMemoModel::generateRequestNumber();
        $success = RequestMemoModel::create([
            'request_number' => $requestNumber,
            'conveyor_id' => $conveyorId ? (int)$conveyorId : null,
            'shift' => $shift ?: null,
            'memo_content' => $memoContent,
            'status' => 'pending',
            'requested_by' => session('user_id'),
        ]);

        if ($success) {
            // Add initial history
            $lastId = \App\Database::lastId();
            RequestMemoModel::addHistory($lastId, 'pending', session('user_id'), 'Request created');
            
            Session::flash('success', 'Internal Memo request created successfully. Request #: ' . $requestNumber);
            $this->redirect(url('/requests/memo'));
        } else {
            Session::flash('error', 'Failed to create request');
            $this->redirect(url('/requests/memo/create'));
        }
    }

    /**
     * PIC: Show - View detail with history
     */
    public function show(int $id): void
    {
        $memo = RequestMemoModel::findById($id);
        if (!$memo) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/requests/memo'));
        }

        // PIC can only view own requests
        if (session('user_role') !== 'admin' && $memo->requested_by !== session('user_id')) {
            Session::flash('error', 'Unauthorized access');
            $this->redirect(url('/requests/memo'));
        }

        $history = RequestMemoModel::getHistory($id);

        $this->setTitle('Internal Memo Request Detail');
        $this->view('request_memo/show', [
            'memo' => $memo,
            'history' => $history,
        ]);
    }

    /**
     * Admin: Index - List all requests with filters
     */
    public function adminIndex(): void
    {
        $search = $this->input('search');
        $status = $this->input('status');
        $dateFrom = $this->input('date_from');
        $dateTo = $this->input('date_to');
        
        $requests = RequestMemoModel::getAll();
        
        if ($search) {
            $requests = array_filter($requests, function($req) use ($search) {
                return stripos($req->request_number, $search) !== false ||
                       stripos($req->memo_content, $search) !== false ||
                       stripos($req->requester, $search) !== false;
            });
        }
        
        if ($status) {
            $requests = array_filter($requests, function($req) use ($status) {
                return $req->status === $status;
            });
        }
        
        if ($dateFrom) {
            $requests = array_filter($requests, function($req) use ($dateFrom) {
                return strtotime($req->created_at) >= strtotime($dateFrom);
            });
        }
        
        if ($dateTo) {
            $requests = array_filter($requests, function($req) use ($dateTo) {
                return strtotime($req->created_at) <= strtotime($dateTo . ' 23:59:59');
            });
        }

        $this->setTitle('Manage Internal Memo Requests');
        $this->view('admin/request_memo/admin_index', [
            'requests' => array_values($requests),
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalCount' => count($requests),
            'pendingCount' => RequestMemoModel::countByStatus('pending'),
            'approvedCount' => RequestMemoModel::countByStatus('approved'),
            'rejectedCount' => RequestMemoModel::countByStatus('rejected'),
            'completedCount' => RequestMemoModel::countByStatus('completed'),
        ]);
    }

    /**
     * Admin: Show - View detail with history
     */
    public function adminShow(int $id): void
    {
        $memo = RequestMemoModel::findById($id);
        if (!$memo) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/admin/requests/memo'));
        }

        $history = RequestMemoModel::getHistory($id);

        $this->setTitle('Internal Memo Request Detail');
        $this->view('admin/request_memo/admin_show', [
            'memo' => $memo,
            'history' => $history,
        ]);
    }

    /**
     * Admin: Update status
     */
    public function updateStatus(int $id): void
    {
        if (!$this->validateCSRFToken()) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/requests/memo'));
        }

        $memo = RequestMemoModel::findById($id);
        if (!$memo) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/admin/requests/memo'));
        }

        $status = $this->input('status');
        $notes = $this->input('notes');
        $errors = [];

        // Validation
        $validStatuses = ['approved', 'rejected', 'completed'];
        if (!in_array($status, $validStatuses)) {
            $errors['status'] = 'Invalid status';
        }

        if ($status === 'rejected' && !$notes) {
            $errors['notes'] = 'Notes are required when rejecting';
        }

        if (!empty($errors)) {
            $this->setTitle('Internal Memo Request Detail');
            $this->view('admin/request_memo/admin_show', [
                'memo' => $memo,
                'history' => RequestMemoModel::getHistory($id),
                'errors' => $errors,
            ]);
            return;
        }

        // Update status
        $success = RequestMemoModel::updateStatus($id, $status, session('user_id'), $notes);

        if ($success) {
            // Add history
            RequestMemoModel::addHistory($id, $status, session('user_id'), $notes);
            
            $statusLabel = $status === 'approved' ? 'Approved' : ($status === 'rejected' ? 'Rejected' : 'Completed');
            Session::flash('success', 'Request ' . $statusLabel . ' successfully');
            $this->redirect(url("/admin/requests/memo/show/{$id}"));
        } else {
            Session::flash('error', 'Failed to update request status');
            $this->redirect(url("/admin/requests/memo/show/{$id}"));
        }
    }

    /**
     * Set page title - returns self for method chaining
     */
    protected function setTitle(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Validate CSRF token
     */
    private function validateCSRFToken(): bool
    {
        $csrfToken = $this->input('_csrf_token');
        return Session::verifyToken($csrfToken);
    }
}
