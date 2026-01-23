<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo url('/requests/memo'); ?>" class="text-blue-600 hover:text-blue-900 font-medium flex items-center mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Requests
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Create Internal Memo Request</h1>
            <p class="text-gray-600 mt-2">Write your internal memo and submit for approval</p>
        </div>

        <!-- Flash Messages -->
        <?php if (session('error')): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo session('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo url('/requests/memo/store'); ?>" class="bg-white rounded-lg shadow p-8">
            <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

            <!-- Memo Content -->
            <div class="mb-6">
                <label for="memo_content" class="block text-sm font-medium text-gray-700 mb-2">
                    Memo Content <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <textarea 
                        id="memo_content"
                        name="memo_content"
                        rows="12"
                        placeholder="Enter your internal memo content here... (minimum 10 characters)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($errors['memo_content']) ? 'border-red-500' : ''; ?>"
                    ><?php echo htmlspecialchars($memo_content ?? ''); ?></textarea>
                </div>
                
                <!-- Character Counter -->
                <div class="mt-2 flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <span id="char-count">0</span> / 5000 characters
                    </div>
                    <?php if (isset($errors['memo_content'])): ?>
                        <span class="text-red-600 text-sm"><?php echo $errors['memo_content']; ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Conveyor Selection -->
            <div class="mb-6">
                <label for="conveyor_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Conveyor <span class="text-gray-500">(optional)</span>
                </label>
                <?php if (isset($errors['conveyor_id'])): ?>
                    <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        <?php echo $errors['conveyor_id']; ?>
                    </div>
                <?php endif; ?>
                <select 
                    id="conveyor_id"
                    name="conveyor_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">-- Select Conveyor --</option>
                    <?php if (isset($conveyors)): ?>
                        <?php foreach ($conveyors as $conveyor): ?>
                            <option value="<?php echo $conveyor->id; ?>" <?php echo (isset($conveyor_id) && $conveyor_id == $conveyor->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($conveyor->conveyor_name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Shift Selection -->
            <div class="mb-6">
                <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">
                    Shift <span class="text-gray-500">(optional)</span>
                </label>
                <?php if (isset($errors['shift'])): ?>
                    <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        <?php echo $errors['shift']; ?>
                    </div>
                <?php endif; ?>
                <select 
                    id="shift"
                    name="shift" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">-- Select Shift --</option>
                    <?php if (isset($shifts)): ?>
                        <?php foreach ($shifts as $shiftOption): ?>
                            <option value="<?php echo $shiftOption; ?>" <?php echo (isset($shift) && $shift === $shiftOption) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($shiftOption); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Submission Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Before Submitting:</p>
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            <li>Ensure your memo content is clear and complete</li>
                            <li>Double-check for any typos or errors</li>
                            <li>Your memo will be sent for admin approval</li>
                            <li>You can view the status in your requests list</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Submit Request
                </button>
                <a href="<?php echo url('/requests/memo'); ?>" class="flex-1 px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('memo_content').addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
    });
    // Set initial count if value exists
    window.addEventListener('load', function() {
        document.getElementById('char-count').textContent = document.getElementById('memo_content').value.length;
    });
</script>
