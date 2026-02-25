<!-- Main Navigation Header -->
<nav class="bg-white shadow sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?php echo url('/'); ?>" class="text-2xl font-bold text-blue-600">
                        PRM
                    </a>
                    <span class="ml-2 text-xs text-gray-600 font-medium">Production Request Management</span>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <?php if (session('user_id')): ?>
                    
                    <!-- Dashboard Link -->
                    <a href="<?php echo url('dashboard'); ?>" class="text-gray-700 hover:text-blue-600 font-medium text-sm transition-colors">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 12l2 2 4-4"></path>
                        </svg>
                        Dashboard
                    </a>

                    <?php if (session('user_role') === 'admin'): ?>
                        
                        <!-- Admin Requests Dropdown -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-blue-600 font-medium text-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Requests
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="<?php echo url('/admin/requests/atk'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">ATK Requests</a>
                                <a href="<?php echo url('/admin/request_checksheet'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Checksheet Requests</a>
                                <a href="<?php echo url('/admin/request-id'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">ID Requests</a>
                                <a href="<?php echo url('/admin/requests/memo'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Internal Memo</a>
                            </div>
                        </div>

                        <!-- Master Data Dropdown -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-blue-600 font-medium text-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4l8-4M3 7v10l8 4m0 0l8-4M3 7l8 4"></path>
                                </svg>
                                Master Data
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="<?php echo url('/admin/master/atk'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Master ATK</a>
                                <a href="<?php echo url('/admin/atk-stock'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Stock Management</a>
                                <a href="<?php echo url('/admin/master/checksheet'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Master Checksheet</a>
                                <a href="<?php echo url('/admin/master/conveyor'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Manage Conveyors</a>
                            </div>
                        </div>

                    <?php else: ?>
                        
                        <!-- PIC My Requests Dropdown -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-blue-600 font-medium text-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                My Requests
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <a href="<?php echo url('/requests/atk'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">ATK Requests</a>
                                <a href="<?php echo url('/request_checksheet'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Checksheet Requests</a>
                                <a href="<?php echo url('/request-id'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">ID Requests</a>
                                <a href="<?php echo url('/requests/memo'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Internal Memo</a>
                            </div>
                        </div>

                    <?php endif; ?>

                    <!-- Profile Dropdown -->
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600 font-medium text-sm transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <?php echo e(session('user_name', 'User')); ?>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-0 w-48 bg-white rounded-md shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="<?php echo url('profile'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">My Profile</a>
                            <a href="<?php echo url('change-password'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Change Password</a>
                            <?php if (session('user_role') === 'admin'): ?>
                                <hr class="my-1">
                                <a href="<?php echo url('/admin/users'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Manage Users</a>
                            <?php endif; ?>
                            <hr class="my-1">
                            <a href="<?php echo url('logout'); ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Logout</a>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="<?php echo url('login'); ?>" class="text-gray-700 hover:text-blue-600 font-medium text-sm">Login</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button x-data="{ open: false }" @click="open = !open" class="text-gray-700 hover:text-blue-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-data="{ open: false }" @click.outside="open = false" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <?php if (session('user_id')): ?>
                <a href="<?php echo url('dashboard'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Dashboard</a>
                
                <?php if (session('user_role') === 'admin'): ?>
                    <a href="<?php echo url('/admin/requests/atk'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">ATK Requests</a>
                    <a href="<?php echo url('/admin/master/atk'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Master Data</a>
                    <a href="<?php echo url('/admin/atk-stock'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Stock Management</a>
                    <a href="<?php echo url('/admin/users'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Manage Users</a>
                <?php else: ?>
                    <a href="<?php echo url('/requests/atk'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">My ATK Requests</a>
                    <a href="<?php echo url('/request_checksheet'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Checksheet Requests</a>
                <?php endif; ?>
                
                <hr class="my-2">
                <a href="<?php echo url('profile'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">My Profile</a>
                <a href="<?php echo url('logout'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">Logout</a>
            <?php else: ?>
                <a href="<?php echo url('login'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
