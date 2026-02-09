<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        <?php if (session('error')): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <span class="font-medium">Error:</span> <?php echo e(session('error')); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session('success')): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            <span class="font-medium">Success:</span> <?php echo e(session('success')); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome back, <?php echo e($user_name ?? 'User'); ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Requests</p>
                        <p class="text-3xl font-bold text-gray-900">12</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold text-gray-900">5</p>
                    </div>
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold text-gray-900">4</p>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-gray-900">3</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Modules -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Modules</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Request ATK Card -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Request ATK</h3>
                                <p class="text-sm text-gray-500">Office Stationery</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Request office supplies and track approval status.</p>
                    <div class="flex gap-2">
                        <?php if (session('user_role') === 'admin'): ?>
                            <a href="<?php echo url('admin/requests/atk'); ?>" class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-2 rounded text-sm font-medium text-center">
                                View Requests
                            </a>
                        <?php else: ?>
                            <a href="<?php echo url('requests/atk'); ?>" class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-2 rounded text-sm font-medium text-center">
                                View Requests
                            </a>
                            <a href="<?php echo url('requests/atk/create'); ?>" class="flex-1 bg-blue-600 text-white hover:bg-blue-700 px-3 py-2 rounded text-sm font-medium text-center">
                                New Request
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Request Checksheet Card -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Checksheet</h3>
                                <p class="text-sm text-gray-500">Equipment Verification</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Submit and track checksheet verification forms.</p>
                    <div class="flex gap-2">
                        <a href="#" class="flex-1 bg-green-50 text-green-600 hover:bg-green-100 px-3 py-2 rounded text-sm font-medium text-center">
                            View Requests
                        </a>
                        <a href="#" class="flex-1 bg-green-600 text-white hover:bg-green-700 px-3 py-2 rounded text-sm font-medium text-center">
                            New Request
                        </a>
                    </div>
                </div>

                <!-- Request ID Card -->
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v10a2 2 0 002 2h5m4-16h5a2 2 0 012 2v10a2 2 0 01-2 2h-5m-4-4h.01"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Request ID</h3>
                                <p class="text-sm text-gray-500">Employee ID Card</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Request and track employee ID card issuance.</p>
                    <div class="flex gap-2">
                        <a href="#" class="flex-1 bg-purple-50 text-purple-600 hover:bg-purple-100 px-3 py-2 rounded text-sm font-medium text-center">
                            View Requests
                        </a>
                        <a href="#" class="flex-1 bg-purple-600 text-white hover:bg-purple-700 px-3 py-2 rounded text-sm font-medium text-center">
                            New Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
