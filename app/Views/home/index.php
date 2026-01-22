<?php
$content = ob_get_clean();
ob_start();
?>

<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900">Welcome to Production Request Management System</h1>
            <p class="mt-4 text-xl text-gray-600">A native PHP MVC application built with OOP principles</p>

            <div class="mt-8 space-x-4">
                <?php if (!session('user_id')): ?>
                    <a href="<?php echo url('login'); ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                        Login
                    </a>
                    <a href="<?php echo url('register'); ?>" class="inline-block border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                        Register
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('dashboard'); ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                        Go to Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Features -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a4 4 0 11-8 0 4 4 0 018 0zm12 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">User Management</h3>
                <p class="mt-2 text-gray-600">Manage users, roles, and permissions with an intuitive interface.</p>
            </div>

            <!-- Feature 2 -->
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Request Tracking</h3>
                <p class="mt-2 text-gray-600">Track production requests with real-time status updates and notifications.</p>
            </div>

            <!-- Feature 3 -->
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Secure & Reliable</h3>
                <p class="mt-2 text-gray-600">Built with security best practices including CSRF protection and data validation.</p>
            </div>
        </div>

        <!-- Tech Stack -->
        <div class="mt-16 bg-white rounded-lg shadow p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Technology Stack</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900">Backend</h4>
                    <ul class="mt-2 text-gray-600 text-sm space-y-1">
                        <li>PHP 7.4 / 8.2+</li>
                        <li>Native MVC Architecture</li>
                        <li>PSR-4 Autoloader</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Database</h4>
                    <ul class="mt-2 text-gray-600 text-sm space-y-1">
                        <li>MySQL 5.7+</li>
                        <li>PDO with Prepared Statements</li>
                        <li>UTF-8 Charset</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Frontend</h4>
                    <ul class="mt-2 text-gray-600 text-sm space-y-1">
                        <li>TailwindCSS</li>
                        <li>Alpine.js</li>
                        <li>Responsive Design</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Security</h4>
                    <ul class="mt-2 text-gray-600 text-sm space-y-1">
                        <li>CSRF Protection</li>
                        <li>XSS Prevention</li>
                        <li>Password Hashing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
