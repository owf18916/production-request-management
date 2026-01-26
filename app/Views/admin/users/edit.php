<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
            <p class="mt-2 text-gray-600">Update user information and conveyor assignments</p>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo url("/admin/users/update/{$user->id}"); ?>" class="bg-white p-8 rounded-lg shadow">
            <input type="hidden" name="_csrf_token" value="<?php echo isset($_SESSION['_csrf_token']) ? htmlspecialchars($_SESSION['_csrf_token']) : ''; ?>">

            <!-- User Info Card -->
            <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Created At</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo date('M d, Y H:i', strtotime($user->created_at)); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Login</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo $user->last_login_at ? date('M d, Y H:i', strtotime($user->last_login_at)) : 'Never'; ?></p>
                    </div>
                </div>
            </div>

            <!-- NIK -->
            <div class="mb-6">
                <label for="nik" class="block text-sm font-medium text-gray-700">NIK *</label>
                <input 
                    type="text" 
                    name="nik" 
                    id="nik" 
                    placeholder="Enter NIK (alphanumeric)" 
                    maxlength="50"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($_SESSION['errors']['nik']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo isset($_POST['nik']) ? htmlspecialchars($_POST['nik']) : htmlspecialchars($user->nik); ?>"
                >
                <?php if (isset($_SESSION['errors']['nik'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['nik']); ?></p>
                    <?php unset($_SESSION['errors']['nik']); ?>
                <?php endif; ?>
            </div>

            <!-- Full Name -->
            <div class="mb-6">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                <input 
                    type="text" 
                    name="full_name" 
                    id="full_name" 
                    placeholder="Enter full name" 
                    maxlength="100"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($_SESSION['errors']['full_name']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($user->full_name); ?>"
                >
                <?php if (isset($_SESSION['errors']['full_name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['full_name']); ?></p>
                    <?php unset($_SESSION['errors']['full_name']); ?>
                <?php endif; ?>
            </div>

            <!-- Username -->
            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    placeholder="Enter username (alphanumeric + underscore)" 
                    maxlength="50"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($_SESSION['errors']['username']) ? 'border-red-500' : ''; ?>"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : htmlspecialchars($user->username); ?>"
                >
                <?php if (isset($_SESSION['errors']['username'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['username']); ?></p>
                    <?php unset($_SESSION['errors']['username']); ?>
                <?php endif; ?>
            </div>

            <!-- Role (Read-only info) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <div class="mt-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                        <?php echo htmlspecialchars($user->role); ?>
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Role cannot be changed. Contact administrator to change user role.</p>
            </div>

            <!-- Password Section (Optional) -->
            <div x-data="{ showPassword: false, showConfirm: false, passwordStrength: 0 }" class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">New Password (Optional)</label>
                <p class="text-sm text-gray-500 mb-2">Leave blank to keep current password</p>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Enter new password (min 6 characters)" 
                        :type="showPassword ? 'text' : 'password'"
                        @input="passwordStrength = $el.value.length >= 6 ? 3 : (($el.value.length >= 4) ? 2 : 1)"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['password']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute right-3 top-4 text-gray-600 hover:text-gray-900"
                    >
                        <span x-show="!showPassword">👁️</span>
                        <span x-show="showPassword">👁️‍🗨️</span>
                    </button>
                </div>
                <div class="mt-2 h-2 bg-gray-200 rounded">
                    <div 
                        class="h-full rounded transition-all"
                        :class="passwordStrength === 1 ? 'w-1/3 bg-red-500' : passwordStrength === 2 ? 'w-2/3 bg-yellow-500' : 'w-full bg-green-500'"
                        x-show="passwordStrength > 0"
                    ></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    <span x-show="passwordStrength === 1">Weak</span>
                    <span x-show="passwordStrength === 2">Fair</span>
                    <span x-show="passwordStrength === 3">Strong</span>
                </p>
                <?php if (isset($_SESSION['errors']['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['password']); ?></p>
                    <?php unset($_SESSION['errors']['password']); ?>
                <?php endif; ?>
            </div>

            <!-- Confirm Password -->
            <div x-data="{ showConfirm: false }" class="mb-6">
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm New Password (Optional)</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password_confirm" 
                        id="password_confirm" 
                        placeholder="Confirm new password" 
                        :type="showConfirm ? 'text' : 'password'"
                        @input="if(document.getElementById('password').value && $el.value !== document.getElementById('password').value) $el.classList.add('border-red-500'); else $el.classList.remove('border-red-500')"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['password_confirm']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['password_confirm']) ? htmlspecialchars($_POST['password_confirm']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showConfirm = !showConfirm" 
                        class="absolute right-3 top-4 text-gray-600 hover:text-gray-900"
                    >
                        <span x-show="!showConfirm">👁️</span>
                        <span x-show="showConfirm">👁️‍🗨️</span>
                    </button>
                </div>
                <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['password_confirm']); ?></p>
                    <?php unset($_SESSION['errors']['password_confirm']); ?>
                <?php endif; ?>
            </div>

            <!-- Conveyors Multi-select -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-3">Assign Conveyors</label>
                <div class="space-y-2">
                    <?php if (empty($conveyors)): ?>
                        <p class="text-gray-500 italic">No conveyors available</p>
                    <?php else: ?>
                        <?php foreach ($conveyors as $conveyor): ?>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="conveyors[]" 
                                    value="<?php echo $conveyor->id; ?>"
                                    <?php 
                                    $checked = false;
                                    if (isset($_POST['conveyors'])) {
                                        $checked = in_array($conveyor->id, $_POST['conveyors']);
                                    } else {
                                        $checked = in_array($conveyor->id, $userConveyorIds);
                                    }
                                    echo $checked ? 'checked' : '';
                                    ?>
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-gray-700"><?php echo htmlspecialchars($conveyor->conveyor_name); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold"
                >
                    Update User
                </button>
                <a 
                    href="<?php echo url('/admin/users'); ?>" 
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

<!-- Alpine.js is loaded from main layout -->
