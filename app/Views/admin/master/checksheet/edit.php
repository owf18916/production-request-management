<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Master Checksheet</h1>
            <p class="mt-2 text-gray-600">Update inspection checksheet details</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="<?php echo url("/admin/master/checksheet/update/{$checksheet->id}"); ?>" class="space-y-6">
                <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

                <!-- Kode Checksheet -->
                <div>
                    <label for="kode_checksheet" class="block text-sm font-medium text-gray-700">Kode Checksheet *</label>
                    <input type="text" id="kode_checksheet" name="kode_checksheet" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="<?php echo htmlspecialchars($checksheet->kode_checksheet); ?>"
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
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="<?php echo htmlspecialchars($checksheet->nama_checksheet); ?>"
                           maxlength="150" required>
                    <?php if (isset($errors) && isset($errors['nama_checksheet'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo $errors['nama_checksheet']; ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-500">Max 150 characters</p>
                </div>

                <!-- Info -->
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span><strong>Created:</strong> <?php echo date('d/m/Y H:i', strtotime($checksheet->created_at)); ?></span>
                        <span><strong>Updated:</strong> <?php echo date('d/m/Y H:i', strtotime($checksheet->updated_at)); ?></span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Update Checksheet
                    </button>
                    <a href="<?php echo url('/admin/master/checksheet'); ?>" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
