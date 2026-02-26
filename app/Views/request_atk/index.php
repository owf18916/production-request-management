<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Request ATK</h1>
                    <p class="mt-2 text-gray-600">Manage your ATK (Alat Tulis Kantor) requests</p>
                </div>
                <a href="<?php echo url('requests/atk/create'); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $statusCounts['pending'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $statusCounts['approved'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $statusCounts['rejected'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $statusCounts['completed'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-xs">
                        <input type="text" name="search" placeholder="Search by request number or item name..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                    <a href="<?php echo url('requests/atk?clear_filters=1'); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Reset</a>
                </div>
            </form>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($requests)): ?>
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No requests</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new request.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conveyor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm font-medium text-gray-900"><?php echo $request->request_number; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->nama_barang ?? '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->qty; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->conveyor_name ?? '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->shift ?? '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ];
                                    $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                    $label = $statusLabels[$request->status] ?? ucfirst($request->status);
                                    ?>
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                        <?php echo $label; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo date('d/m/Y', strtotime($request->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo url("requests/atk/show/{$request->id}"); ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Pagination Info -->
        <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-gray-700">
                <span class="font-medium">
                    Showing <?php echo $pagination->getStartItem(); ?> to <?php echo $pagination->getEndItem(); ?> of <?php echo $totalCount; ?> results
                </span>
            </div>
            
            <!-- Pagination Navigation -->
            <?php if ($pagination->getTotalPages() > 1): ?>
            <nav class="mt-4 md:mt-0 flex items-center justify-center space-x-2">
                <!-- Previous Button -->
                <?php if ($pagination->hasPreviousPage()): ?>
                    <a href="<?php echo url('requests/atk?page=' . $pagination->getPreviousPage() . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($statusFilter ?? '') . '&start_date=' . urlencode($startDate ?? '') . '&end_date=' . urlencode($endDate ?? '')); ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        ← Previous
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed">
                        ← Previous
                    </span>
                <?php endif; ?>

                <!-- Page Numbers -->
                <div class="flex items-center space-x-1">
                    <?php 
                    $pageRange = $pagination->getPageRange(5);
                    $currentPage = $pagination->getCurrentPage();
                    
                    // Show first page if not in range
                    if ($pageRange[0] > 1): ?>
                        <a href="<?php echo url('requests/atk?page=1&search=' . urlencode($search ?? '') . '&status=' . urlencode($statusFilter ?? '') . '&start_date=' . urlencode($startDate ?? '') . '&end_date=' . urlencode($endDate ?? '')); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            1
                        </a>
                        <?php if ($pageRange[0] > 2): ?>
                            <span class="px-2 py-2 text-gray-700">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php foreach ($pageRange as $page): ?>
                        <?php if ($page === $currentPage): ?>
                            <span class="px-3 py-2 border border-blue-500 rounded-md text-sm font-medium text-white bg-blue-600">
                                <?php echo $page; ?>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo url('requests/atk?page=' . $page . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($statusFilter ?? '') . '&start_date=' . urlencode($startDate ?? '') . '&end_date=' . urlencode($endDate ?? '')); ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <?php echo $page; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Show last page if not in range -->
                    <?php if ($pageRange[count($pageRange) - 1] < $pagination->getTotalPages()): ?>
                        <?php if ($pageRange[count($pageRange) - 1] < $pagination->getTotalPages() - 1): ?>
                            <span class="px-2 py-2 text-gray-700">...</span>
                        <?php endif; ?>
                        <a href="<?php echo url('requests/atk?page=' . $pagination->getTotalPages() . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($statusFilter ?? '') . '&start_date=' . urlencode($startDate ?? '') . '&end_date=' . urlencode($endDate ?? '')); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <?php echo $pagination->getTotalPages(); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Next Button -->
                <?php if ($pagination->hasNextPage()): ?>
                    <a href="<?php echo url('requests/atk?page=' . $pagination->getNextPage() . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($statusFilter ?? '') . '&start_date=' . urlencode($startDate ?? '') . '&end_date=' . urlencode($endDate ?? '')); ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Next →
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed">
                        Next →
                    </span>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
