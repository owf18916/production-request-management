<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Include Confirmation Modal -->
        <?php include __DIR__ . '/../layouts/confirmation_modal.php'; ?>

        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo url('requests/atk'); ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center gap-1 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Requests
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Request ATK Detail</h1>
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
                            <label class="text-sm font-medium text-gray-500">Notes</label>
                            <p class="mt-2 text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($request->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Timeline -->
            <div>
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
                                                    elseif ($item->status === 'accepted') echo 'bg-blue-400';
                                                    elseif ($item->status === 'rejected') echo 'bg-red-400';
                                                    elseif ($item->status === 'completed') echo 'bg-green-400';
                                                ?>
                                            "></div>
                                            <div class="w-0.5 h-8 bg-gray-200 mt-1"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?php 
                                                    $statusLabels = ['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'completed' => 'Closed'];
                                                    echo $statusLabels[$item->status] ?? ucfirst($item->status); 
                                                ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo $item->changed_by_name ?? 'System'; ?> • <?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?>
                                            </p>
                                            <?php if ($item->notes): ?>
                                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($item->notes); ?></p>
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

                <!-- Action Buttons -->
                <?php if ($request->status === 'pending' || $request->status === 'approved'): ?>
                    <div class="bg-white rounded-lg shadow p-6 mt-6 space-y-3">
                        <?php if ($request->status === 'approved'): ?>
                            <!-- Complete Request Button -->
                            <button 
                                type="button"
                                onclick="openCompleteConfirmation()"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                            >
                                Confirm Goods Received (Complete)
                            </button>
                            <p class="text-xs text-gray-500 text-center">Click when you receive the goods</p>
                        <?php endif; ?>

                        <!-- Cancel Request Button -->
                        <button 
                            type="button"
                            onclick="openCancelConfirmation()"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                        >
                            Cancel Request
                        </button>
                        <p class="text-xs text-gray-500 text-center">This will cancel your request</p>
                    </div>

                    <script>
                    function openCompleteConfirmation() {
                        openConfirmationModal(
                            'Complete Request',
                            'Are you sure you want to mark this request as completed? Confirm that you have received the goods.',
                            submitCompleteForm,
                            'Yes, Confirm Receipt',
                            'green'
                        );
                    }

                    function submitCompleteForm() {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?php echo url("requests/atk/complete/{$request->id}"); ?>';
                        form.innerHTML = '<input type="hidden" name="_csrf_token" value="<?php echo session('_csrf_token') ?? ''; ?>">';
                        document.body.appendChild(form);
                        form.submit();
                    }

                    function openCancelConfirmation() {
                        openConfirmationModal(
                            'Cancel Request',
                            'Are you sure you want to cancel this request? This action cannot be undone.',
                            submitCancelForm,
                            'Yes, Cancel Request',
                            'red'
                        );
                    }

                    function submitCancelForm() {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?php echo url("requests/atk/cancel/{$request->id}"); ?>';
                        form.innerHTML = '<input type="hidden" name="_csrf_token" value="<?php echo session('_csrf_token') ?? ''; ?>">';
                        document.body.appendChild(form);
                        form.submit();
                    }
                    </script>
                <?php endif; ?>
        </div>
    </div>
</div>
