<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Change Password</h1>
            <p class="mt-2 text-gray-600">Update your account password</p>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Security Info -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">Password Security</h3>
            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                <li>Use at least 6 characters</li>
                <li>Mix uppercase, lowercase, and numbers for better security</li>
                <li>Avoid using common words or personal information</li>
                <li>Never share your password with anyone</li>
            </ul>
        </div>

        <!-- Form -->
        <form method="POST" action="<?php echo url('/update-password'); ?>" x-data="passwordForm()" class="bg-white p-8 rounded-lg shadow">
            <input type="hidden" name="_csrf_token" value="<?php echo isset($_SESSION['_csrf_token']) ? htmlspecialchars($_SESSION['_csrf_token']) : ''; ?>">

            <!-- Current Password -->
            <div x-data="{ showCurrent: false }" class="mb-6">
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password *</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        placeholder="Enter your current password" 
                        :type="showCurrent ? 'text' : 'password'"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['current_password']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['current_password']) ? htmlspecialchars($_POST['current_password']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showCurrent = !showCurrent" 
                        class="absolute right-3 top-3 text-gray-600 hover:text-gray-900"
                    >
                        <span x-show="!showCurrent">👁️</span>
                        <span x-show="showCurrent">👁️‍🗨️</span>
                    </button>
                </div>
                <?php if (isset($_SESSION['errors']['current_password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['current_password']); ?></p>
                    <?php unset($_SESSION['errors']['current_password']); ?>
                <?php endif; ?>
            </div>

            <!-- New Password -->
            <div x-data="{ showNew: false, passwordStrength: 0 }" class="mb-6">
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password *</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="new_password" 
                        id="new_password" 
                        placeholder="Enter new password (min 6 characters)" 
                        :type="showNew ? 'text' : 'password'"
                        @input="passwordStrength = $el.value.length >= 6 ? 3 : (($el.value.length >= 4) ? 2 : 1)"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['new_password']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['new_password']) ? htmlspecialchars($_POST['new_password']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showNew = !showNew" 
                        class="absolute right-3 top-3 text-gray-600 hover:text-gray-900"
                    >
                        <span x-show="!showNew">👁️</span>
                        <span x-show="showNew">👁️‍🗨️</span>
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
                    <span x-show="passwordStrength === 1">Weak password</span>
                    <span x-show="passwordStrength === 2">Fair password</span>
                    <span x-show="passwordStrength === 3">Strong password</span>
                </p>
                <?php if (isset($_SESSION['errors']['new_password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['new_password']); ?></p>
                    <?php unset($_SESSION['errors']['new_password']); ?>
                <?php endif; ?>
            </div>

            <!-- Confirm New Password -->
            <div x-data="{ showConfirm: false }" class="mb-6">
                <label for="new_password_confirm" class="block text-sm font-medium text-gray-700">Confirm New Password *</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="new_password_confirm" 
                        id="new_password_confirm" 
                        placeholder="Confirm new password" 
                        :type="showConfirm ? 'text' : 'password'"
                        @input="if($el.value !== document.getElementById('new_password').value) $el.classList.add('border-red-500'); else $el.classList.remove('border-red-500')"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['new_password_confirm']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['new_password_confirm']) ? htmlspecialchars($_POST['new_password_confirm']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showConfirm = !showConfirm" 
                        class="absolute right-3 top-3 text-gray-600 hover:text-gray-900"
                    >
                        <span x-show="!showConfirm">👁️</span>
                        <span x-show="showConfirm">👁️‍🗨️</span>
                    </button>
                </div>
                <?php if (isset($_SESSION['errors']['new_password_confirm'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['new_password_confirm']); ?></p>
                    <?php unset($_SESSION['errors']['new_password_confirm']); ?>
                <?php endif; ?>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold"
                >
                    Update Password
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

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
