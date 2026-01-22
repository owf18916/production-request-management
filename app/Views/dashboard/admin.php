<?php
$content = ob_get_clean();
ob_start();
?>

<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome back, <?php echo e($user_name ?? 'Admin'); ?>!</p>
        </div>

        <!-- Admin Menu Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Master Conveyor Management -->
            <a href="<?php echo url('admin/master/conveyor'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Master Conveyor</h3>
                        <p class="text-gray-600 text-sm mt-1">Manage conveyors and user assignments</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- User Management -->
            <a href="<?php echo url('admin/users'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">User Management</h3>
                        <p class="text-gray-600 text-sm mt-1">Manage system users and roles</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a3 3 0 003-3v-2a3 3 0 00-3-3H3a3 3 0 00-3 3v2a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- System Settings -->
            <a href="<?php echo url('admin/conveyors'); ?>" class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">System Settings</h3>
                        <p class="text-gray-600 text-sm mt-1">Configure system settings and options</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">-</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 10H9M9 20h6m0 0a9 9 0 110-18 9 9 0 010 18z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Conveyors -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Conveyors</p>
                        <p class="text-3xl font-bold text-gray-900">-</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">System Status</p>
                        <p class="text-3xl font-bold text-green-600">Active</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Admin Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Admin Users</p>
                        <p class="text-3xl font-bold text-gray-900">-</p>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
