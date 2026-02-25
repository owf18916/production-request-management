<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo url('admin/requests/atk'); ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center gap-1 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Requests
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Request ATK Review</h1>
            <p class="mt-2 text-gray-600">Request #<?php echo $request->request_number; ?></p>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-3 gap-8">
            <!-- Left Column - Request Details -->
            <div class="col-span-2">
                <!-- Request Info Card -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Request Information</h2>
                        </div>
                        <div>
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'completed' => 'bg-green-100 text-green-800',
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
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                <?php echo $label; ?>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Request Number -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Request Number</label>
                            <p class="mt-1 text-lg font-mono font-semibold text-gray-900"><?php echo $request->request_number; ?></p>
                        </div>

                        <!-- ATK Item -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">ATK Item</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo $request->nama_barang ?? '-'; ?></p>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Quantity</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo $request->qty; ?></p>
                        </div>

                        <!-- Date Requested -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date Requested</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo date('d/m/Y H:i', strtotime($request->created_at)); ?></p>
                        </div>

                        <!-- Requester -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Requester</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900"><?php echo $request->requester ?? '-'; ?></p>
                        </div>

                        <!-- Approved By -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Approved By</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                <?php echo $request->approver ? ($request->approver . ' - ' . date('d/m/Y H:i', strtotime($request->approved_at))) : '-'; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <?php if ($request->notes): ?>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="text-sm font-medium text-gray-500">Requester Notes</label>
                            <p class="mt-2 text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded border border-gray-200"><?php echo htmlspecialchars($request->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Status Update Form & Timeline -->
            <div class="space-y-6">
                <!-- Status Update Form -->
                <?php if ($request->status === 'pending'): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>

                        <form method="POST" action="<?php echo url("admin/requests/atk/{$request->id}/update-status"); ?>">
                            <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                                <?php if (isset($errors['status'])): ?>
                                    <div class="p-3 mb-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                        <?php echo $errors['status']; ?>
                                    </div>
                                <?php endif; ?>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Status</option>
                                    <option value="approved">Approve (will reduce stock)</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes <span class="text-gray-500 text-xs">(required for rejection)</span>
                                </label>
                                <?php if (isset($errors['notes'])): ?>
                                    <div class="p-2 mb-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                        <?php echo $errors['notes']; ?>
                                    </div>
                                <?php endif; ?>
                                <textarea 
                                    name="notes" 
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2">
                                <button 
                                    type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                                >
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Status</h3>
                        <p class="text-gray-600">This request is <?php echo ucfirst($request->status); ?>. 
                        <?php if ($request->status === 'approved'): ?>
                            User dapat mengkonfirmasi penerimaan barang dan mengubah status menjadi Completed.
                        <?php elseif ($request->status === 'completed'): ?>
                            Request telah selesai.
                        <?php endif; ?>
                        No further changes can be made by admin.</p>
                    </div>
                <?php endif; ?>

                <!-- Status Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Status History</h3>

                    <?php if (!empty($history)): ?>
                        <div class="space-y-4">
                            <?php foreach (array_reverse($history) as $item): ?>
                                <div class="relative pb-4">
                                    <div class="flex gap-3">
                                        <div class="flex flex-col items-center">
                                            <div class="w-3 h-3 rounded-full 
                                                <?php 
                                                    if ($item->status === 'pending') echo 'bg-yellow-400';
                                                    elseif ($item->status === 'approved') echo 'bg-blue-400';
                                                    elseif ($item->status === 'rejected') echo 'bg-red-400';
                                                    elseif ($item->status === 'completed') echo 'bg-green-400';
                                                    elseif ($item->status === 'cancelled') echo 'bg-gray-400';
                                                ?>
                                            "></div>
                                            <div class="w-0.5 h-8 bg-gray-200 mt-1"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?php 
                                                    $statusLabels = [
                                                        'pending' => 'Pending',
                                                        'approved' => 'Approved',
                                                        'rejected' => 'Rejected',
                                                        'completed' => 'Completed',
                                                        'cancelled' => 'Cancelled'
                                                    ];
                                                    echo $statusLabels[$item->status] ?? ucfirst($item->status); 
                                                ?>
                                            </p>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo $item->changed_by_name ?? 'System'; ?> • <?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?>
                                            </p>
                                            <?php if ($item->notes): ?>
                                                <p class="text-sm text-gray-600 mt-1 bg-gray-50 p-2 rounded border border-gray-200"><?php echo htmlspecialchars($item->notes); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">No status history available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
