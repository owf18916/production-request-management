<!-- Request Details View -->
<?php
$content = ob_get_clean();
ob_start();
?>

<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="<?php echo url('requests'); ?>" class="text-blue-600 hover:text-blue-700">← Back to Requests</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Request #<?php echo e($request_id); ?></h1>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">Request details will be displayed here.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$data['content'] = $content;
extract($data);
require __DIR__ . '/../layouts/main.php';
?>
