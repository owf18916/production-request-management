<div class="min-h-screen bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Dasbor Admin</h1>
            <p class="mt-2 text-sm text-gray-600">Selamat datang, <?php echo e($user_name); ?>. Berikut adalah ringkasan semua requests produksi.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Pending Requests Card -->
            <div class="block">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Request Pending</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $stats['pending']; ?></p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Approved Today Card -->
            <div class="block">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Disetujui Hari Ini</p>
                            <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $stats['approved_today']; ?></p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Rejected Today Card -->
            <div class="block">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Ditolak Hari Ini</p>
                            <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $stats['rejected_today']; ?></p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Completed This Month Card -->
            <div class="block">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Selesai Bulan Ini</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $stats['completed_month']; ?></p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Request Types Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Pending by Type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Pending Berdasarkan Tipe</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request ATK</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['atk'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request Checksheet</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['checksheet'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request ID</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['id'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Internal Memo</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['memo'] ?? 0; ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h2>
                <div class="space-y-3">
                    <a href="<?php echo url('/admin/requests/atk'); ?>" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                        Request ATK
                    </a>
                    <a href="<?php echo url('/admin/request_checksheet'); ?>" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                        Request Checksheet
                    </a>
                    <a href="<?php echo url('/admin/request-id'); ?>" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                        Request ID
                    </a>
                    <a href="<?php echo url('/admin/requests/memo'); ?>" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                        Internal Memo
                    </a>
                </div>
            </div>

            <!-- Master Data -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Data Master</h2>
                <div class="space-y-3">
                    <a href="<?php echo url('/admin/master/atk'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        Master ATK
                    </a>
                    <a href="<?php echo url('/admin/master/checksheet'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        Master Checksheet
                    </a>
                    <a href="<?php echo url('/admin/master/conveyor'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        Master Conveyor
                    </a>
                    <a href="<?php echo url('/admin/users'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        Master Users
                    </a>
                </div>
            </div>

        </div>

        <!-- Recent Requests Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Requests (10 Terakhir)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Request #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Requested By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($recentRequests)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No requests found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentRequests as $request): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $request->type ?? 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #<?php echo $request->request_number ?? 'N/A'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo $request->requester ?? 'N/A'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $status = $request->status ?? 'pending';
                                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $color; ?>">
                                            <?php 
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'completed' => 'Completed'
                                            ];
                                            echo $statusLabels[$status] ?? ucfirst($status);
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo date('d M Y', strtotime($request->created_at ?? date('Y-m-d'))); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php
                                        // Determine the URL based on request type
                                        $viewUrl = '#';
                                        $type = $request->type ?? '';
                                        $id = $request->id ?? '';
                                        
                                        switch($type) {
                                            case 'ATK':
                                                $viewUrl = url('/admin/requests/atk/' . $id);
                                                break;
                                            case 'Checksheet':
                                                $viewUrl = url('/admin/request_checksheet/show/' . $id);
                                                break;
                                            case 'ID':
                                                $viewUrl = url('/admin/request-id/' . $id);
                                                break;
                                            case 'Memo':
                                                $viewUrl = url('/admin/requests/memo/show/' . $id);
                                                break;
                                        }
                                        ?>
                                        <a href="<?php echo $viewUrl; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>