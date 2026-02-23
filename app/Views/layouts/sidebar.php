<!-- Responsive Sidebar Navigation with Alpine.js -->
<div class="flex">
    <!-- Sidebar -->
    <div x-data="{ open: true }" class="flex">
        <!-- Desktop Sidebar -->
        <aside :class="open ? 'w-64' : 'w-20'" class="hidden md:flex flex-col bg-gray-900 text-white transition-all duration-300 ease-in-out fixed h-screen pt-16 left-0 top-0 z-30 overflow-y-auto">
            
            <!-- Sidebar Toggle Button -->
            <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                <span v-show="open" class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Navigation</span>
                <button @click="open = !open" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Menu Items -->
            <nav class="flex-1 px-4 py-4 space-y-2">
                
                <!-- Dashboard -->
                <a href="<?php echo url('dashboard'); ?>" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 12l2 2 4-4"></path>
                    </svg>
                    <span v-show="open" class="ml-3 text-sm font-medium">Dashboard</span>
                </a>

                <?php if (session('user_role') === 'admin'): ?>
                    
                    <!-- Admin Section Header -->
                    <div v-show="open" class="px-4 py-3 text-xs uppercase text-gray-500 font-bold tracking-wider mt-4">Admin Tools</div>

                    <!-- Requests Section -->
                    <div x-data="{ requestsOpen: false }" class="space-y-1">
                        <button @click="requestsOpen = !requestsOpen" class="w-full flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span v-show="open" class="ml-3 text-sm font-medium flex-1 text-left">Requests</span>
                            <span v-show="open" :class="requestsOpen ? 'rotate-180' : ''" class="transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </span>
                        </button>
                        <div x-show="requestsOpen && open" class="bg-gray-800 rounded-lg py-2 px-2 space-y-1">
                            <a href="<?php echo url('/admin/requests/atk'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                                ATK Requests
                            </a>
                            <a href="<?php echo url('/admin/request_checksheet'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                                Checksheet
                            </a>
                            <a href="<?php echo url('/admin/request-id'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                                ID Requests
                            </a>
                            <a href="<?php echo url('/admin/requests/memo'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                                Internal Memo
                            </a>
                        </div>
                    </div>

                    <!-- Master Data Section -->
                    <div x-data="{ masterOpen: false }" class="space-y-1">
                        <button @click="masterOpen = !masterOpen" class="w-full flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4l8-4M3 7v10l8 4m0 0l8-4"></path>
                            </svg>
                            <span v-show="open" class="ml-3 text-sm font-medium flex-1 text-left">Master Data</span>
                            <span v-show="open" :class="masterOpen ? 'rotate-180' : ''" class="transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </span>
                        </button>
                        <div x-show="masterOpen && open" class="bg-gray-800 rounded-lg py-2 px-2 space-y-1">
                            <a href="<?php echo url('/admin/master/atk'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                                Master ATK
                            </a>
                            <a href="<?php echo url('/admin/master/checksheet'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                                Master Checksheet
                            </a>
                            <a href="<?php echo url('/admin/master/conveyor'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                                Conveyors
                            </a>
                        </div>
                    </div>

                    <!-- Users Management -->
                    <a href="<?php echo url('/admin/users'); ?>" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 10H9M9 20h6m0 0a9 9 0 110-18 9 9 0 010 18z"></path>
                        </svg>
                        <span v-show="open" class="ml-3 text-sm font-medium">Manage Users</span>
                    </a>

                <?php else: ?>
                    
                    <!-- PIC Section Header -->
                    <div v-show="open" class="px-4 py-3 text-xs uppercase text-gray-500 font-bold tracking-wider mt-4">My Workspace</div>

                    <!-- My Requests -->
                    <div x-data="{ myRequestsOpen: false }" class="space-y-1">
                        <button @click="myRequestsOpen = !myRequestsOpen" class="w-full flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span v-show="open" class="ml-3 text-sm font-medium flex-1 text-left">My Requests</span>
                            <span v-show="open" :class="myRequestsOpen ? 'rotate-180' : ''" class="transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </span>
                        </button>
                        <div x-show="myRequestsOpen && open" class="bg-gray-800 rounded-lg py-2 px-2 space-y-1">
                            <a href="<?php echo url('/requests/atk'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                                ATK Requests
                            </a>
                            <a href="<?php echo url('/request_checksheet'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                                Checksheet
                            </a>
                            <a href="<?php echo url('/request-id'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                                ID Requests
                            </a>
                            <a href="<?php echo url('/requests/memo'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded transition-colors">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                                Internal Memo
                            </a>
                        </div>
                    </div>

                    <!-- Create Request Section -->
                    <div v-show="open" class="px-4 py-3 text-xs uppercase text-gray-500 font-bold tracking-wider mt-4">Create New</div>
                    <div x-show="open" class="space-y-1 px-2">
                        <a href="<?php echo url('/requests/atk/create'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            ATK Request
                        </a>
                        <a href="<?php echo url('/request_checksheet/create'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Checksheet
                        </a>
                        <a href="<?php echo url('/request-id/create'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            ID Request
                        </a>
                        <a href="<?php echo url('/requests/memo/create'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Internal Memo
                        </a>
                    </div>

                <?php endif; ?>

            </nav>

            <!-- Sidebar Footer -->
            <div class="border-t border-gray-700 p-4 space-y-2">
                <a href="<?php echo url('profile'); ?>" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-800 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span v-show="open" class="ml-3 text-sm font-medium">Profile</span>
                </a>
                <a href="<?php echo url('logout'); ?>" class="flex items-center px-4 py-3 rounded-lg hover:bg-red-900 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-red-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span v-show="open" class="ml-3 text-sm font-medium">Logout</span>
                </a>
            </div>

        </aside>

        <!-- Main Content Offset -->
        <div class="hidden md:block" :style="open ? 'width: 256px' : 'width: 80px'" class="transition-all duration-300 flex-shrink-0"></div>
    </div>
</div>
