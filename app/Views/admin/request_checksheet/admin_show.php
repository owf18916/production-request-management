<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($request->request_number); ?></h1>
                    <p class="text-gray-600 mt-2">Created on <?php echo date('d/m/Y H:i', strtotime($request->created_at)); ?></p>
                </div>
                <a href="<?php echo url('admin/request_checksheet'); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to List
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($message = session('message')): ?>
            <div class="mb-6 p-4 rounded-lg" style="background-color: <?php echo session('message_type') === 'success' ? '#dcfce7' : '#fee2e2'; ?>; border: 1px solid <?php echo session('message_type') === 'success' ? '#86efac' : '#fca5a5'; ?>; border-radius: 0.5rem;">
                <p style="color: <?php echo session('message_type') === 'success' ? '#166534' : '#991b1b'; ?>"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Checksheet</p>
                                <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($request->nama_checksheet); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Quantity</p>
                                <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($request->qty); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Requested by</p>
                                <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($request->full_name); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Current Status</p>
                                <?php 
                                $statusColors = [
                                    'pending' => ['bg' => '#fef08a', 'text' => '#713f12'],
                                    'approved' => ['bg' => '#bfdbfe', 'text' => '#1e3a8a'],
                                    'rejected' => ['bg' => '#fecaca', 'text' => '#7f1d1d'],
                                    'completed' => ['bg' => '#bbf7d0', 'text' => '#166534'],
                                ];
                                $colors = $statusColors[$request->status] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                ?>
                                <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold" style="background-color: <?php echo $colors['bg']; ?>; color: <?php echo $colors['text']; ?>;">
                                    <?php echo ucfirst($request->status); ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($request->notes): ?>
                            <div>
                                <p class="text-sm text-gray-600">Notes</p>
                                <p class="text-gray-900"><?php echo htmlspecialchars($request->notes); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Update Form (if not locked) -->
                <?php if ($request->status !== 'rejected' && $request->status !== 'completed'): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h2>
                        <form method="POST" action="<?php echo url("admin/request_checksheet/update_status/{$request->id}"); ?>" class="space-y-4">
                            <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    New Status <span class="text-red-600">*</span>
                                </label>
                                <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['status']) ? 'border-red-500' : ''; ?>">
                                    <option value="">Select status...</option>
                                    <?php if ($request->status === 'pending'): ?>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    <?php elseif ($request->status === 'approved'): ?>
                                        <option value="completed">Mark as Completed</option>
                                        <option value="rejected">Reject</option>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($errors['status'])): ?>
                                    <p class="text-red-600 text-sm"><?php echo $errors['status']; ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                    Notes / Reason
                                    <span class="text-gray-500">(required for rejection)</span>
                                </label>
                                <textarea 
                                    id="notes" 
                                    name="notes"
                                    rows="3"
                                    placeholder="Add any notes or reason for the status change..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['notes']) ? 'border-red-500' : ''; ?>"
                                ></textarea>
                                <?php if (isset($errors['notes'])): ?>
                                    <p class="text-red-600 text-sm"><?php echo $errors['notes']; ?></p>
                                <?php endif; ?>
                            </div>

                            <button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
                            >
                                Update Status
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-lg shadow-sm p-6 border-l-4 border-gray-300">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Status Locked</h2>
                        <p class="text-gray-600">This request has a final status and cannot be changed further.</p>
                    </div>
                <?php endif; ?>

                <!-- Approval Info (if approved) -->
                <?php if ($request->approved_by): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="font-semibold text-green-900 mb-2">Approval Information</h3>
                        <div class="space-y-2 text-green-800 text-sm">
                            <p><strong>Approved by:</strong> <?php echo htmlspecialchars($request->approved_by_name ?? 'Unknown'); ?></p>
                            <p><strong>Approved at:</strong> <?php echo date('d/m/Y H:i', strtotime($request->approved_at)); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- History Timeline -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Request History</h2>
                    <?php if (count($history) > 0): ?>
                        <div class="space-y-4">
                            <?php foreach ($history as $h): ?>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 rounded-full bg-blue-600 mt-1"></div>
                                        <?php if ($h !== end($history)): ?>
                                            <div class="w-0.5 h-12 bg-gray-300 my-2"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-semibold text-gray-900">Status changed to <span class="text-blue-600"><?php echo ucfirst($h->status); ?></span></p>
                                                    <p class="text-sm text-gray-600 mt-1">by <?php echo htmlspecialchars($h->full_name ?? 'System'); ?></p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    <?php echo date('d/m/Y H:i', strtotime($h->created_at)); ?>
                                                </p>
                                            </div>
                                            <?php if ($h->notes): ?>
                                                <p class="text-gray-700 mt-2 text-sm"><?php echo htmlspecialchars($h->notes); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-600">No history available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Request Info Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Request Info</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Request Number</p>
                            <p class="font-semibold text-gray-900 break-all"><?php echo htmlspecialchars($request->request_number); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Requested by</p>
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($request->full_name); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Requested on</p>
                            <p class="font-semibold text-gray-900"><?php echo date('d/m/Y', strtotime($request->created_at)); ?></p>
                        </div>
                        <?php if ($request->updated_at !== $request->created_at): ?>
                            <div>
                                <p class="text-gray-600">Last Updated</p>
                                <p class="font-semibold text-gray-900"><?php echo date('d/m/Y H:i', strtotime($request->updated_at)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status Legend -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Status Legend</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
                            <span>Pending Review</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-400 rounded-full"></span>
                            <span>Approved</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                            <span>Completed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-red-400 rounded-full"></span>
                            <span>Rejected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
