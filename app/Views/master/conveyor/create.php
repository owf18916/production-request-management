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
$oldInput = getFlash('old_input', []);
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
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Add New Conveyor</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add New Conveyor</h1>
        <p class="text-gray-600 mt-1">Create a new production conveyor</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?php echo url('admin/master/conveyor/store'); ?>" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">

            <!-- Conveyor Name -->
            <div>
                <label for="conveyor_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Conveyor Name <span class="text-red-600">*</span>
                </label>
                <input type="text" id="conveyor_name" name="conveyor_name" 
                       value="<?php echo e($oldInput['conveyor_name'] ?? ''); ?>"
                       placeholder="Enter conveyor name"
                       class="w-full px-4 py-2 border <?php echo isset($errors['conveyor_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <?php if (isset($errors['conveyor_name'])): ?>
                    <p class="mt-1 text-sm text-red-600">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 17.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l9-9z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo e($errors['conveyor_name']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Status <span class="text-red-600">*</span>
                </label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="radio" id="status_active" name="status" value="active" 
                               <?php echo (!isset($oldInput['status']) || $oldInput['status'] === 'active' ? 'checked' : ''); ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="status_active" class="ml-3 block text-sm text-gray-700">
                            Active
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="status_inactive" name="status" value="inactive"
                               <?php echo (isset($oldInput['status']) && $oldInput['status'] === 'inactive' ? 'checked' : ''); ?>
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="status_inactive" class="ml-3 block text-sm text-gray-700">
                            Inactive
                        </label>
                    </div>
                </div>
                <?php if (isset($errors['status'])): ?>
                    <p class="mt-2 text-sm text-red-600">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 17.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l9-9z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo e($errors['status']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="<?php echo url('admin/master/conveyor'); ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Create Conveyor
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
