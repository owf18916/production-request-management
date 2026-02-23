<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\RequestID as RequestIDModel;
use App\Models\Conveyor as ConveyorModel;
use App\Models\User as UserModel;

class RequestID extends Controller
{
    /**
     * ID Type configurations with fields
     */
    private const ID_TYPE_FIELDS = [
        'id_punggung' => [
            'job' => ['type' => 'text', 'label' => 'Job', 'required' => true],
            'keterangan' => ['type' => 'select', 'label' => 'Keterangan', 'options' => ['Asli', 'Perbaikan'], 'required' => true],
            'qty' => ['type' => 'number', 'label' => 'Qty', 'required' => true],
            'warna' => ['type' => 'text', 'label' => 'Warna', 'required' => true],
        ],
        'pin_4m' => [
            'nama' => ['type' => 'text', 'label' => 'Nama', 'required' => true],
            'nik' => ['type' => 'text', 'label' => 'NIK', 'required' => true],
            'matrix_skill' => ['type' => 'text', 'label' => 'Matrix Skill', 'required' => true],
            'keterangan' => ['type' => 'select', 'label' => 'Keterangan', 'options' => ['Bulat', 'Kotak'], 'required' => true],
            'pin' => ['type' => 'text', 'label' => 'PIN', 'required' => true],
            'qty' => ['type' => 'number', 'label' => 'Qty', 'required' => true],
        ],
        'id_kaki' => [
            'job' => ['type' => 'text', 'label' => 'Job', 'required' => true],
            'keterangan' => ['type' => 'select', 'label' => 'Keterangan', 'options' => ['Bulat', 'Kotak'], 'required' => true],
            'qty' => ['type' => 'number', 'label' => 'Qty', 'required' => true],
        ],
        'job_psd' => [
            'remarks' => ['type' => 'textarea', 'label' => 'Remarks', 'required' => true],
        ],
        'id_other' => [
            'nama_id' => ['type' => 'text', 'label' => 'Nama ID', 'required' => true],
            'panjang' => ['type' => 'number', 'label' => 'Panjang (cm)', 'required' => true],
            'lebar' => ['type' => 'number', 'label' => 'Lebar (cm)', 'required' => true],
        ],
    ];

