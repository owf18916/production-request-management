<div class="min-h-screen bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Dasbor Saya</h1>
            <p class="mt-2 text-sm text-gray-600">Selamat datang, <?php echo e($user_name); ?>. Berikut adalah ringkasan requests produksi Anda.</p>
        </div>
    </div>

    <!-- Conveyor & Shift Setup Alert -->
    <div id="conveyor-alert" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6" x-data="conveyorSetupForm()" x-init="loadActiveConveyor()">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800">Setup Conveyor & Shift untuk Request</h3>
                    <div class="mt-2 text-sm text-blue-700" x-show="!activeConveyor">
                        <p class="mb-3">⚠️ Anda belum setup Conveyor dan Shift. Harap setup terlebih dahulu sebelum membuat request.</p>
                        <button 
                            @click="setupModal = true"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Setup Sekarang
                        </button>
                    </div>
                    <div class="mt-2 text-sm text-blue-700" x-show="activeConveyor">
                        <p class="mb-3">✓ <strong>Setup Aktif:</strong> Conveyor: <span x-text="activeConveyor"></span>, Shift: <span x-text="activeShift"></span></p>
                        <button 
                            @click="setupModal = true"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 mr-2"
                        >
                            Ubah Setup
                        </button>
                        <button 
                            @click="clearConveyor()"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Hapus Setup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Setup Modal -->
            <div x-show="setupModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" @click="setupModal = false">
                <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4" @click.stop>
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Setup Conveyor & Shift</h3>
                        
                        <!-- Conveyor Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Conveyor</label>
                            <select 
                                x-model="formData.conveyor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">-- Pilih Conveyor --</option>
                                <template x-for="conveyor in conveyors" :key="conveyor.id">
                                    <option :value="conveyor.id" x-text="conveyor.conveyor_name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Shift Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                            <select 
                                x-model="formData.shift"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">-- Pilih Shift --</option>
                                <option value="Shift A">Shift A</option>
                                <option value="Shift B">Shift B</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div x-show="message" class="mb-4 p-3 rounded-md" :class="message.includes('successfully') ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'">
                            <p class="text-sm" x-text="message"></p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button 
                                @click="setupConveyor()"
                                type="button"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                            >
                                Simpan Setup
                            </button>
                            <button 
                                @click="setupModal = false; message = ''"
                                type="button"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Pending Requests Card -->
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Request Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $stats['pending']; ?></p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Approved Requests Card -->
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Request Disetujui</p>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $stats['approved']; ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Rejected Requests Card -->
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Request Ditolak</p>
                        <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $stats['rejected']; ?></p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Completed Requests Card -->
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Request Selesai</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $stats['completed']; ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions and Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Buat Permintaan Baru</h2>
                <div class="space-y-3">
                    <a href="<?php echo url('/requests/atk/create'); ?>" class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Request ATK
                    </a>
                    <a href="<?php echo url('/request_checksheet/create'); ?>" class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Request Checksheet
                    </a>
                    <a href="<?php echo url('/request-id/create'); ?>" class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Request ID
                    </a>
                    <a href="<?php echo url('/requests/memo/create'); ?>" class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded hover:bg-blue-700 transition-colors text-sm font-medium flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Internal Memo
                    </a>
                </div>
            </div>

            <!-- Requests by Type -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Request Saya</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request ATK</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['atk'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request Checksheet</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['checksheet'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Request ID</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['id'] ?? 0; ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Internal Memo</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full"><?php echo $requestsByType['memo'] ?? 0; ?></span>
                    </div>
                </div>
            </div>

            <!-- My Links -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h2>
                <div class="space-y-3">
                    <a href="<?php echo url('/requests/atk'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        View Request ATK
                    </a>
                    <a href="<?php echo url('/request_checksheet'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        View Request Checksheet
                    </a>
                    <a href="<?php echo url('/request-id'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        View Request ID
                    </a>
                    <a href="<?php echo url('/requests/memo'); ?>" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded hover:bg-purple-700 transition-colors text-sm font-medium">
                        View Internal Memo
                    </a>
                </div>
            </div>

        </div>

        <!-- Recent Requests Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">My Recent Requests (10 Terakhir)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Request #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($recentRequests)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No requests found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentRequests as $request): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $request->type ?? 'N/A'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #<?php echo $request->request_number ?? 'N/A'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                        <?php 
                                        if (!empty($request->checksheet_name)) {
                                            echo 'Checksheet: ' . $request->checksheet_name;
                                        } elseif (!empty($request->id_type)) {
                                            echo 'ID Type: ' . ucfirst(str_replace('_', ' ', $request->id_type));
                                        } elseif (!empty($request->memo_content)) {
                                            echo substr($request->memo_content, 0, 40) . '...';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $status = $request->status ?? 'pending';
                                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $color; ?>">
                                            <?php 
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'completed' => 'Completed'
                                            ];
                                            echo $statusLabels[$status] ?? ucfirst($status);
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo date('d M Y', strtotime($request->created_at ?? date('Y-m-d'))); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php
                                        // Determine the URL based on request type
                                        $viewUrl = '#';
                                        $type = $request->type ?? '';
                                        $id = $request->id ?? '';
                                        
                                        switch($type) {
                                            case 'ATK':
                                                $viewUrl = url('/requests/atk/show/' . $id);
                                                break;
                                            case 'Checksheet':
                                                $viewUrl = url('/request_checksheet/show/' . $id);
                                                break;
                                            case 'ID':
                                                $viewUrl = url('/request-id/' . $id);
                                                break;
                                            case 'Memo':
                                                $viewUrl = url('/requests/memo/show/' . $id);
                                                break;
                                        }
                                        ?>
                                        <a href="<?php echo $viewUrl; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script>
function conveyorSetupForm() {
    return {
        setupModal: false,
        conveyors: <?php echo json_encode($conveyors ?? []); ?>,
        formData: { conveyor_id: '', shift: '' },
        message: '',
        activeConveyor: null,
        activeShift: null,

        loadActiveConveyor() {
            fetch('<?php echo url('dashboard/get-active-conveyor-shift'); ?>')
                .then(r => r.json())
                .then(data => {
                    if (data.has_active) {
                        this.activeConveyor = data.conveyor_name;
                        this.activeShift = data.shift;
                    }
                })
                .catch(e => console.error('Error loading conveyor:', e));
        },

        setupConveyor() {
            if (!this.formData.conveyor_id || !this.formData.shift) {
                this.message = 'Harap pilih Conveyor dan Shift';
                return;
            }
            
            const conveyorName = this.conveyors.find(c => c.id == this.formData.conveyor_id)?.conveyor_name;
            
            fetch('<?php echo url('dashboard/setup-conveyor-shift'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    conveyor_id: this.formData.conveyor_id,
                    conveyor_name: conveyorName,
                    shift: this.formData.shift
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.message = data.message;
                    this.activeConveyor = data.conveyor_name;
                    this.activeShift = data.shift;
                    this.formData = { conveyor_id: '', shift: '' };
                    setTimeout(() => { 
                        this.setupModal = false; 
                        this.message = ''; 
                    }, 1500);
                } else {
                    this.message = data.message || 'Error setting up conveyor/shift';
                }
            })
            .catch(e => { 
                this.message = 'Error: ' + e.message;
                console.error(e);
            });
        },

        clearConveyor() {
            fetch('<?php echo url('dashboard/clear-conveyor-shift'); ?>', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.activeConveyor = null;
                        this.activeShift = null;
                    }
                })
                .catch(e => console.error('Error clearing conveyor:', e));
        }
    };
}
</script>