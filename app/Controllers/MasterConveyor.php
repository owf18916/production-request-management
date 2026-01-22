<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\Conveyor;
use App\Models\User;
use App\Database;

/**
 * Master Conveyor Controller
 * Handles master conveyor management for admin users
 */
class MasterConveyor extends Controller
{
    /**
     * List all conveyors with assigned users count
     */
    public function index(): void
    {
        $search = Security::sanitize($this->input('search') ?? '', 'string');
        $status = Security::sanitize($this->input('status') ?? '', 'string');
        $page = (int) ($this->input('page') ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Build query
        $sql = "SELECT mc.id, mc.conveyor_name, mc.status, mc.created_by, 
                u.full_name as created_by_name, mc.created_at, mc.updated_at,
                COUNT(uc.id) as users_count
                FROM master_conveyor mc
                JOIN users u ON mc.created_by = u.id
                LEFT JOIN user_conveyor uc ON mc.id = uc.conveyor_id
                WHERE 1=1";

        $params = [];

        // Search filter
        if (!empty($search)) {
            $sql .= " AND mc.conveyor_name LIKE ?";
            $params[] = "%$search%";
        }

        // Status filter
        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $sql .= " AND mc.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY mc.id ORDER BY mc.conveyor_name ASC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        // Get conveyors with pagination
        $conveyors = Database::results($sql, $params);

        // Get total count for pagination
        $countSql = "SELECT COUNT(DISTINCT mc.id) as total FROM master_conveyor mc
                     WHERE 1=1";
        $countParams = [];

        if (!empty($search)) {
            $countSql .= " AND mc.conveyor_name LIKE ?";
            $countParams[] = "%$search%";
        }

        if (!empty($status) && in_array($status, ['active', 'inactive'])) {
            $countSql .= " AND mc.status = ?";
            $countParams[] = $status;
        }

        $countResult = Database::row($countSql, $countParams);
        $total = $countResult->total;
        $totalPages = ceil($total / $perPage);

        $this->setTitle('Master Conveyor - Production Request Management');
        $this->view('master/conveyor/index', [
            'conveyors' => $conveyors,
            'search' => $search,
            'status' => $status,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->setTitle('Add New Conveyor - Production Request Management');
        $this->view('master/conveyor/create');
    }

    /**
     * Save new conveyor (POST)
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('admin/master/conveyor'));
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('admin/master/conveyor/create'));
        }

        // Validate input
        $errors = $this->validate([
            'conveyor_name' => 'required|min:3|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url('admin/master/conveyor/create'));
        }

        $conveyorName = Security::sanitize($this->input('conveyor_name') ?? '', 'string');
        $status = Security::sanitize($this->input('status') ?? '', 'string');

        // Check if conveyor name is unique
        if (!Conveyor::isUniqueConveyorName($conveyorName)) {
            Session::flash('error', 'Conveyor name already exists. Please use a different name.');
            Session::flash('old_input', [
                'conveyor_name' => $conveyorName,
                'status' => $status,
            ]);
            $this->redirect(url('admin/master/conveyor/create'));
        }

        // Create new conveyor
        $data = [
            'conveyor_name' => $conveyorName,
            'status' => $status,
            'created_by' => Session::get('user_id'),
        ];

        if (Conveyor::create($data)) {
            Session::flash('success', 'Conveyor created successfully!');
            $this->redirect(url('admin/master/conveyor'));
        } else {
            Session::flash('error', 'Failed to create conveyor. Please try again.');
            $this->redirect(url('admin/master/conveyor/create'));
        }
    }

    /**
     * Show edit form
     */
    public function edit($id): void
    {
        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        $this->setTitle('Edit Conveyor - Production Request Management');
        $this->view('master/conveyor/edit', ['conveyor' => $conveyor]);
    }

    /**
     * Update conveyor (POST)
     */
    public function update($id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('admin/master/conveyor'));
        }

        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("admin/master/conveyor/edit/$id"));
        }

        // Validate input
        $errors = $this->validate([
            'conveyor_name' => 'required|min:3|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Please correct the errors below');
            $this->redirect(url("admin/master/conveyor/edit/$id"));
        }

        $conveyorName = Security::sanitize($this->input('conveyor_name') ?? '', 'string');
        $status = Security::sanitize($this->input('status') ?? '', 'string');

        // Check if conveyor name is unique (except current)
        if (!Conveyor::isUniqueConveyorName($conveyorName, $id)) {
            Session::flash('error', 'Conveyor name already exists. Please use a different name.');
            Session::flash('old_input', [
                'conveyor_name' => $conveyorName,
                'status' => $status,
            ]);
            $this->redirect(url("admin/master/conveyor/edit/$id"));
        }

        // Update conveyor
        $data = [
            'conveyor_name' => $conveyorName,
            'status' => $status,
        ];

