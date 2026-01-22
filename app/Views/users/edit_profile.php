<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
            <p class="mt-2 text-gray-600">Update your personal information</p>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo url('/update-profile'); ?>" class="bg-white p-8 rounded-lg shadow">
            <input type="hidden" name="_csrf_token" value="<?php echo isset($_SESSION['_csrf_token']) ? htmlspecialchars($_SESSION['_csrf_token']) : ''; ?>">

            <!-- NIK (Read-only) -->
            <div class="mb-6">
                <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                <input 
                    type="text" 
                    id="nik" 
                    disabled
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700"
                    value="<?php echo htmlspecialchars($user->nik); ?>"
                >
                <p class="mt-2 text-sm text-gray-500">NIK cannot be changed</p>
            </div>

            <!-- Username (Read-only) -->
            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    disabled
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700"
                    value="<?php echo htmlspecialchars($user->username); ?>"
                >
                <p class="mt-2 text-sm text-gray-500">Username cannot be changed</p>
            </div>

            <!-- Full Name (Editable) -->
            <div class="mb-6">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input 
                    type="text" 
                    name="full_name" 
                    id="full_name" 
                    placeholder="Enter your full name" 
                    maxlength="100"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($_SESSION['errors']['full_name']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($user->full_name); ?>"
                >
                <?php if (isset($_SESSION['errors']['full_name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['full_name']); ?></p>
                    <?php unset($_SESSION['errors']['full_name']); ?>
                <?php endif; ?>
            </div>

            <!-- Role (Read-only) -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <div class="mt-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                        <?php echo htmlspecialchars($user->role); ?>
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Role cannot be changed by user</p>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold"
                >
                    Save Changes
                </button>
                <a 
                    href="<?php echo url('/profile'); ?>" 
                    class="flex-1 px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold text-center"
                >
                    Cancel
                </a>
            </div>

            <!-- Clear session errors -->
            <?php if (isset($_SESSION['errors'])): ?>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
        </form>
    </div>
</div>
