<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Requests - Checksheet</h1>
                    <p class="text-gray-600 mt-2">Total: <span class="font-semibold"><?php echo $totalCount; ?></span> requests</p>
                </div>
                <a href="<?php echo url('request_checksheet/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($message = session('message')): ?>
            <div class="mb-6 p-4 rounded-lg" style="background-color: <?php echo session('message_type') === 'success' ? '#dcfce7' : '#fee2e2'; ?>; border: 1px solid <?php echo session('message_type') === 'success' ? '#86efac' : '#fca5a5'; ?>;">
                <p style="color: <?php echo session('message_type') === 'success' ? '#166534' : '#991b1b'; ?>"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                               placeholder="Search by request number or checksheet..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                        Search
                    </button>
                    <?php if ($search || $status || $startDate || $endDate): ?>
                        <a href="<?php echo url('request_checksheet'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Requests Table -->
        <?php if (count($requests) > 0): ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Request No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Checksheet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <a href="<?php echo url("request_checksheet/show/{$request->id}"); ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($request->request_number); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo htmlspecialchars($request->nama_checksheet ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo htmlspecialchars($request->qty); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php 
                                    $statusColors = [
                                        'pending' => '#fef08a',
                                        'approved' => '#bfdbfe',
                                        'rejected' => '#fecaca',
                                        'completed' => '#bbf7d0',
                                    ];
                                    $statusTextColors = [
                                        'pending' => '#713f12',
                                        'approved' => '#1e3a8a',
                                        'rejected' => '#7f1d1d',
                                        'completed' => '#166534',
                                    ];
                                    $bgColor = $statusColors[$request->status] ?? '#f3f4f6';
                                    $textColor = $statusTextColors[$request->status] ?? '#374151';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>;">
                                        <?php echo ucfirst($request->status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo date('d/m/Y H:i', strtotime($request->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="<?php echo url("request_checksheet/show/{$request->id}"); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No requests found</h3>
                <p class="mt-2 text-gray-600">Start by creating a new checksheet request</p>
                <a href="<?php echo url('request_checksheet/create'); ?>" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Create Request
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
