<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">ID Requests Management</h1>
            <p class="mt-2 text-gray-600">Review and manage all ID requests from users</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-gray-900"><?php echo $totalCount; ?></div>
                <div class="text-sm text-gray-600">Total Requests</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-yellow-600">
                    <?php 
                        $pendingCount = count(array_filter($requests, fn($r) => $r->status === 'pending'));
                        echo $pendingCount;
                    ?>
                </div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-green-600">
                    <?php 
                        $approvedCount = count(array_filter($requests, fn($r) => $r->status === 'approved'));
                        echo $approvedCount;
                    ?>
                </div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-red-600">
                    <?php 
                        $rejectedCount = count(array_filter($requests, fn($r) => $r->status === 'rejected'));
                        echo $rejectedCount;
                    ?>
                </div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="<?php echo url('/admin/request-id'); ?>" class="space-y-4">
                <div class="flex gap-4 flex-wrap">
                    <input type="text" name="search" placeholder="Search request number or user..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-xs">
                    
                    <select name="id_type" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All ID Types</option>
                        <option value="id_punggung" <?php echo $idTypeFilter === 'id_punggung' ? 'selected' : ''; ?>>ID Punggung</option>
                        <option value="pin_4m" <?php echo $idTypeFilter === 'pin_4m' ? 'selected' : ''; ?>>PIN 4M</option>
                        <option value="id_kaki" <?php echo $idTypeFilter === 'id_kaki' ? 'selected' : ''; ?>>ID Kaki</option>
                        <option value="job_psd" <?php echo $idTypeFilter === 'job_psd' ? 'selected' : ''; ?>>Job PSD</option>
                        <option value="id_other" <?php echo $idTypeFilter === 'id_other' ? 'selected' : ''; ?>>ID Other</option>
                    </select>

                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Export Button -->
            <?php if (!empty($startDate) && !empty($endDate)): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="<?php echo url('admin/request-id/export?start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate)); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export to Excel
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (!empty($requests)): ?>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Request Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Requester</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Conveyor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Shift</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-900">
                                    <?php echo htmlspecialchars($request->request_number); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($request->requester); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                        <?php 
                                            $typeLabels = [
                                                'id_punggung' => 'ID Punggung',
                                                'pin_4m' => 'PIN 4M',
                                                'id_kaki' => 'ID Kaki',
                                                'job_psd' => 'Job PSD',
                                                'id_other' => 'ID Other'
                                            ];
                                            echo htmlspecialchars($typeLabels[$request->id_type] ?? $request->id_type);
                                        ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->conveyor_name ?? '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $request->shift ?? '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php 
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected',
                                            'completed' => 'Completed',
                                            'cancelled' => 'Cancelled'
                                        ];
                                    ?>
                                    <span class="px-2 py-1 rounded <?php echo $statusColors[$request->status]; ?>">
                                        <?php echo htmlspecialchars($statusLabels[$request->status]); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo date('d M Y', strtotime($request->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="<?php echo url('/admin/request-id/' . $request->id); ?>" class="text-blue-600 hover:text-blue-900">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">No requests found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
