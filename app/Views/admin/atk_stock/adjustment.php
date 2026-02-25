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
            <h1 class="text-3xl font-bold text-gray-900">Penyesuaian Stock</h1>
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
            <form method="POST" action="<?php echo url("/admin/atk-stock/store-adjustment/{$atk->id}"); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penyesuaian *</label>
                    <input 
                        type="number" 
                        name="qty" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: 10 (tambah) atau -5 (kurangi)"
                    >
                    <p class="text-xs text-gray-500 mt-1">Masukkan nilai positif untuk tambah, negatif untuk kurangi. Contoh: 10 atau -5</p>
                </div>

                <!-- Reason -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penyesuaian *</label>
                    <select 
                        name="reason"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">-- Pilih Alasan --</option>
                        <option value="stock_opname">Stock Opname / Audit</option>
                        <option value="damage">Barang Rusak/Cacat</option>
                        <option value="loss">Barang Hilang</option>
                        <option value="return">Pengembalian Supplier</option>
                        <option value="correction">Koreksi Data</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Lengkap *</label>
                    <textarea 
                        name="notes" 
                        rows="4"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Jelaskan detail penyesuaian: kondisi barang, tanggal ditemukan, info tambahan..."
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Catatan ini sangat penting untuk audit trail</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium"
                    >
                        Simpan Penyesuaian
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-900">
                <strong>Catatan Penting:</strong> Penyesuaian stock harus didokumentasikan dengan baik. Semua perubahan akan dicatat di riwayat transaksi untuk audit trail.
            </p>
        </div>
    </div>
</div>
