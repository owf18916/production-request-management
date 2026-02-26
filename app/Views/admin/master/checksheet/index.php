<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Master Checksheet</h1>
                <p class="mt-2 text-gray-600">Manage inspection and quality control checksheets</p>
            </div>
            <a href="<?php echo url('/admin/master/checksheet/create'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Checksheet
                </span>
            </a>
        </div>

        <!-- Search Box -->
        <div class="mb-6">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by kode or nama checksheet..." 
                       value="<?php echo htmlspecialchars($search ?? ''); ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Search
                </button>
                <?php if ($search): ?>
                    <a href="<?php echo url('/admin/master/checksheet'); ?>" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Statistics -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-600 text-sm font-medium">Total Checksheets</div>
                <div class="mt-2 text-3xl font-bold text-blue-600"><?php echo count($checksheets); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-600 text-sm font-medium">Displayed</div>
                <div class="mt-2 text-3xl font-bold text-green-600"><?php echo count($checksheets); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-600 text-sm font-medium">Total Records</div>
                <div class="mt-2 text-3xl font-bold text-purple-600"><?php echo $totalCount; ?></div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Checksheet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($checksheets)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No checksheets found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($checksheets as $checksheet): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <?php echo htmlspecialchars($checksheet->kode_checksheet); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($checksheet->nama_checksheet); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($checksheet->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="<?php echo url("/admin/master/checksheet/edit/{$checksheet->id}"); ?>" 
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <button @click="deleteChecksheet(<?php echo $checksheet->id; ?>)" 
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Info -->
        <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-gray-700">
                <span class="font-medium">
                    Showing <?php echo $pagination->getStartItem(); ?> to <?php echo $pagination->getEndItem(); ?> of <?php echo $totalCount; ?> results
                </span>
            </div>
            
            <!-- Pagination Navigation -->
            <?php if ($pagination->getTotalPages() > 1): ?>
            <nav class="mt-4 md:mt-0 flex items-center justify-center space-x-2">
                <!-- Previous Button -->
                <?php if ($pagination->hasPreviousPage()): ?>
                    <a href="<?php echo url('/admin/master/checksheet?page=' . $pagination->getPreviousPage() . '&search=' . urlencode($search ?? '')); ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        ← Previous
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed">
                        ← Previous
                    </span>
                <?php endif; ?>

                <!-- Page Numbers -->
                <div class="flex items-center space-x-1">
                    <?php 
                    $pageRange = $pagination->getPageRange(5);
                    $currentPage = $pagination->getCurrentPage();
                    
                    // Show first page if not in range
                    if ($pageRange[0] > 1): ?>
                        <a href="<?php echo url('/admin/master/checksheet?page=1&search=' . urlencode($search ?? '')); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            1
                        </a>
                        <?php if ($pageRange[0] > 2): ?>
                            <span class="px-2 py-2 text-gray-700">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php foreach ($pageRange as $page): ?>
                        <?php if ($page === $currentPage): ?>
                            <span class="px-3 py-2 border border-blue-500 rounded-md text-sm font-medium text-white bg-blue-600">
                                <?php echo $page; ?>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo url('/admin/master/checksheet?page=' . $page . '&search=' . urlencode($search ?? '')); ?>" 
                               class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <?php echo $page; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Show last page if not in range -->
                    <?php if ($pageRange[count($pageRange) - 1] < $pagination->getTotalPages()): ?>
                        <?php if ($pageRange[count($pageRange) - 1] < $pagination->getTotalPages() - 1): ?>
                            <span class="px-2 py-2 text-gray-700">...</span>
                        <?php endif; ?>
                        <a href="<?php echo url('/admin/master/checksheet?page=' . $pagination->getTotalPages() . '&search=' . urlencode($search ?? '')); ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <?php echo $pagination->getTotalPages(); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Next Button -->
                <?php if ($pagination->hasNextPage()): ?>
                    <a href="<?php echo url('/admin/master/checksheet?page=' . $pagination->getNextPage() . '&search=' . urlencode($search ?? '')); ?>" 
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Next →
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed">
                        Next →
                    </span>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div x-data="{ open: false, checksheetId: null }" @delete-checksheet.window="open = true; checksheetId = $event.detail.id">
    <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Checksheet?</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this checksheet? This action cannot be undone.</p>
            <div class="flex gap-4">
                <button @click="open = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <form method="POST" :action="'<?php echo url('/admin/master/checksheet/delete'); ?>/' + checksheetId" class="flex-1">
                    <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteChecksheet(id) {
    window.dispatchEvent(new CustomEvent('delete-checksheet', { detail: { id } }));
}
</script>