    /**
     * PIC Index - List user's requests
     */
    public function index(): void
    {
        $userId = session('user_id');
        $search = $this->input('search', '');
        $idTypeFilter = $this->input('id_type', '');
        $statusFilter = $this->input('status', '');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');

        $requests = RequestIDModel::getByUser($userId);

        // Apply filters
        if ($idTypeFilter) {
            $requests = array_filter($requests, fn($r) => $r->id_type === $idTypeFilter);
        }
        if ($statusFilter) {
            $requests = array_filter($requests, fn($r) => $r->status === $statusFilter);
        }
        if ($search) {
            $requests = array_filter($requests, fn($r) => 
                stripos($r->request_number, $search) !== false || 
                stripos($r->id_type, $search) !== false
            );
        }
        if ($startDate && $endDate) {
            $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
                $createdDate = date('Y-m-d', strtotime($request->created_at));
                return $createdDate >= $startDate && $createdDate <= $endDate;
            });
        }

        $this->setTitle('My ID Requests');
        $this->view('request_id/index', [
            'requests' => $requests,
            'search' => $search,
            'idTypeFilter' => $idTypeFilter,
            'statusFilter' => $statusFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * PIC Create - Show form with ID type selector
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
        
        $this->setTitle('Request ID');
        $this->view('request_id/create', [
            'idTypes' => RequestIDModel::VALID_ID_TYPES,
            'idTypeFields' => self::ID_TYPE_FIELDS,
            'csrf_token' => Session::generateToken(),
            'active_conveyor_id' => $activeConveyorId,
            'active_conveyor_name' => $activeConveyorName,
            'active_shift' => $activeShift,
        ]);
    }

    /**
     * PIC Store - Save request with multiple ID items
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
            $this->redirect(url('request-id/create'), 'error', 'Invalid request');
        }

        // Validate that conveyor and shift are setup
        if (!Session::hasActiveConveyorAndShift()) {
            $this->redirect(url('request-id/create'), 'error', 'Conveyor dan Shift belum di-setup');
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
                if (empty($item['id_type']) || !in_array($item['id_type'], RequestIDModel::VALID_ID_TYPES)) {
                    $errors["items.{$index}.id_type"] = "Item " . ($index + 1) . ": ID Type harus valid";
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
            
            $this->setTitle('Request ID');
            $this->view('request_id/create', [
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
            $requestNumber = RequestIDModel::generateRequestNumber();
            
            $success = RequestIDModel::create([
                'request_number' => $requestNumber,
                'id_type' => $item['id_type'],
                'conveyor_id' => $conveyorId,
                'shift' => $shift,
                'status' => 'pending',
                'requested_by' => $userId,
                'notes' => $item['notes'] ?? null,
            ]);

            if (!$success) {
                $allSuccess = false;
                $insertErrors[] = "Item " . ($index + 1) . " gagal disimpan";
                error_log("RequestID store - Failed to insert item " . ($index + 1) . " for request " . $requestNumber);
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
                    'redirect' => url('request-id')
                ]);
                return;
            }
            
            $this->redirect(url('request-id'), 'success', 'Request dengan ' . count($items) . ' item berhasil dibuat');
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
            
            $this->redirect(url('request-id/create'), 'error', 'Gagal membuat request: ' . implode(', ', $insertErrors));
        }
    }

    /**
     * PIC Show - View detail with history
     */
    public function show(int $id): void
    {
        $userId = session('user_id');
        $request = RequestIDModel::findById($id);

        if (!$request) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/request-id'));
        }

        // Check authorization - PIC can only view own requests
        if ($request->requested_by !== $userId) {
            $this->redirect(url('/request-id'), 'error', 'Unauthorized access');
        }

        $history = RequestIDModel::getHistory($id);
        $details = RequestIDModel::getDetails($id);

        // Convert details to associative array
        $detailsArray = [];
        foreach ($details as $detail) {
            $detailsArray[$detail->detail_key] = $detail->detail_value;
        }

        $this->setTitle('Request ID Detail');
        $this->view('request_id/show', [
            'request' => $request,
            'history' => $history,
            'details' => $detailsArray,
            'idTypeFields' => self::ID_TYPE_FIELDS,
        ]);
    }

    /**
     * Cancel - Cancel own pending request
     */
    public function cancel(int $id): void
    {
        $userId = session('user_id');
        $request = RequestIDModel::findById($id);

        if (!$request) {
            $this->redirect(url('/request-id'), 'error', 'Request not found');
        }

        // Check authorization - PIC can only cancel own requests
        if ($request->requested_by !== $userId) {
            $this->redirect(url('/request-id'), 'error', 'Unauthorized access');
        }

        // Check if request can be cancelled (only pending can be cancelled)
        if ($request->status === 'approved') {
            $this->redirect(url("request-id/{$id}"), 'error', 'Cannot cancel an approved request');
        }

        if ($request->status === 'completed') {
            $this->redirect(url("request-id/{$id}"), 'error', 'Cannot cancel a completed request');
        }

        if ($request->status === 'cancelled') {
            $this->redirect(url("request-id/{$id}"), 'error', 'Request is already cancelled');
        }

        // Update status to cancelled with cancellation note
        $success = RequestIDModel::updateStatus(
            $id,
            'cancelled',
            $userId,
            'Cancelled by requester'
        );

        if ($success) {
            $this->redirect(url("request-id/{$id}"), 'success', 'Request cancelled successfully');
        } else {
            $this->redirect(url("request-id/{$id}"), 'error', 'Failed to cancel request');
        }
    }

    /**
     * Admin Index - List all requests
     */
    public function adminIndex(): void
    {
        $search = $this->input('search', '');
        $idTypeFilter = $this->input('id_type', '');
        $statusFilter = $this->input('status', '');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');

        $requests = RequestIDModel::getAll();

        // Apply filters
        if ($idTypeFilter) {
            $requests = array_filter($requests, fn($r) => $r->id_type === $idTypeFilter);
        }
        if ($statusFilter) {
            $requests = array_filter($requests, fn($r) => $r->status === $statusFilter);
        }
        if ($search) {
            $requests = array_filter($requests, fn($r) => 
                stripos($r->request_number, $search) !== false || 
                stripos($r->requester, $search) !== false
            );
        }

        // Apply date range filter
        if ($startDate && $endDate) {
            $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
                $createdDate = date('Y-m-d', strtotime($request->created_at));
                return $createdDate >= $startDate && $createdDate <= $endDate;
            });
        }

        $this->setTitle('All ID Requests');
        $this->view('admin/request_id/admin_index', [
            'requests' => $requests,
            'search' => $search,
            'idTypeFilter' => $idTypeFilter,
            'statusFilter' => $statusFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalCount' => count($requests),
            'idTypes' => RequestIDModel::VALID_ID_TYPES,
        ]);
    }

    /**
     * Admin Show - View detail with history
     */
    public function adminShow(int $id): void
    {
        $request = RequestIDModel::findById($id);

        if (!$request) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/admin/request-id'));
        }

        $history = RequestIDModel::getHistory($id);
        $details = RequestIDModel::getDetails($id);

        // Convert details to associative array
        $detailsArray = [];
        foreach ($details as $detail) {
            $detailsArray[$detail->detail_key] = $detail->detail_value;
        }

        $this->setTitle('Request ID Detail');
        $this->view('admin/request_id/admin_show', [
            'request' => $request,
            'history' => $history,
            'details' => $detailsArray,
            'idTypeFields' => self::ID_TYPE_FIELDS,
        ]);
    }

    /**
     * Admin Update Status
     */
    public function updateStatus(int $id): void
    {
        if (!$this->validateCSRF()) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/admin/request-id/' . $id));
        }

        $request = RequestIDModel::findById($id);
        if (!$request) {
            Session::flash('error', 'Request not found');
            $this->redirect(url('/admin/request-id'));
        }

        $newStatus = $this->input('status');
        $notes = $this->input('notes', '');
        $userId = session('user_id');

        // Validate status
        if (!in_array($newStatus, RequestIDModel::VALID_STATUSES)) {
            Session::flash('error', 'Invalid status');
            $this->redirect(url('/admin/request-id/' . $id));
        }

        // Update status
        $success = RequestIDModel::updateStatus($id, $newStatus, $userId, $notes);

        if ($success) {
            Session::flash('success', 'Request status updated successfully');
            $this->redirect(url('/admin/request-id/' . $id));
        } else {
            Session::flash('error', 'Failed to update request status');
            $this->redirect(url('/admin/request-id/' . $id));
        }
    }

    /**
     * CSRF Validation helper (using Session::verifyToken)
     */
    private function validateCSRF(): bool
    {
        $csrfToken = $this->input('_csrf_token');
        return Session::verifyToken($csrfToken);
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
        $userId = session('user_id');
        $userRole = Session::get('user_role');
        
        if (!$userId) {
            $this->redirect(url('login'));
        }

        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        // Validate date range
        if (!$startDate || !$endDate) {
            Session::flash('error', 'Tanggal mulai dan akhir harus diisi');
            $this->redirect(url('admin/request-id'));
            return;
        }

        $validation = validateDateRange($startDate, $endDate);
        if ($validation !== true) {
            Session::flash('error', $validation);
            $this->redirect(url('admin/request-id'));
            return;
        }

        // Get requests based on role
        if ($userRole === 'admin') {
            $requests = RequestIDModel::getAll();
        } else {
            $requests = RequestIDModel::getByUser($userId);
        }

        // Apply date range filter
        $requests = array_filter($requests, function($request) use ($startDate, $endDate) {
            $createdDate = date('Y-m-d', strtotime($request->created_at));
            return $createdDate >= $startDate && $createdDate <= $endDate;
        });

        // Prepare data for export
        $headers = ['Request Number', 'ID Type', 'Conveyor', 'Shift', 'Status', 'Requester', 'Created Date'];
        $data = [];

        $typeLabels = [
            'id_punggung' => 'ID Punggung',
            'pin_4m' => 'PIN 4M',
            'id_kaki' => 'ID Kaki',
            'job_psd' => 'Job PSD',
            'id_other' => 'ID Other'
        ];

        foreach ($requests as $request) {
            // Get conveyor name
            $conveyor = ConveyorModel::findById($request->conveyor_id);
            $conveyorName = $conveyor ? $conveyor->conveyor_name : 'N/A';

            // Get requester name
            $requester = UserModel::getUserById($request->requested_by);
            $requesterName = $requester ? $requester->full_name : 'N/A';

            $idTypeLabel = $typeLabels[$request->id_type] ?? $request->id_type;

            $data[] = [
                $request->request_number,
                $idTypeLabel,
                $conveyorName,
                $request->shift ?? 'N/A',
                ucfirst($request->status),
                $requesterName,
                date('Y-m-d H:i:s', strtotime($request->created_at)),
            ];
        }

        // Export to Excel
        $filename = 'Request_ID_' . date('Y-m-d_His');
        exportExcel($filename, $headers, $data);
    }
}
