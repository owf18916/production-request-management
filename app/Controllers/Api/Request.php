<?php

namespace App\Controllers\Api;

use App\Controller;
use App\Session;
use App\Models\MasterATK as MasterATKModel;
use App\Models\MasterChecksheet;
use App\Models\RequestID as RequestIDModel;

/**
 * API Request Controller
 */
class Request extends Controller
{
    /**
     * Get all requests (JSON)
     */
    public function index(): void
    {
        // TODO: Add API authentication

        // Get page and per_page from query params
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 15);

        // TODO: Fetch requests from database
        $requests = [
            [
                'id' => 1,
                'title' => 'Sample Request',
                'status' => 'pending',
                'priority' => 'medium',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->json([
            'success' => true,
            'data' => $requests,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($requests),
            ],
        ]);
    }

    /**
     * Store request (JSON)
     */
    public function store(): void
    {
        if ($this->method() !== 'POST') {
            $this->json(['error' => 'Invalid method'], 405);
        }

        // TODO: Add API authentication
        // TODO: Validate input
        // TODO: Create request in database

        $this->json([
            'success' => true,
            'message' => 'Request created successfully',
            'data' => [
                'id' => 1,
                'title' => 'New Request',
            ],
        ], 201);
    }

    /**
     * Search ATK items (JSON)
     */
    public function searchATK(): void
    {
        header('Content-Type: application/json');
        
        $query = $this->input('q', '');
        
        if (strlen($query) < 1) {
            echo json_encode(['results' => []]);
            exit;
        }

        $results = MasterATKModel::search($query);
        echo json_encode(['results' => $results]);
        exit;
    }

    /**
     * Search Checksheet items (JSON)
     */
    public function searchChecksheet(): void
    {
        header('Content-Type: application/json');
        
        $query = $this->input('q', '');
        
        if (strlen($query) < 1) {
            echo json_encode(['results' => []]);
            exit;
        }

        $results = MasterChecksheet::search($query);
        echo json_encode(['results' => $results]);
        exit;
    }

    /**
     * Search ID Types (JSON)
     */
    public function searchIDTypes(): void
    {
        header('Content-Type: application/json');
        
        $idTypes = RequestIDModel::VALID_ID_TYPES;
        $results = [];
        
        foreach ($idTypes as $type) {
            $results[] = [
                'id' => $type,
                'nama_id' => ucfirst(str_replace('_', ' ', $type))
            ];
        }
        
        echo json_encode(['results' => $results]);
        exit;
    }
}


