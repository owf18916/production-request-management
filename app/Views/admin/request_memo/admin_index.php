<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Internal Memo Request Management</h1>
                    <p class="mt-2 text-gray-600">Review and manage all internal memo requests</p>
                </div>
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
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['pending'] ?? 0; ?></dd>
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
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['approved'] ?? 0; ?></dd>
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
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['rejected'] ?? 0; ?></dd>
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
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['completed'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <form method="GET" action="<?php echo url('/admin/requests/memo'); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" placeholder="Request # or content..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($status === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo ($status === 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo ($status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-4 flex gap-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Filter
                    </button>
                    <a href="<?php echo url('/admin/requests/memo'); ?>" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium text-center">
                        Clear Filters
                    </a>
                </div>
            </form>

            <!-- Export Button -->
            <?php if (!empty($startDate) && !empty($endDate)): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="<?php echo url('admin/requests/memo/export?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export to Excel
                    </a>
                </div>
            <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending']; ?></div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-2xl font-bold text-green-600"><?php echo $stats['approved']; ?></div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-2xl font-bold text-red-600"><?php echo $stats['rejected']; ?></div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-2xl font-bold text-blue-600"><?php echo $stats['completed']; ?></div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <?php if ($requests): ?>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Request #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Submitted By</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Content Preview</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Conveyor</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Shift</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($requests as $req): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-medium text-xs text-gray-900"><?php echo $req->request_number; ?></span>
                                </td>
                                <td class="px-4 py-3 whitespace-normal text-xs text-gray-700" style="max-width: 120px;">
                                    <?php echo $req->requester; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-600" style="max-width: 180px; word-break: break-word;">
                                        <?php echo substr($req->memo_content, 0, 100); ?>...
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                                    <?php echo $req->conveyor_name ?? '-'; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                                    <?php echo $req->shift ?? '-'; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        <?php if ($req->status === 'pending'): ?>
                                            bg-yellow-100 text-yellow-800
                                        <?php elseif ($req->status === 'approved'): ?>
                                            bg-green-100 text-green-800
                                        <?php elseif ($req->status === 'rejected'): ?>
                                            bg-red-100 text-red-800
                                        <?php else: ?>
                                            bg-blue-100 text-blue-800
                                        <?php endif; ?>
                                    ">
                                        <?php echo ucfirst($req->status); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                                    <?php echo date('M d, Y', strtotime($req->created_at)); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs">
                                    <a href="<?php echo url("/admin/requests/memo/show/{$req->id}"); ?>" class="text-blue-600 hover:text-blue-900 font-medium">Review</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No requests found</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
