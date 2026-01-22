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

$errors = getFlash('errors', []);
?>

<div class="py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?php echo url('admin/master/conveyor'); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Master Conveyor
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Manage Users</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Users - <span class="text-blue-600"><?php echo e($conveyor->conveyor_name); ?></span></h1>
        <p class="text-gray-600 mt-1">Assign and manage users for this conveyor</p>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Currently Assigned Users -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Currently Assigned Users</h2>
                
                <?php if (empty($assignedUsers)): ?>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding users to this conveyor from the form below.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">NIK</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Full Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Username</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($assignedUsers as $index => $user): ?>
                                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ showRemoveModal: false, userId: <?php echo $user->id; ?> }">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo ($index + 1); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo e($user->nik); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e($user->full_name); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo e($user->username); ?>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <button @click="showRemoveModal = true" 
                                                    class="text-red-600 hover:text-red-900 flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span>Remove</span>
                                            </button>

                                            <!-- Remove Confirmation Modal -->
                                            <div x-show="showRemoveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
                                                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                                    <div class="mt-3">
                                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Remove User</h3>
                                                        <p class="text-gray-600 mb-6">Are you sure you want to remove "<span class="font-semibold"><?php echo e($user->full_name); ?></span>" from this conveyor? This action cannot be undone.</p>
                                                        <div class="flex justify-end space-x-3">
                                                            <button @click="showRemoveModal = false" 
                                                                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="<?php echo url("admin/master/conveyor/remove-user/{$conveyor->id}/{$user->id}"); ?>" class="inline">
                                                                <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">
                                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                                    Remove
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
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Add Users Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Add Users</h2>
                
                <form method="POST" action="<?php echo url("admin/master/conveyor/assign-users/{$conveyor->id}"); ?>" x-data="assignUsersForm()" class="space-y-4">
                    <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">

                    <!-- Available Users Select -->
                    <div>
                        <label for="user_ids" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Users <span class="text-red-600">*</span>
                        </label>
                        
                        <?php if (empty($availableUsers)): ?>
                            <div class="bg-gray-50 p-4 rounded text-center">
                                <p class="text-sm text-gray-600">All users are already assigned to this conveyor.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <div class="relative">
                                    <input type="text" id="userSearch" @input="searchUsers($event)" 
                                           placeholder="Search by NIK or name..."
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                <!-- Users Dropdown -->
                                <div class="border border-gray-300 rounded-lg bg-white max-h-48 overflow-y-auto">
                                    <?php foreach ($availableUsers as $user): ?>
                                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" x-data="{ visible: true }" :style="{ display: visible ? 'flex' : 'none' }" class="user-item" data-nik="<?php echo strtolower($user->nik); ?>" data-name="<?php echo strtolower($user->full_name); ?>">
                                            <input type="checkbox" name="user_ids[]" value="<?php echo $user->id; ?>" @change="updateSelected()"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900"><?php echo e($user->full_name); ?></p>
                                                <p class="text-xs text-gray-500"><?php echo e($user->nik); ?> - <?php echo e($user->username); ?></p>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Selected Count -->
                            <div class="mt-3 p-3 bg-blue-50 rounded">
                                <p class="text-sm text-blue-800" x-text="`Selected: ${selectedCount} user(s)`"></p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" :disabled="selectedCount === 0"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                Assign Selected Users
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function assignUsersForm() {
    return {
        selectedCount: 0,
        
        searchUsers(event) {
            const query = event.target.value.toLowerCase();
            const items = document.querySelectorAll('.user-item');
            
            items.forEach(item => {
                const nik = item.dataset.nik;
                const name = item.dataset.name;
                const visible = nik.includes(query) || name.includes(query);
                item.style.display = visible ? 'flex' : 'none';
            });
        },
        
        updateSelected() {
            const checkboxes = document.querySelectorAll('input[name="user_ids[]"]:checked');
            this.selectedCount = checkboxes.length;
        }
    };
}
</script>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
