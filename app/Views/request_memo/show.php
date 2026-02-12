<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Include Confirmation Modal -->
        <?php include __DIR__ . '/../layouts/confirmation_modal.php'; ?>

        <!-- Back Link -->
        <a href="<?php echo url('/requests/memo'); ?>" class="text-blue-600 hover:text-blue-900 font-medium flex items-center mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Requests
        </a>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Request <?php echo $memo->request_number; ?></h1>
                    <p class="text-gray-600 mt-2">Submitted on <?php echo date('M d, Y \a\t g:i A', strtotime($memo->created_at)); ?></p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
                        <?php if ($memo->status === 'pending'): ?>
                            bg-yellow-100 text-yellow-800
                        <?php elseif ($memo->status === 'approved'): ?>
                            bg-green-100 text-green-800
                        <?php elseif ($memo->status === 'rejected'): ?>
                            bg-red-100 text-red-800
                        <?php elseif ($memo->status === 'cancelled'): ?>
                            bg-gray-100 text-gray-800
                        <?php else: ?>
                            bg-blue-100 text-blue-800
                        <?php endif; ?>
                    ">
                        <?php echo ucfirst($memo->status); ?>
                    </span>
                </div>
            </div>

            <!-- Memo Info -->
            <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b border-gray-200">
                <div>
                    <label class="text-sm text-gray-600">Submitted By</label>
                    <p class="text-lg font-medium text-gray-900"><?php echo $memo->requester; ?></p>
                </div>
                <?php if ($memo->approved_by): ?>
                    <div>
                        <label class="text-sm text-gray-600">Approved By</label>
                        <p class="text-lg font-medium text-gray-900"><?php echo $memo->approver; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Memo Content -->
            <div class="mb-6">
                <label class="text-sm text-gray-600 block mb-2">Memo Content</label>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">
                        <?php echo htmlspecialchars($memo->memo_content); ?>
                    </p>
                </div>
            </div>

            <!-- Notes if any -->
            <?php if ($memo->notes): ?>
                <div class="mb-6">
                    <label class="text-sm text-gray-600 block mb-2">Admin Notes</label>
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">
                            <?php echo htmlspecialchars($memo->notes); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- History Timeline -->
        <div class="bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Request History</h2>
            
            <?php if ($history): ?>
                <div class="space-y-6">
                    <?php foreach ($history as $entry): ?>
                        <div class="flex gap-4">
                            <!-- Timeline Indicator -->
                            <div class="flex flex-col items-center">
                                <div class="w-4 h-4 bg-blue-600 rounded-full mt-2"></div>
                                <?php if ($entry !== end($history)): ?>
                                    <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                                <?php endif; ?>
                            </div>

                            <!-- Timeline Content -->
                            <div class="pb-6">
                                <div class="flex items-baseline gap-3 mb-1">
                                    <span class="text-sm font-semibold text-gray-900">
                                        <?php echo ucfirst($entry->status); ?>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('M d, Y \a\t g:i A', strtotime($entry->created_at)); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">
                                    By: <span class="font-medium"><?php echo $entry->changed_by_name; ?></span>
                                </p>
                                <?php if ($entry->notes): ?>
                                    <div class="bg-gray-50 p-3 rounded text-sm text-gray-700">
                                        <?php echo htmlspecialchars($entry->notes); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600 text-center py-8">No history records found</p>
            <?php endif; ?>
        </div>

        <!-- Cancel Request Button -->
        <?php if ($memo->status === 'pending'): ?>
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <button 
                    type="button"
                    onclick="openCancelConfirmation()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                >
                    Cancel Request
                </button>
                <p class="text-xs text-gray-500 mt-2">This will cancel your request</p>
            </div>

            <script>
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
                form.action = '<?php echo url("requests/memo/cancel/{$memo->id}"); ?>';
                form.innerHTML = '<input type="hidden" name="_csrf_token" value="<?php echo session('_csrf_token') ?? ''; ?>">';
                document.body.appendChild(form);
                form.submit();
            }
            </script>
        <?php endif; ?>
    </div>
</div>
