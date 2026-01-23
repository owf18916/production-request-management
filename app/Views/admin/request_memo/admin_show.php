<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Link -->
        <a href="<?php echo url('/admin/requests/memo'); ?>" class="text-blue-600 hover:text-blue-900 font-medium flex items-center mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Requests
        </a>

        <!-- Flash Messages -->
        <?php if (session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo session('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Request Header -->
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
                        <?php else: ?>
                            bg-blue-100 text-blue-800
                        <?php endif; ?>
                    ">
                        <?php echo ucfirst($memo->status); ?>
                    </span>
                </div>
            </div>

            <!-- Request Info Grid -->
            <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b border-gray-200">
                <div>
                    <label class="text-sm text-gray-600">Submitted By</label>
                    <p class="text-lg font-medium text-gray-900"><?php echo $memo->requester; ?></p>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Created</label>
                    <p class="text-lg font-medium text-gray-900"><?php echo date('M d, Y', strtotime($memo->created_at)); ?></p>
                </div>
                <?php if ($memo->approved_by): ?>
                    <div>
                        <label class="text-sm text-gray-600">Approved By</label>
                        <p class="text-lg font-medium text-gray-900"><?php echo $memo->approver; ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Approved At</label>
                        <p class="text-lg font-medium text-gray-900"><?php echo date('M d, Y \a\t g:i A', strtotime($memo->approved_at)); ?></p>
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

            <!-- Existing Notes -->
            <?php if ($memo->notes): ?>
                <div class="mb-6">
                    <label class="text-sm text-gray-600 block mb-2">Previous Notes</label>
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">
                            <?php echo htmlspecialchars($memo->notes); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status Update Form (if pending or approved) -->
        <?php if (in_array($memo->status, ['pending', 'approved'])): ?>
            <div class="bg-white rounded-lg shadow p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Update Status</h2>
                
                <form method="POST" action="<?php echo url("/admin/requests/memo/update-status/{$memo->id}"); ?>">
                    <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

                    <!-- Status Selection -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-600">*</span>
                        </label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select Status</option>
                            <?php if ($memo->status === 'pending'): ?>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            <?php elseif ($memo->status === 'approved'): ?>
                                <option value="completed">Mark as Completed</option>
                                <option value="rejected">Reject</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes 
                            <span class="text-red-600" id="notes-required">*</span>
                            <span class="text-gray-600">(Required for rejection)</span>
                        </label>
                        <textarea 
                            name="notes"
                            id="notes"
                            rows="4"
                            placeholder="Add any comments or feedback..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['notes']) ? 'border-red-500' : ''; ?>"
                        ></textarea>
                        <?php if (isset($errors['notes'])): ?>
                            <span class="text-red-600 text-sm"><?php echo $errors['notes']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Update Status
                        </button>
                        <a href="<?php echo url('/admin/requests/memo'); ?>" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 mb-6">
                <p class="text-gray-700 text-center">
                    This request is locked and cannot be updated (Status: <strong><?php echo ucfirst($memo->status); ?></strong>)
                </p>
            </div>
        <?php endif; ?>

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
    </div>
</div>

<script>
    // Show/hide notes requirement indicator
    document.getElementById('status').addEventListener('change', function() {
        const notesRequired = document.getElementById('notes-required');
        if (this.value === 'rejected') {
            notesRequired.style.display = 'inline';
        } else {
            notesRequired.style.display = 'none';
        }
    });
</script>
