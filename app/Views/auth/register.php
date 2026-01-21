<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto mt-12">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Register</h1>

        <form method="POST" action="<?php echo url('register'); ?>" class="space-y-4">
            <!-- CSRF Token -->
            <input type="hidden" name="_csrf_token" value="<?php echo e(csrfToken()); ?>">

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="name" name="name" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-medium hover:bg-blue-700 transition duration-200">
                Register
            </button>
        </form>

        <!-- Login Link -->
        <p class="text-center mt-4 text-sm text-gray-600">
            Already have an account?
            <a href="<?php echo url('login'); ?>" class="text-blue-600 hover:text-blue-700 font-medium">
                Login here
            </a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$data['content'] = $content;
extract($data);
require __DIR__ . '/../layouts/main.php';
?>
