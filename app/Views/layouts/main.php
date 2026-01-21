<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrfToken()); ?>">
    <title><?php echo isset($title) ? e($title) . ' - ' : ''; echo e(config('app.name', 'Production Request Management System')); ?></title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('css/style.css'); ?>">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="<?php echo url('/'); ?>" class="text-xl font-bold text-blue-600">
                            PRM System
                        </a>
                    </div>
                </div>

                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <?php if (session('user_id')): ?>
                        <a href="<?php echo url('dashboard'); ?>" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-700 hover:text-gray-900 flex items-center">
                                <span><?php echo e(session('user_name', 'User')); ?></span>
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-10">
                                <a href="<?php echo url('profile'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="<?php echo url('logout'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo url('login'); ?>" class="text-gray-700 hover:text-gray-900">Login</a>
                        <a href="<?php echo url('register'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button x-data x-on:click="$dispatch('toggle-menu')"
                            class="text-gray-700 hover:text-gray-900">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <?php echo e(getFlash('success')); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (hasFlash('error')): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <?php echo e(getFlash('error')); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> <?php echo e(config('app.name', 'Production Request Management System')); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="<?php echo url('js/app.js'); ?>"></script>
</body>
</html>
