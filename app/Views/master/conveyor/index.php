<?php
$content = ob_get_clean();
ob_start();
?>

<?php
// Ensure user is logged in and is admin
if (!session('user_id')) {
    header('Location: ' . url('login'));
    exit;
}
if (session('user_role') !== 'admin') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

// Get old input for search/filter
$oldSearch = getFlash('old_search', '');
$oldStatus = getFlash('old_status', '');
?>

<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Master Conveyor</h1>
            <p class="text-gray-600 mt-1">Manage production conveyors and assign users</p>
        </div>
        <a href="<?php echo url('admin/master/conveyor/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Conveyor
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="<?php echo e($search ?? ''); ?>" 
                           placeholder="Search by conveyor name..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($status === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="inactive" <?php echo ($status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>

            <!-- Search Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (empty($conveyors)): ?>
            <!-- Empty State -->
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No conveyors found</h3>
                <p class="mt-1 text-gray-500">Get started by creating a new conveyor.</p>
                <a href="<?php echo url('admin/master/conveyor/create'); ?>" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Conveyor
                </a>
            </div>
        <?php else: ?>
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Conveyor Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Users Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $offset = ($page - 1) * 10;
                        foreach ($conveyors as $index => $conveyor): 
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors" x-data="{ showDeleteModal: false, conveyorId: <?php echo $conveyor->id; ?> }">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo ($offset + $index + 1); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo e($conveyor->conveyor_name); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($conveyor->status === 'active'): ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo $conveyor->users_count; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo e($conveyor->created_by_name); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <!-- Edit Button -->
                                        <a href="<?php echo url("admin/master/conveyor/edit/{$conveyor->id}"); ?>" 
                                           class="text-blue-600 hover:text-blue-900 flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        <!-- Manage Users Button -->
                                        <a href="<?php echo url("admin/master/conveyor/manage-users/{$conveyor->id}"); ?>" 
                                           class="text-purple-600 hover:text-purple-900 flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a3 3 0 003-3v-2a3 3 0 00-3-3H3a3 3 0 00-3 3v2a3 3 0 003 3z"></path>
                                            </svg>
                                            <span>Users</span>
                                        </a>

                                        <!-- Toggle Status Button (AJAX) -->
                                        <button @click="toggleStatus(<?php echo $conveyor->id; ?>, '<?php echo e(csrfToken()); ?>')"
                                                class="text-orange-600 hover:text-orange-900 flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <span>Toggle</span>
                                        </button>

                                        <!-- Delete Button -->
                                        <button @click="showDeleteModal = true"
                                                class="text-red-600 hover:text-red-900 flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>

                                    <!-- Delete Confirmation Modal -->
                                    <div x-show="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
                                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                            <div class="mt-3">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Conveyor</h3>
                                                <p class="text-gray-600 mb-6">Are you sure you want to delete "<span class="font-semibold"><?php echo e($conveyor->conveyor_name); ?></span>"? This action cannot be undone.</p>
                                                <div class="flex justify-end space-x-3">
                                                    <button @click="showDeleteModal = false" 
                                                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="<?php echo url("admin/master/conveyor/delete/{$conveyor->id}"); ?>" class="inline">
                                                        <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">
                                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo url("admin/master/conveyor?search=" . urlencode($search) . "&status=$status&page=" . ($page - 1)); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="<?php echo url("admin/master/conveyor?search=" . urlencode($search) . "&status=$status&page=" . ($page + 1)); ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium"><?php echo ($offset + 1); ?></span> to 
                            <span class="font-medium"><?php echo min($offset + count($conveyors), $total); ?></span> of 
                            <span class="font-medium"><?php echo $total; ?></span> results
                        </p>
                    </div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo url("admin/master/conveyor?search=" . urlencode($search) . "&status=$status&page=" . ($page - 1)); ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <a href="<?php echo url("admin/master/conveyor?search=" . urlencode($search) . "&status=$status&page=$i"); ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 <?php echo ($i === $page ? 'z-10 border-blue-500 bg-blue-50' : 'hover:bg-gray-50'); ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo url("admin/master/conveyor?search=" . urlencode($search) . "&status=$status&page=" . ($page + 1)); ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function toggleStatus(conveyorId, csrfToken) {
    if (!confirm('Are you sure you want to toggle the status?')) {
        return;
    }

    try {
        const response = await fetch('<?php echo url("admin/master/conveyor/toggle-status/"); ?>' + conveyorId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
            body: JSON.stringify({
                _csrf_token: csrfToken,
            }),
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}
</script>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
