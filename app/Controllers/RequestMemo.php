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
        
        $this->setTitle('Create Internal Memo Request');
        $this->view('request_memo/create', [
            'csrf_token' => Session::generateToken(),
            'active_conveyor_id' => $activeConveyorId,
            'active_conveyor_name' => $activeConveyorName,
            'active_shift' => $activeShift,
        ]);
    }

    /**
     * PIC: Store - Save request with multiple memo items
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
            $this->redirect(url('requests/memo/create'), 'error', 'Invalid request');
        }

        // Validate that conveyor and shift are setup
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('requests/memo/create'), 'error', 'Conveyor dan Shift belum di-setup');
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
                if (empty($item['memo_content'])) {
                    $errors["items.{$index}.memo_content"] = "Item " . ($index + 1) . ": Memo content harus diisi";
                } elseif (strlen($item['memo_content']) < 10) {
                    $errors["items.{$index}.memo_content"] = "Item " . ($index + 1) . ": Memo minimal 10 karakter";
                } elseif (strlen($item['memo_content']) > 5000) {
                    $errors["items.{$index}.memo_content"] = "Item " . ($index + 1) . ": Memo maksimal 5000 karakter";
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
            
            $this->setTitle('Create Internal Memo Request');
            $this->view('request_memo/create', [
                'errors' => $errors,
                'csrf_token' => Session::generateToken(),
                'active_conveyor_id' => Session::getActiveConveyorId(),
                'active_conveyor_name' => Session::getActiveConveyorName(),
                'active_shift' => Session::getActiveShift(),
                'items' => $items,
            ]);
            return;
        }

        $conveyorId = Session::getActiveConveyorId();
        $shift = Session::getActiveShift();
        $allSuccess = true;
        $insertErrors = [];

        // Insert each item
        foreach ($items as $index => $item) {
            // Generate request number untuk setiap item
            $requestNumber = RequestMemoModel::generateRequestNumber();
            
            $success = RequestMemoModel::create([
                'request_number' => $requestNumber,
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'memo_content' => $item['memo_content'],
                'status' => 'pending',
                'requested_by' => $userId,
            ]);

            if (!$success) {
                $allSuccess = false;
                $insertErrors[] = "Item " . ($index + 1) . " gagal disimpan";
                error_log("RequestMemo store - Failed to insert item " . ($index + 1) . " for request " . $requestNumber);
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
                    'redirect' => url('requests/memo')
                ]);
                return;
            }
            
            $this->redirect(url('requests/memo'), 'success', 'Request dengan ' . count($items) . ' item berhasil dibuat');
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
            
            $this->redirect(url('requests/memo/create'), 'error', 'Gagal membuat request: ' . implode(', ', $insertErrors));
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
     * Cancel - Cancel own pending request
     */
    public function cancel(int $id): void
    {
        $userId = session('user_id');
        $memo = RequestMemoModel::findById($id);

        if (!$memo) {
            $this->redirect(url('/requests/memo'), 'error', 'Request not found');
        }

        // Check authorization - PIC can only cancel own requests
        if ($memo->requested_by !== $userId) {
            $this->redirect(url('/requests/memo'), 'error', 'Unauthorized access');
        }

        // Check if request can be cancelled (only pending can be cancelled)
        if ($memo->status === 'approved') {
            $this->redirect(url("requests/memo/show/{$id}"), 'error', 'Cannot cancel an approved request');
        }

        if ($memo->status === 'completed') {
            $this->redirect(url("requests/memo/show/{$id}"), 'error', 'Cannot cancel a completed request');
        }

        if ($memo->status === 'cancelled') {
            $this->redirect(url("requests/memo/show/{$id}"), 'error', 'Request is already cancelled');
        }

        // Update status to cancelled with cancellation note
        $success = RequestMemoModel::updateStatus(
            $id,
            'cancelled',
            $userId,
            'Cancelled by requester'
        );

        if ($success) {
            $this->redirect(url("requests/memo/show/{$id}"), 'success', 'Request cancelled successfully');
        } else {
            $this->redirect(url("requests/memo/show/{$id}"), 'error', 'Failed to cancel request');
        }
    }

    /**
     * Admin: Index - List all requests with filters
     */
    public function adminIndex(): void
    {
        $search = $this->input('search');
        $status = $this->input('status');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');
        
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

        if ($startDate && $endDate) {
            $requests = array_filter($requests, function($req) use ($startDate, $endDate) {
                $createdDate = date('Y-m-d', strtotime($req->created_at));
                return $createdDate >= $startDate && $createdDate <= $endDate;
            });
        }

        $this->setTitle('Manage Internal Memo Requests');
        $this->view('admin/request_memo/admin_index', [
            'requests' => array_values($requests),
            'search' => $search,
            'status' => $status,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

    /**
     * Export - Export memo requests to Excel
     */
    public function export(): void
    {
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Validate date range
        if (!$startDate || !$endDate) {
            Session::flash('error', 'Tanggal mulai dan akhir harus diisi');
            $this->redirect(url('admin/requests/memo'));
            return;
        }

        $validation = validateDateRange($startDate, $endDate);
        if ($validation !== true) {
            Session::flash('error', $validation);
            $this->redirect(url('admin/requests/memo'));
            return;
        }

        // Get all requests
        $requests = RequestMemoModel::getAll();

        // Apply date range filter
        $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
            $createdDate = date('Y-m-d', strtotime($request->created_at));
            return $createdDate >= $startDate && $createdDate <= $endDate;
        });

        // Prepare data for export
        $headers = ['Request Number', 'Requester', 'Memo Content', 'Conveyor', 'Shift', 'Status', 'Created Date'];
        $data = [];

        foreach ($requests as $request) {
            // Get conveyor name
            $conveyor = ConveyorModel::findById($request->conveyor_id);
            $conveyorName = $conveyor ? $conveyor->conveyor_name : 'N/A';

            $data[] = [
                $request->request_number,
                $request->requester,
                substr($request->memo_content, 0, 100),
                $conveyorName,
                $request->shift ?? 'N/A',
                ucfirst($request->status),
                date('Y-m-d H:i:s', strtotime($request->created_at)),
            ];
        }

        // Export to Excel
        $filename = 'Request_Memo_' . date('Y-m-d_His');
        error_log("Exporting memo with filename: " . $filename . ", data rows: " . count($data));
        exportExcel($filename, $headers, $data);
    }
}
