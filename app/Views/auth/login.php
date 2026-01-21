<?php
$content = ob_get_clean();
ob_start();
?>

<div class="min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-white mb-2">Production Request</h1>
            <p class="text-blue-100">Management System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-xl p-8" x-data="loginForm()">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Sign In</h2>

            <!-- Flash Messages -->
            <?php if (session('error')): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700">
                        <span class="font-medium">Error:</span> <?php echo e(session('error')); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (session('success')): ?>
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">
                        <span class="font-medium">Success:</span> <?php echo e(session('success')); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?php echo url('login'); ?>" @submit="handleSubmit" class="space-y-5">
                <!-- CSRF Token -->
                <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">

                <!-- Identifier (Username or NIK) -->
                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-2">
                        Username or NIK
                    </label>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        x-model="form.identifier"
                        @blur="validateIdentifier"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        :class="errors.identifier ? 'border-red-500' : 'border-gray-300'"
                        placeholder="Enter username or NIK">
                    <p x-show="errors.identifier" class="mt-1 text-sm text-red-600" x-text="errors.identifier"></p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            x-model="form.password"
                            @blur="validatePassword"
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            :class="errors.password ? 'border-red-500' : 'border-gray-300'"
                            placeholder="••••••••">
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                <path d="M15.171 13.576l1.47 1.47a10.016 10.016 0 01-1.909 2.956 10.016 10.016 0 01-5.744 3.134 9.960 9.960 0 01-4.512-1.074l1.781-1.781a8 8 0 004.513 1.254c3.846 0 7.298-2.652 8.436-6.302a8.002 8.002 0 00-1.035-2.957z"></path>
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password" class="mt-1 text-sm text-red-600" x-text="errors.password"></p>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember_me" 
                        name="remember_me"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600 cursor-pointer">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    :disabled="loading || !isFormValid"
                    class="w-full bg-blue-600 text-white font-medium py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Sign In</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>
            </form>

            <!-- Demo Credentials Info -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-900 mb-3">Demo Credentials:</p>
                <div class="space-y-2 text-xs text-blue-800">
                    <div>
                        <span class="font-medium">Admin:</span> 
                        <code class="bg-blue-100 px-2 py-0.5 rounded">admin</code> / 
                        <code class="bg-blue-100 px-2 py-0.5 rounded">admin123</code>
                    </div>
                    <div>
                        <span class="font-medium">PIC:</span> 
                        <code class="bg-blue-100 px-2 py-0.5 rounded">pic</code> / 
                        <code class="bg-blue-100 px-2 py-0.5 rounded">pic123</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-6 text-sm text-blue-100">
            &copy; <?php echo date('Y'); ?> Production Request Management System
        </p>
    </div>
</div>

<script>
    function loginForm() {
        return {
            form: {
                identifier: '',
                password: '',
            },
            errors: {
                identifier: '',
                password: '',
            },
            showPassword: false,
            loading: false,
            
            get isFormValid() {
                return this.form.identifier.trim().length > 0 && 
                       this.form.password.length >= 6 && 
                       !this.errors.identifier && 
                       !this.errors.password;
            },
            
            validateIdentifier() {
                if (this.form.identifier.trim().length < 2) {
                    this.errors.identifier = 'Username or NIK must be at least 2 characters';
                } else {
                    this.errors.identifier = '';
                }
            },
            
            validatePassword() {
                if (this.form.password.length < 6) {
                    this.errors.password = 'Password must be at least 6 characters';
                } else {
                    this.errors.password = '';
                }
            },
            
            handleSubmit(e) {
                this.validateIdentifier();
                this.validatePassword();
                
                if (!this.isFormValid) {
                    e.preventDefault();
                }
            }
        }
    }
</script>

<?php
$content = ob_get_clean();
$data['content'] = $content;
extract($data);
require __DIR__ . '/../layouts/main.php';
?>
