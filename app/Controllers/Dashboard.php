<?php

namespace App\Controllers;

use App\Controller;
use App\Session;
use App\Security;
use App\Models\Dashboard as DashboardModel;

/**
 * Dashboard Controller
 */
class Dashboard extends Controller
{
    /**
     * Show dashboard - routes to admin or PIC dashboard based on role
     */
    public function index(): void
    {
        // Check if user is logged in
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        $role = Session::get('user_role');
        
        // Route to appropriate dashboard based on role
        if ($role === 'admin') {
            $this->adminDashboard();
        } else {
            $this->picDashboard();
        }
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard(): void
    {
        // Check if user is logged in
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // Check if user is admin
        if (Session::get('user_role') !== 'admin') {
            Session::flash('error', 'You do not have permission to access this page');
            $this->redirect(url('dashboard'));
        }

        $this->setTitle('Admin Dashboard');
        $this->with('user_name', Session::get('user_name'))
             ->with('user_role', Session::get('user_role'));

        // Get statistics
        $stats = $this->getStatistics('admin', null);
        
        // Get recent requests
        $recentRequests = DashboardModel::getRecentRequestsAdmin(10);
        
        // Get status distribution
        $statusDistribution = DashboardModel::getStatusDistributionAdmin();
        
        // Get requests by type
        $requestsByType = DashboardModel::getRequestsByTypeAdmin();

        $this->with('stats', $stats)
             ->with('recentRequests', $recentRequests)
             ->with('statusDistribution', $statusDistribution)
             ->with('requestsByType', $requestsByType);

        $this->view('dashboard/admin');
    }

    /**
     * Show PIC dashboard
     */
    public function picDashboard(): void
    {
        // Check if user is logged in
        if (!Session::has('user_id')) {
            $this->redirect(url('login'));
        }

        // Check if user is PIC (not admin)
        if (Session::get('user_role') === 'admin') {
            $this->redirect(url('/dashboard/admin'));
        }

        $userId = Session::get('user_id');

        $this->setTitle('My Dashboard');
        $this->with('user_name', Session::get('user_name'))
             ->with('user_role', Session::get('user_role'));

        // Get statistics
        $stats = $this->getStatistics('pic', $userId);
        
        // Get recent own requests
        $recentRequests = DashboardModel::getRecentRequestsPIC($userId, 10);
        
        // Get status distribution
        $statusDistribution = DashboardModel::getStatusDistributionPIC($userId);
        
        // Get requests by type
        $requestsByType = DashboardModel::getRequestsByTypePIC($userId);

        $this->with('stats', $stats)
             ->with('recentRequests', $recentRequests)
             ->with('statusDistribution', $statusDistribution)
             ->with('requestsByType', $requestsByType);

        $this->view('dashboard/pic');
    }

    /**
     * Get dashboard statistics based on role
     */
    private function getStatistics(string $role, ?int $userId): array
    {
        if ($role === 'admin') {
            return [
                'pending' => DashboardModel::getTotalPendingRequestsAdmin(),
                'approved_today' => DashboardModel::getTotalApprovedTodayAdmin(),
                'rejected_today' => DashboardModel::getTotalRejectedTodayAdmin(),
                'completed_month' => DashboardModel::getTotalCompletedThisMonthAdmin(),
            ];
        } else {
            return [
                'pending' => DashboardModel::getTotalPendingRequestsPIC($userId),
                'approved' => DashboardModel::getTotalApprovedRequestsPIC($userId),
                'rejected' => DashboardModel::getTotalRejectedRequestsPIC($userId),
                'completed' => DashboardModel::getTotalCompletedRequestsPIC($userId),
            ];
        }
    }
}