        if (Conveyor::update($id, $data)) {
            Session::flash('success', 'Conveyor updated successfully!');
            $this->redirect(url('admin/master/conveyor'));
        } else {
            Session::flash('error', 'Failed to update conveyor. Please try again.');
            $this->redirect(url("admin/master/conveyor/edit/$id"));
        }
    }

    /**
     * Delete conveyor (POST)
     */
    public function delete($id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('admin/master/conveyor'));
        }

        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url('admin/master/conveyor'));
        }

        // Check if conveyor has assigned users
        if (Conveyor::hasUsers($id)) {
            Session::flash('error', 'Cannot delete conveyor. It has assigned users. Please remove all users first.');
            $this->redirect(url('admin/master/conveyor'));
        }

        // Delete conveyor
        if (Conveyor::delete($id)) {
            Session::flash('success', 'Conveyor deleted successfully!');
            $this->redirect(url('admin/master/conveyor'));
        } else {
            Session::flash('error', 'Failed to delete conveyor. Please try again.');
            $this->redirect(url('admin/master/conveyor'));
        }
    }

    /**
     * Show user assignment page
     */
    public function manageUsers($id): void
    {
        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        // Get currently assigned users
        $assignedUsers = Conveyor::getConveyorUsers($id);

        // Get available users (not yet assigned)
        $availableUsers = Conveyor::getUsersNotInConveyor($id);

        $this->setTitle("Manage Users - {$conveyor->conveyor_name}");
        $this->view('master/conveyor/manage_users', [
            'conveyor' => $conveyor,
            'assignedUsers' => $assignedUsers,
            'availableUsers' => $availableUsers,
        ]);
    }

    /**
     * Assign multiple users to conveyor (POST)
     */
    public function assignUsers($id): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('admin/master/conveyor'));
        }

        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        }

        // Get user IDs
        $userIds = $this->input('user_ids') ?? [];
        if (!is_array($userIds) || empty($userIds)) {
            Session::flash('error', 'Please select at least one user.');
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        }

        // Validate user IDs
        $validUserIds = [];
        foreach ($userIds as $userId) {
            $userId = (int) $userId;
            $user = User::getUserById($userId);
            if ($user) {
                $validUserIds[] = $userId;
            }
        }

        if (empty($validUserIds)) {
            Session::flash('error', 'Invalid user selection. Please try again.');
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        }

        // Get already assigned users
        $assignedUsers = Conveyor::getConveyorUsers($id);
        $assignedUserIds = array_map(function($user) {
            return $user->id;
        }, $assignedUsers);

        // Filter out already assigned users
        $newUserIds = array_diff($validUserIds, $assignedUserIds);

        if (empty($newUserIds)) {
            Session::flash('error', 'Selected users are already assigned to this conveyor.');
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        }

        // Insert new assignments
        $assigned = 0;
        foreach ($newUserIds as $userId) {
            $sql = "INSERT INTO user_conveyor (user_id, conveyor_id, created_at) 
                    VALUES (?, ?, NOW())";
            if (Database::query($sql, [$userId, $id])) {
                $assigned++;
            }
        }

        if ($assigned > 0) {
            Session::flash('success', "Successfully assigned $assigned user(s) to conveyor!");
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        } else {
            Session::flash('error', 'Failed to assign users. Please try again.');
            $this->redirect(url("admin/master/conveyor/manage-users/$id"));
        }
    }

    /**
     * Remove user from conveyor (POST)
     */
    public function removeUser($conveyorId, $userId): void
    {
        if ($this->method() !== 'POST') {
            $this->redirect(url('admin/master/conveyor'));
        }

        $conveyorId = (int) $conveyorId;
        $userId = (int) $userId;

        $conveyor = Conveyor::findById($conveyorId);
        if (!$conveyor) {
            http_response_code(404);
            $this->setTitle('Conveyor Not Found');
            $this->view('404');
            return;
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            $this->redirect(url("admin/master/conveyor/manage-users/$conveyorId"));
        }

        // Check if relationship exists
        $sql = "SELECT id FROM user_conveyor WHERE user_id = ? AND conveyor_id = ?";
        $relationship = Database::row($sql, [$userId, $conveyorId]);

        if (!$relationship) {
            Session::flash('error', 'User is not assigned to this conveyor.');
            $this->redirect(url("admin/master/conveyor/manage-users/$conveyorId"));
        }

        // Delete relationship
        $deleteSql = "DELETE FROM user_conveyor WHERE user_id = ? AND conveyor_id = ?";
        if (Database::query($deleteSql, [$userId, $conveyorId])) {
            Session::flash('success', 'User removed from conveyor successfully!');
            $this->redirect(url("admin/master/conveyor/manage-users/$conveyorId"));
        } else {
            Session::flash('error', 'Failed to remove user. Please try again.');
            $this->redirect(url("admin/master/conveyor/manage-users/$conveyorId"));
        }
    }

    /**
     * Toggle active/inactive status (POST)
     */
    public function toggleStatus($id): void
    {
        if ($this->method() !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 400);
        }

        $id = (int) $id;
        $conveyor = Conveyor::findById($id);

        if (!$conveyor) {
            $this->json(['success' => false, 'message' => 'Conveyor not found'], 404);
        }

        // Validate CSRF token
        $csrfToken = $this->input('_csrf_token') ?? '';
        if (!Session::verifyToken($csrfToken)) {
            $this->json(['success' => false, 'message' => 'Security token expired'], 403);
        }

        // Toggle status
        $newStatus = $conveyor->status === 'active' ? 'inactive' : 'active';

        if (Conveyor::update($id, ['status' => $newStatus])) {
            $this->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'new_status' => $newStatus,
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }
}
