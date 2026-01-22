<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
            <p class="mt-2 text-gray-600">Add a new user to the system</p>
        </div>

        <!-- Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo url('/admin/users/store'); ?>" class="bg-white p-8 rounded-lg shadow">
            <input type="hidden" name="_csrf_token" value="<?php echo isset($_SESSION['_csrf_token']) ? htmlspecialchars($_SESSION['_csrf_token']) : ''; ?>">

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
                    value="<?php echo isset($_POST['nik']) ? htmlspecialchars($_POST['nik']) : ''; ?>"
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
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
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
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
                <?php if (isset($_SESSION['errors']['username'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['username']); ?></p>
                    <?php unset($_SESSION['errors']['username']); ?>
                <?php endif; ?>
            </div>

            <!-- Password Section -->
            <div x-data="{ showPassword: false, showConfirm: false, passwordStrength: 0 }" class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Enter password (min 6 characters)" 
                        :type="showPassword ? 'text' : 'password'"
                        @input="passwordStrength = $el.value.length >= 6 ? 3 : (($el.value.length >= 4) ? 2 : 1)"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['password']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>"
                    >
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword" 
                        class="absolute right-3 top-3 text-gray-600 hover:text-gray-900"
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
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password_confirm" 
                        id="password_confirm" 
                        placeholder="Confirm password" 
                        :type="showConfirm ? 'text' : 'password'"
                        @input="if($el.value !== document.getElementById('password').value) $el.classList.add('border-red-500'); else $el.classList.remove('border-red-500')"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 <?php echo isset($_SESSION['errors']['password_confirm']) ? 'border-red-500' : ''; ?>"
                        value="<?php echo isset($_POST['password_confirm']) ? htmlspecialchars($_POST['password_confirm']) : ''; ?>"
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
                <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['password_confirm']); ?></p>
                    <?php unset($_SESSION['errors']['password_confirm']); ?>
                <?php endif; ?>
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700">Role *</label>
                <select 
                    name="role" 
                    id="role" 
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo isset($_SESSION['errors']['role']) ? 'border-red-500' : ''; ?>"
                >
                    <option value="">-- Select Role --</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="pic" <?php echo (isset($_POST['role']) && $_POST['role'] === 'pic') ? 'selected' : ''; ?>>PIC (Production In-Charge)</option>
                </select>
                <?php if (isset($_SESSION['errors']['role'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['role']); ?></p>
                    <?php unset($_SESSION['errors']['role']); ?>
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
                                    <?php echo (isset($_POST['conveyors']) && in_array($conveyor->id, $_POST['conveyors'])) ? 'checked' : ''; ?>
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
                    Create User
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

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
