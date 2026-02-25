<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo url('/admin/atk-stock'); ?>" class="text-blue-600 hover:text-blue-900 text-sm font-medium flex items-center gap-1 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Manajemen Stock
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Riwayat Transaksi Stock</h1>
            <p class="mt-2 text-gray-600"><?php echo htmlspecialchars($atk->nama_barang); ?></p>
        </div>

        <!-- Stock Summary -->
        <div class="grid grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Stock Awal</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $stock->beginning_stock; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-green-600 font-medium">Masuk</p>
                <p class="text-2xl font-bold text-green-700">+<?php echo $stock->in_qty; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-red-600 font-medium">Keluar</p>
                <p class="text-2xl font-bold text-red-700">-<?php echo $stock->out_qty; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-orange-600 font-medium">Penyesuaian</p>
                <p class="text-2xl font-bold text-orange-700">
                    <?php 
                        $adjSign = $stock->adjustment >= 0 ? '+' : '';
                        echo $adjSign . $stock->adjustment;
                    ?>
                </p>
            </div>
            <div class="bg-blue-100 rounded-lg shadow p-4">
                <p class="text-sm text-blue-700 font-medium">Stock Akhir</p>
                <p class="text-2xl font-bold text-blue-900"><?php echo $stock->ending_stock; ?></p>
            </div>
        </div>

        <!-- Transaction History Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal/Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Sebelum</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Sesudah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php
                                            $typeLabels = [
                                                'beginning' => 'Awal',
                                                'incoming' => 'Masuk',
                                                'out' => 'Keluar',
                                                'adjustment' => 'Penyesuaian',
                                                'restore' => 'Kembali'
                                            ];
                                            $typeColors = [
                                                'beginning' => 'bg-gray-100 text-gray-800',
                                                'incoming' => 'bg-green-100 text-green-800',
                                                'out' => 'bg-red-100 text-red-800',
                                                'adjustment' => 'bg-orange-100 text-orange-800',
                                                'restore' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $typeLabel = $typeLabels[$item->transaction_type] ?? ucfirst($item->transaction_type);
                                            $typeColor = $typeColors[$item->transaction_type] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $typeColor; ?>">
                                            <?php echo $typeLabel; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold">
                                        <?php 
                                            $sign = $item->qty >= 0 ? '+' : '';
                                            echo $sign . $item->qty;
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                        <?php echo $item->previous_balance ?? '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                                        <?php echo $item->new_balance ?? '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                        <?php 
                                            if ($item->reference_type === 'request_atk') {
                                                echo "Request ATK #{$item->reference_id}";
                                            }
                                            if ($item->notes) {
                                                echo ($item->reference_type ? ' - ' : '') . htmlspecialchars($item->notes);
                                            }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo htmlspecialchars($item->created_by_name ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada transaksi untuk item ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Last Stocktake Info -->
        <?php if ($stock->last_stocktake_date): ?>
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <strong>Stocktake Terakhir:</strong> <?php echo date('d/m/Y H:i', strtotime($stock->last_stocktake_date)); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
