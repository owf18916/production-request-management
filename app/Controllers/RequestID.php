<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\RequestID as RequestIDModel;
use App\Models\Conveyor as ConveyorModel;

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

        $this->setTitle('My ID Requests');
        $this->view('request_id/index', [
            'requests' => $requests,
            'search' => $search,
            'idTypeFilter' => $idTypeFilter,
            'statusFilter' => $statusFilter,
            'totalCount' => count($requests),
        ]);
    }

    /**
     * PIC Create - Show form with ID type selector
     */
    public function create(): void
    {
        $conveyors = ConveyorModel::getActive();
        $shifts = ['Shift A', 'Shift B'];
        
        $this->setTitle('Request ID');
        $this->view('request_id/create', [
            'idTypes' => RequestIDModel::VALID_ID_TYPES,
            'idTypeFields' => self::ID_TYPE_FIELDS,
            'conveyors' => $conveyors,
            'shifts' => $shifts,
            'csrf_token' => Session::generateToken(),
        ]);
    }

    /**
     * PIC Store - Save request with dynamic fields
     */
    public function store(): void
    {
        if (!$this->validateCSRF()) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('/request-id/create'));
        }

        $idType = $this->input('id_type');
        $conveyorId = $this->input('conveyor_id');
        $shift = $this->input('shift');
        $userId = session('user_id');

        $errors = [];

        // Validate ID type
        if (!$idType || !in_array($idType, RequestIDModel::VALID_ID_TYPES)) {
            $errors['id_type'] = 'Invalid ID type selected';
        }

        // Validate dynamic fields
        if ($idType && isset(self::ID_TYPE_FIELDS[$idType])) {
            $fields = self::ID_TYPE_FIELDS[$idType];
            $details = [];

            foreach ($fields as $fieldName => $fieldConfig) {
                $fieldValue = $this->input($fieldName);

                if ($fieldConfig['required'] && !$fieldValue) {
                    $errors[$fieldName] = $fieldConfig['label'] . ' is required';
                }

                // Validate numeric fields
                if ($fieldName === 'qty' || $fieldName === 'panjang' || $fieldName === 'lebar') {
                    if ($fieldValue && !is_numeric($fieldValue)) {
                        $errors[$fieldName] = $fieldConfig['label'] . ' must be numeric';
                    }
                }

                if ($fieldValue) {
                    $details[$fieldName] = $fieldValue;
                }
            }
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
            $this->setTitle('Request ID');
            $this->view('request_id/create', [
                'errors' => $errors,
                'idTypes' => RequestIDModel::VALID_ID_TYPES,
                'idTypeFields' => self::ID_TYPE_FIELDS,
                'formData' => ['id_type' => $idType, 'conveyor_id' => $conveyorId, 'shift' => $shift] + ($details ?? []),
                'conveyors' => ConveyorModel::getActive(),
                'shifts' => ['Shift A', 'Shift B'],
                'csrf_token' => Session::generateToken(),
            ]);
            return;
        }

        // Generate request number
        $requestNumber = RequestIDModel::generateRequestNumber();

        // Create request
        $success = RequestIDModel::create([
            'request_number' => $requestNumber,
            'id_type' => $idType,
            'conveyor_id' => $conveyorId ? (int)$conveyorId : null,
            'shift' => $shift ?: null,
            'status' => 'pending',
            'requested_by' => $userId,
            'notes' => $this->input('notes'),
        ]);

        if ($success) {
            // Get the inserted ID
            $sql = "SELECT id FROM request_id WHERE request_number = ?";
            $result = \App\Database::row($sql, [$requestNumber]);
            $requestId = $result->id;

            // Save details
            RequestIDModel::saveDetails($requestId, $details);

            // Record initial history
            $historyQuery = "INSERT INTO request_id_history (request_id_id, status, changed_by, notes, created_at)
                            VALUES (?, ?, ?, ?, NOW())";
            \App\Database::query($historyQuery, [$requestId, 'pending', $userId, 'Request created']);

            Session::flash('success', 'ID Request created successfully. Request Number: ' . $requestNumber);
            $this->redirect(url('/request-id'));
        } else {
            Session::flash('error', 'Failed to create ID request');
            $this->redirect(url('/request-id/create'));
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
            $detailsArray[$detail->field_name] = $detail->field_value;
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
     * Admin Index - List all requests
     */
    public function adminIndex(): void
    {
        $search = $this->input('search', '');
        $idTypeFilter = $this->input('id_type', '');
        $statusFilter = $this->input('status', '');

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

        $this->setTitle('All ID Requests');
        $this->view('admin/request_id/admin_index', [
            'requests' => $requests,
            'search' => $search,
            'idTypeFilter' => $idTypeFilter,
            'statusFilter' => $statusFilter,
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
            $detailsArray[$detail->field_name] = $detail->field_value;
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
}
