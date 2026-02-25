<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Stock ATK</h1>
            <p class="mt-2 text-gray-600">Kelola stock barang ATK - incoming, adjustment, dan history</p>
        </div>

        <!-- Search Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="<?php echo url('/admin/atk-stock'); ?>" class="flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari barang..." 
                        value="<?php echo htmlspecialchars($search); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                >
                    Cari
                </button>
                <?php if ($search): ?>
                    <a 
                        href="<?php echo url('/admin/atk-stock'); ?>" 
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium"
                    >
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Stock List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Awal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">In</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Out</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Adj</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($stocks)): ?>
                            <?php foreach ($stocks as $stock): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        <?php echo htmlspecialchars($stock->kode_barang ?? '-'); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($stock->nama_barang); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">
                                        <?php echo $stock->beginning_stock; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-700 font-semibold">
                                        +<?php echo $stock->in_qty; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-700 font-semibold">
                                        -<?php echo $stock->out_qty; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-orange-700 font-semibold">
                                        <?php 
                                            $adjSign = $stock->adjustment >= 0 ? '+' : '';
                                            echo $adjSign . $stock->adjustment;
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold">
                                        <span class="px-3 py-1 rounded-full text-white <?php echo $stock->ending_stock > 0 ? 'bg-green-600' : 'bg-red-600'; ?>">
                                            <?php echo $stock->ending_stock; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a 
                                                href="<?php echo url("/admin/atk-stock/add-incoming/{$stock->atk_id}"); ?>"
                                                class="text-blue-600 hover:text-blue-900 font-medium"
                                            >
                                                In
                                            </a>
                                            <a 
                                                href="<?php echo url("/admin/atk-stock/adjustment/{$stock->atk_id}"); ?>"
                                                class="text-orange-600 hover:text-orange-900 font-medium"
                                            >
                                                Adj
                                            </a>
                                            <a 
                                                href="<?php echo url("/admin/atk-stock/transaction-history/{$stock->atk_id}"); ?>"
                                                class="text-gray-600 hover:text-gray-900 font-medium"
                                            >
                                                History
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data stock ATK
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($stocks)): ?>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900"><?php echo $totalCount; ?></span> item(s)
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
