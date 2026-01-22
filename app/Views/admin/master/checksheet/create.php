<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Add Master Checksheet</h1>
            <p class="mt-2 text-gray-600">Create a new inspection checksheet</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="<?php echo url('/admin/master/checksheet/store'); ?>" class="space-y-6">
                <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

                <!-- Kode Checksheet -->
                <div>
                    <label for="kode_checksheet" class="block text-sm font-medium text-gray-700">Kode Checksheet *</label>
                    <input type="text" id="kode_checksheet" name="kode_checksheet" 
                           placeholder="e.g., CS-001" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="<?php echo isset($_POST['kode_checksheet']) ? htmlspecialchars($_POST['kode_checksheet']) : ''; ?>"
                           required>
                    <?php if (isset($errors) && isset($errors['kode_checksheet'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $errors['kode_checksheet']; ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Uppercase letters, numbers, and hyphens only</p>
                </div>

                <!-- Nama Checksheet -->
                <div>
                    <label for="nama_checksheet" class="block text-sm font-medium text-gray-700">Nama Checksheet *</label>
                    <input type="text" id="nama_checksheet" name="nama_checksheet" 
                           placeholder="e.g., Daily Production Inspection" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="<?php echo isset($_POST['nama_checksheet']) ? htmlspecialchars($_POST['nama_checksheet']) : ''; ?>"
                           maxlength="150" required>
                    <?php if (isset($errors) && isset($errors['nama_checksheet'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $errors['nama_checksheet']; ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Max 150 characters</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Create Checksheet
                    </button>
                    <a href="<?php echo url('/admin/master/checksheet'); ?>" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm text-blue-700">
                    <strong>Tips:</strong> Use a unique code for each checksheet to make them easier to identify and search.
                </div>
            </div>
        </div>
    </div>
</div>
