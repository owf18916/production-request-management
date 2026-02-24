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
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

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

        // Apply date range filter
        if ($startDate && $endDate) {
            $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
                $createdDate = date('Y-m-d', strtotime($request->created_at));
                return $createdDate >= $startDate && $createdDate <= $endDate;
            });
        }

        $this->setTitle('Request ATK');
        $this->view('request_atk/index', [
            'requests' => array_values($requests),
            'search' => $search,
            'statusFilter' => $statusFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

        // Check if user has setup conveyor and shift
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('dashboard'), 'error', 'Harap setup Conveyor dan Shift terlebih dahulu di Dashboard');
        }

        $activeConveyorId = Session::getActiveConveyorId();
        $activeConveyorName = Session::getActiveConveyorName();
        $activeShift = Session::getActiveShift();

        $this->setTitle('Create Request ATK');
        $this->view('request_atk/create', [
            'csrf_token' => Session::generateToken(),
            'active_conveyor_id' => $activeConveyorId,
            'active_conveyor_name' => $activeConveyorName,
            'active_shift' => $activeShift,
        ]);
    }

    /**
     * Store - Save new request with multiple items (PIC)
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
            $this->redirect(url('requests/atk/create'), 'error', 'Invalid request');
        }

        // Validate that conveyor and shift are setup
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('requests/atk/create'), 'error', 'Conveyor dan Shift belum di-setup');
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
                if (empty($item['atk_id']) || !is_numeric($item['atk_id'])) {
                    $errors["items.{$index}.atk_id"] = "Item " . ($index + 1) . ": ATK item harus dipilih";
                } else {
                    $atk = MasterATKModel::findById((int)$item['atk_id']);
                    if (!$atk) {
                        $errors["items.{$index}.atk_id"] = "Item " . ($index + 1) . ": ATK item tidak valid";
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
            
            $this->setTitle('Create Request ATK');
            $this->view('request_atk/create', [
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
            $requestNumber = RequestATKModel::generateRequestNumber();
            
            $success = RequestATKModel::create([
                'request_number' => $requestNumber,
                'atk_id' => (int)$item['atk_id'],
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
                error_log("RequestATK store - Failed to insert item " . ($index + 1) . " for request " . $requestNumber);
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
                    'redirect' => url('requests/atk')
                ]);
                return;
            }
            
            $this->redirect(url('requests/atk'), 'success', 'Request dengan ' . count($items) . ' item berhasil dibuat');
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
            
            $this->redirect(url('requests/atk/create'), 'error', 'Gagal membuat request: ' . implode(', ', $insertErrors));
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
     * Cancel - Cancel own pending request (PIC)
     */
    public function cancel(int $id): void
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        $request = RequestATKModel::findById($id);

        if (!$request) {
            $this->redirect(url('requests/atk'), 'error', 'Request not found');
        }

        // Check authorization - PIC can only cancel own requests
        if ($request->requested_by != $userId) {
            $this->redirect(url('requests/atk'), 'error', 'Unauthorized access');
        }

        // Check if request can be cancelled (only pending can be cancelled)
        if ($request->status === 'approved') {
            $this->redirect(url("requests/atk/show/{$id}"), 'error', 'Cannot cancel an approved request');
        }

        if ($request->status === 'completed') {
            $this->redirect(url("requests/atk/show/{$id}"), 'error', 'Cannot cancel a completed request');
        }

        if ($request->status === 'cancelled') {
            $this->redirect(url("requests/atk/show/{$id}"), 'error', 'Request is already cancelled');
        }

        // Update status to cancelled with cancellation note
        $success = RequestATKModel::updateStatus(
            $id,
            'cancelled',
            $userId,
            'Cancelled by requester'
        );

        if ($success) {
            $this->redirect(url("requests/atk/show/{$id}"), 'success', 'Request cancelled successfully');
        } else {
            $this->redirect(url("requests/atk/show/{$id}"), 'error', 'Failed to cancel request');
        }
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
            $validNextStatuses = ['approved', 'rejected'];
        } elseif ($currentStatus === 'approved') {
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

    /**
     * Export - Export requests to Excel
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
            $this->redirect(url('admin/requests/atk'));
            return;
        }

        $validation = validateDateRange($startDate, $endDate);
        if ($validation !== true) {
            Session::flash('error', $validation);
            $this->redirect(url('admin/requests/atk'));
            return;
        }

        // Get requests based on role
        if ($userRole === 'admin') {
            $requests = RequestATKModel::getAll();
        } else {
            $requests = RequestATKModel::getByUser($userId);
        }

        // Apply date range filter
        $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
            $createdDate = date('Y-m-d', strtotime($request->created_at));
            return $createdDate >= $startDate && $createdDate <= $endDate;
        });

        // Prepare data for export
        $headers = ['Request Number', 'ATK Name', 'Qty', 'Conveyor', 'Shift', 'Status', 'Requester', 'Created Date'];
        $data = [];

        foreach ($requests as $request) {
            // Get ATK name
            $atk = MasterATKModel::findById($request->atk_id);
            $atkName = $atk ? $atk->nama_barang : 'N/A';

            // Get conveyor name
            $conveyor = ConveyorModel::findById($request->conveyor_id);
            $conveyorName = $conveyor ? $conveyor->conveyor_name : 'N/A';

            // Get requester name
            $requester = UserModel::getUserById($request->requested_by);
            $requesterName = $requester ? $requester->full_name : 'N/A';

            $data[] = [
                $request->request_number,
                $atkName,
                $request->qty,
                $conveyorName,
                $request->shift ?? 'N/A',
                ucfirst($request->status),
                $requesterName,
                date('Y-m-d H:i:s', strtotime($request->created_at)),
            ];
        }

        // Export to Excel
        $filename = 'Request_ATK_' . date('Y-m-d_His');
        error_log("Exporting with filename: " . $filename . ", data rows: " . count($data));
        exportExcel($filename, $headers, $data);
    }
}
