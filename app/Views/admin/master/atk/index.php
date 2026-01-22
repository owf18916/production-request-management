<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Master ATK</h1>
                <p class="mt-2 text-gray-600">Manage office supplies (Alat Tulis Kantor)</p>
            </div>
            <a href="<?php echo url('/admin/master/atk/create'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add ATK
                </span>
            </a>
        </div>

        <!-- Search Box -->
        <div class="mb-6">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Search by kode or nama barang..." 
                       value="<?php echo htmlspecialchars($search ?? ''); ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Search
                </button>
                <?php if ($search): ?>
                    <a href="<?php echo url('/admin/master/atk'); ?>" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Statistics -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-600 text-sm font-medium">Total ATK</div>
                <div class="mt-2 text-3xl font-bold text-blue-600"><?php echo count($atks); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-600 text-sm font-medium">Displayed</div>
                <div class="mt-2 text-3xl font-bold text-green-600"><?php echo count($atks); ?></div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($atks)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No ATK found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($atks as $atk): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($atk->kode_barang); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($atk->nama_barang); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($atk->created_at)); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="<?php echo url("/admin/master/atk/edit/{$atk->id}"); ?>" 
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <button @click="deleteAtk(<?php echo $atk->id; ?>)" 
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div x-data="{ open: false, atkId: null }" @delete-atk.window="open = true; atkId = $event.detail.id">
    <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Delete ATK?</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this ATK? This action cannot be undone.</p>
            <div class="flex gap-4">
                <button @click="open = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <form method="POST" :action="'<?php echo url('/admin/master/atk/delete'); ?>/' + atkId" class="flex-1">
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
function deleteAtk(id) {
    window.dispatchEvent(new CustomEvent('delete-atk', { detail: { id } }));
}
</script>
