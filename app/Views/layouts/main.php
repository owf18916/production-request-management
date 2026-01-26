<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrfToken()); ?>">
    <title><?php echo isset($title) ? e($title) . ' - ' : ''; echo e(config('app.name', 'Production Request Management System')); ?></title>

    <!-- TailwindCSS - Compiled locally -->
    <link rel="stylesheet" href="<?php echo asset('css/tailwind.css'); ?>">

    <!-- Alpine.js - Local version -->
    <script src="<?php echo asset('js/alpine.js'); ?>" defer></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body class="bg-gray-50" x-data>
    <!-- Header Navigation -->
    <?php require __DIR__ . '/header.php'; ?>

    <!-- Main Content -->
    <main class="pt-16 w-full">
            <!-- Flash Messages -->
            <?php if (hasFlash('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-400 p-4 m-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
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
                <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
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

            <!-- Page Content -->
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
    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
