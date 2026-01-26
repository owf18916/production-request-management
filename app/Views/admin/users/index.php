<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="mt-2 text-gray-600">Manage users and their conveyor assignments</p>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <div class="mb-6 bg-white p-6 rounded-lg shadow">
            <form method="GET" action="<?php echo url('/admin/users'); ?>" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-xs">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search by NIK, name, or username..." 
                        value="<?php echo htmlspecialchars($search ?? ''); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div class="flex gap-2">
                    <select 
                        name="role" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="pic" <?php echo $roleFilter === 'pic' ? 'selected' : ''; ?>>PIC</option>
                    </select>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                    >
                        Search
                    </button>
                    <a 
                        href="<?php echo url('/admin/users'); ?>" 
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                    >
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Create User Button -->
        <div class="mb-6">
            <a 
                href="<?php echo url('/admin/users/create'); ?>" 
                class="inline-block px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold"
            >
                + Create New User
            </a>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($users)): ?>
                <div class="p-6 text-center text-gray-500">
                    No users found
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIK
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Username
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Conveyors
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Login
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user->nik); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo htmlspecialchars($user->full_name); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo htmlspecialchars($user->username); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo htmlspecialchars($user->role); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <div class="flex flex-wrap gap-1">
                                            <?php if (!empty($user->conveyors)): ?>
                                                <?php foreach ($user->conveyors as $conveyor): ?>
                                                    <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                                        <?php echo htmlspecialchars($conveyor->conveyor_name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400 italic">No conveyors</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php 
                                        if ($user->last_login_at) {
                                            echo date('M d, Y', strtotime($user->last_login_at));
                                        } else {
                                            echo '<span class="text-gray-400">Never</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <a 
                                                href="<?php echo url("/admin/users/edit/{$user->id}"); ?>" 
                                                class="text-blue-600 hover:text-blue-900 font-semibold"
                                            >
                                                Edit
                                            </a>
                                            <form 
                                                action="<?php echo url("/admin/users/delete/{$user->id}"); ?>" 
                                                method="POST" 
                                                style="display: inline;"
                                                x-data
                                                @submit.prevent="if(confirm('Are you sure you want to delete this user?')) $el.submit()"
                                            >
                                                <input type="hidden" name="_csrf_token" value="<?php echo isset($_SESSION['_csrf_token']) ? htmlspecialchars($_SESSION['_csrf_token']) : ''; ?>">
                                                <button 
                                                    type="submit" 
                                                    class="text-red-600 hover:text-red-900 font-semibold"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <div class="mt-8 grid grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-500 text-sm font-semibold">Total Users</div>
                <div class="text-3xl font-bold text-gray-900 mt-2"><?php echo count($users); ?></div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-500 text-sm font-semibold">Admin Users</div>
                <div class="text-3xl font-bold text-gray-900 mt-2"><?php echo count(array_filter($users, fn($u) => $u->role === 'admin')); ?></div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-500 text-sm font-semibold">PIC Users</div>
                <div class="text-3xl font-bold text-gray-900 mt-2"><?php echo count(array_filter($users, fn($u) => $u->role === 'pic')); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js is loaded from main layout -->
