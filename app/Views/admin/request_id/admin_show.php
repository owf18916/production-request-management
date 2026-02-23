<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Request Detail</h1>
                <p class="mt-2 text-gray-600">Request #<?php echo htmlspecialchars($request->request_number); ?></p>
            </div>
            <a href="<?php echo url('/admin/request-id'); ?>" class="text-blue-600 hover:text-blue-900">
                ← Back to List
            </a>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="col-span-2">
                <!-- Request Info -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Request Number</label>
                            <p class="text-lg font-mono text-gray-900"><?php echo htmlspecialchars($request->request_number); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Requester</label>
                            <p class="text-lg text-gray-900"><?php echo htmlspecialchars($request->requester); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">ID Type</label>
                            <p class="text-lg text-gray-900">
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
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <p>
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
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColors[$request->status]; ?>">
                                    <?php echo htmlspecialchars($statusLabels[$request->status]); ?>
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Created Date</label>
                            <p class="text-gray-900"><?php echo date('d M Y H:i', strtotime($request->created_at)); ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Last Updated</label>
                            <p class="text-gray-900"><?php echo date('d M Y H:i', strtotime($request->updated_at)); ?></p>
                        </div>
                    </div>

                    <?php if ($request->approved_at): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Approved By</label>
                                    <p class="text-gray-900"><?php echo htmlspecialchars($request->approver ?? '-'); ?></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Approved Date</label>
                                    <p class="text-gray-900"><?php echo date('d M Y H:i', strtotime($request->approved_at)); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Dynamic Fields -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h2>
                    
                    <?php if (!empty($details)): ?>
                        <div class="space-y-3">
                            <?php foreach ($details as $fieldName => $fieldValue): ?>
                                <?php 
                                    $fieldConfig = $idTypeFields[$request->id_type][$fieldName] ?? null;
                                    $fieldLabel = $fieldConfig['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
                                ?>
                                <div class="pb-3 border-b border-gray-200 last:border-0">
                                    <label class="text-sm font-medium text-gray-600"><?php echo htmlspecialchars($fieldLabel); ?></label>
                                    <p class="text-gray-900 mt-1">
                                        <?php 
                                            if (is_string($fieldValue) && strlen($fieldValue) > 100) {
                                                echo '<pre class="bg-gray-50 p-3 rounded text-sm">' . htmlspecialchars($fieldValue) . '</pre>';
                                            } else {
                                                echo htmlspecialchars($fieldValue);
                                            }
                                        ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-600">No details recorded</p>
                    <?php endif; ?>
                </div>

                <?php if ($request->notes): ?>
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">User Notes</h2>
                        <div class="bg-blue-50 p-4 rounded border border-blue-200">
                            <p class="text-gray-900"><?php echo htmlspecialchars($request->notes); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status Update Form -->
                <?php if ($request->status === 'pending' || $request->status === 'approved'): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h2>
                        
                        <form method="POST" action="<?php echo url('/admin/request-id/' . $request->id . '/update-status'); ?>">
                            <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">
                            
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <?php if ($request->status === 'pending'): ?>
                                        <option value="">Select Status...</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    <?php elseif ($request->status === 'approved'): ?>
                                        <option value="">Select Status...</option>
                                        <option value="completed">Mark as Completed</option>
                                        <option value="rejected">Reject</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Add any notes about this decision..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Update Status
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg p-6 border-2 border-dashed border-gray-300">
                        <p class="text-gray-700 font-medium">
                            This request is in a final state (<?php echo htmlspecialchars($statusLabels[$request->status]); ?>) and cannot be modified.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar - History Timeline -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status History</h2>
                    
                    <?php if (!empty($history)): ?>
                        <div class="space-y-4">
                            <?php foreach ($history as $event): ?>
                                <div class="flex gap-3">
                                    <div class="flex flex-col items-center">
                                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                        <?php if ($event !== $history[count($history) - 1]): ?>
                                            <div class="w-0.5 h-8 bg-gray-300 mt-1"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 pb-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php 
                                                $statusLabel = ucfirst(str_replace('_', ' ', $event->status));
                                                echo htmlspecialchars($statusLabel);
                                            ?>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            By: <?php echo htmlspecialchars($event->changed_by_name ?? 'System'); ?><br>
                                            <?php echo date('d M Y H:i', strtotime($event->created_at)); ?>
                                        </p>
                                        <?php if ($event->notes): ?>
                                            <p class="text-xs text-gray-700 mt-2 bg-gray-50 p-2 rounded">
                                                <?php echo htmlspecialchars($event->notes); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-600">No history available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
