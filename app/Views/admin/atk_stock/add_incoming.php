<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo url('/admin/atk-stock'); ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center gap-1 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Manajemen Stock
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Stock Masuk</h1>
            <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($atk->nama_barang); ?></p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <!-- Current Stock Display -->
            <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Kode Barang</p>
                    <p class="text-lg font-mono font-bold text-gray-900"><?php echo htmlspecialchars($atk->kode_barang); ?></p>
                </div>
                <div>
                    <p class="text-sm text-blue-600 font-medium">Stock Saat Ini</p>
                    <p class="text-lg font-bold text-gray-900"><?php echo $stock->ending_stock; ?> unit</p>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="<?php echo url("/admin/atk-stock/store-incoming/{$atk->id}"); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Stock Masuk *</label>
                    <input 
                        type="number" 
                        name="qty" 
                        required 
                        min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: 100"
                    >
                    <p class="text-xs text-gray-500 mt-1">Masukkan jumlah barang yang diterima</p>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea 
                        name="notes" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: Barang dari supplier ABC, batch 2024-01..."
                    ></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                    >
                        Simpan Stock Masuk
                    </button>
                    <a 
                        href="<?php echo url('/admin/atk-stock'); ?>"
                        class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-900">
                <strong>Catatan:</strong> Stock ini akan ditambahkan ke gudang dan akan mengurangi jumlah unit ketika request ATK diapprove oleh admin.
            </p>
        </div>
    </div>
</div>
