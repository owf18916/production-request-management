<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-2 text-gray-600">View and manage your profile information</p>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                <div class="flex items-center">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($user->full_name); ?></h2>
                        <p class="text-blue-100">@<?php echo htmlspecialchars($user->username); ?></p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-white font-semibold <?php echo $user->role === 'admin' ? 'bg-blue-700' : 'bg-green-600'; ?>">
                        <?php echo strtoupper($user->role); ?>
                    </span>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="px-6 py-8">
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">NIK</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1"><?php echo htmlspecialchars($user->nik); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Username</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1"><?php echo htmlspecialchars($user->username); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Member Since</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1"><?php echo date('M d, Y', strtotime($user->created_at)); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Last Login</p>
                        <p class="text-lg text-gray-900 font-semibold mt-1">
                            <?php echo $user->last_login_at ? date('M d, Y H:i', strtotime($user->last_login_at)) : 'Never'; ?>
                        </p>
                    </div>
                </div>

                <!-- Conveyors Section -->
                <div class="border-t pt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Conveyors</h3>
                    <?php if (empty($conveyors)): ?>
                        <p class="text-gray-500 italic">You are not assigned to any conveyors yet</p>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($conveyors as $conveyor): ?>
                                <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                                    <?php echo htmlspecialchars($conveyor->conveyor_name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="border-t mt-8 pt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex gap-4 flex-wrap">
                        <a 
                            href="<?php echo url('/edit-profile'); ?>" 
                            class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold"
                        >
                            Edit Profile
                        </a>
                        <a 
                            href="<?php echo url('/change-password'); ?>" 
                            class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-semibold"
                        >
                            Change Password
                        </a>
                        <a 
                            href="<?php echo url('/dashboard'); ?>" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-semibold"
                        >
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
