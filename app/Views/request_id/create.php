<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create ID Request</h1>
            <p class="mt-2 text-gray-600">Submit a new ID request</p>
        </div>

        <!-- Form -->
        <form method="POST" action="<?php echo url('/request-id/store'); ?>" x-data="requestForm()" x-init="initForm()" class="bg-white rounded-lg shadow p-8">
            <!-- CSRF Token -->
            <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

            <!-- ID Type Selection -->
            <div class="mb-6">
                <label class="block text-lg font-medium text-gray-900 mb-4">Select ID Type</label>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" id="id_punggung" name="id_type" value="id_punggung" 
                               @change="selectedType = 'id_punggung'" 
                               <?php echo ($formData['id_type'] ?? '') === 'id_punggung' ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600">
                        <label for="id_punggung" class="ml-3 text-gray-700 font-medium cursor-pointer">
                            ID Punggung
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="pin_4m" name="id_type" value="pin_4m" 
                               @change="selectedType = 'pin_4m'" 
                               <?php echo ($formData['id_type'] ?? '') === 'pin_4m' ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600">
                        <label for="pin_4m" class="ml-3 text-gray-700 font-medium cursor-pointer">
                            PIN 4M
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="id_kaki" name="id_type" value="id_kaki" 
                               @change="selectedType = 'id_kaki'" 
                               <?php echo ($formData['id_type'] ?? '') === 'id_kaki' ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600">
                        <label for="id_kaki" class="ml-3 text-gray-700 font-medium cursor-pointer">
                            ID Kaki
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="job_psd" name="id_type" value="job_psd" 
                               @change="selectedType = 'job_psd'" 
                               <?php echo ($formData['id_type'] ?? '') === 'job_psd' ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600">
                        <label for="job_psd" class="ml-3 text-gray-700 font-medium cursor-pointer">
                            Job PSD
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="id_other" name="id_type" value="id_other" 
                               @change="selectedType = 'id_other'" 
                               <?php echo ($formData['id_type'] ?? '') === 'id_other' ? 'checked' : ''; ?>
                               class="h-4 w-4 text-blue-600">
                        <label for="id_other" class="ml-3 text-gray-700 font-medium cursor-pointer">
                            ID Other
                        </label>
                    </div>
                </div>

                <?php if (isset($errors['id_type'])): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['id_type']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Dynamic Form Fields -->
            <div x-show="selectedType" class="mb-6 p-6 bg-blue-50 rounded-lg border border-blue-200">
                <!-- ID Punggung Fields -->
                <div x-show="selectedType === 'id_punggung'" class="space-y-4">
                    <div>
                        <label for="job" class="block text-sm font-medium text-gray-700 mb-1">Job <span class="text-red-600">*</span></label>
                        <input type="text" id="job" name="job" value="<?php echo htmlspecialchars($formData['job'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['job'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['job']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="keterangan_punggung" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                        <select id="keterangan_punggung" name="keterangan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            <option value="Asli" <?php echo ($formData['keterangan'] ?? '') === 'Asli' ? 'selected' : ''; ?>>Asli</option>
                            <option value="Perbaikan" <?php echo ($formData['keterangan'] ?? '') === 'Perbaikan' ? 'selected' : ''; ?>>Perbaikan</option>
                        </select>
                        <?php if (isset($errors['keterangan'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['keterangan']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="qty_punggung" class="block text-sm font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                        <input type="number" id="qty_punggung" name="qty" value="<?php echo htmlspecialchars($formData['qty'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['qty'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['qty']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="warna" class="block text-sm font-medium text-gray-700 mb-1">Warna <span class="text-red-600">*</span></label>
                        <input type="text" id="warna" name="warna" value="<?php echo htmlspecialchars($formData['warna'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['warna'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['warna']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PIN 4M Fields -->
                <div x-show="selectedType === 'pin_4m'" class="space-y-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-600">*</span></label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($formData['nama'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['nama'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['nama']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-600">*</span></label>
                        <input type="text" id="nik" name="nik" value="<?php echo htmlspecialchars($formData['nik'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['nik'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['nik']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="matrix_skill" class="block text-sm font-medium text-gray-700 mb-1">Matrix Skill <span class="text-red-600">*</span></label>
                        <input type="text" id="matrix_skill" name="matrix_skill" value="<?php echo htmlspecialchars($formData['matrix_skill'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['matrix_skill'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['matrix_skill']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="keterangan_pin" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                        <select id="keterangan_pin" name="keterangan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            <option value="Bulat" <?php echo ($formData['keterangan'] ?? '') === 'Bulat' ? 'selected' : ''; ?>>Bulat</option>
                            <option value="Kotak" <?php echo ($formData['keterangan'] ?? '') === 'Kotak' ? 'selected' : ''; ?>>Kotak</option>
                        </select>
                        <?php if (isset($errors['keterangan'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['keterangan']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700 mb-1">PIN <span class="text-red-600">*</span></label>
                        <input type="text" id="pin" name="pin" value="<?php echo htmlspecialchars($formData['pin'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['pin'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['pin']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="qty_pin" class="block text-sm font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                        <input type="number" id="qty_pin" name="qty" value="<?php echo htmlspecialchars($formData['qty'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['qty'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['qty']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ID Kaki Fields -->
                <div x-show="selectedType === 'id_kaki'" class="space-y-4">
                    <div>
                        <label for="job_kaki" class="block text-sm font-medium text-gray-700 mb-1">Job <span class="text-red-600">*</span></label>
                        <input type="text" id="job_kaki" name="job" value="<?php echo htmlspecialchars($formData['job'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['job'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['job']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="keterangan_kaki" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                        <select id="keterangan_kaki" name="keterangan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select...</option>
                            <option value="Bulat" <?php echo ($formData['keterangan'] ?? '') === 'Bulat' ? 'selected' : ''; ?>>Bulat</option>
                            <option value="Kotak" <?php echo ($formData['keterangan'] ?? '') === 'Kotak' ? 'selected' : ''; ?>>Kotak</option>
                        </select>
                        <?php if (isset($errors['keterangan'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['keterangan']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="qty_kaki" class="block text-sm font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                        <input type="number" id="qty_kaki" name="qty" value="<?php echo htmlspecialchars($formData['qty'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['qty'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['qty']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Job PSD Fields -->
                <div x-show="selectedType === 'job_psd'" class="space-y-4">
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks <span class="text-red-600">*</span></label>
                        <textarea id="remarks" name="remarks" rows="5" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($formData['remarks'] ?? ''); ?></textarea>
                        <?php if (isset($errors['remarks'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['remarks']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ID Other Fields -->
                <div x-show="selectedType === 'id_other'" class="space-y-4">
                    <div>
                        <label for="nama_id" class="block text-sm font-medium text-gray-700 mb-1">Nama ID <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_id" name="nama_id" value="<?php echo htmlspecialchars($formData['nama_id'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['nama_id'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['nama_id']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="panjang" class="block text-sm font-medium text-gray-700 mb-1">Panjang (cm) <span class="text-red-600">*</span></label>
                        <input type="number" id="panjang" name="panjang" value="<?php echo htmlspecialchars($formData['panjang'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['panjang'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['panjang']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="lebar" class="block text-sm font-medium text-gray-700 mb-1">Lebar (cm) <span class="text-red-600">*</span></label>
                        <input type="number" id="lebar" name="lebar" value="<?php echo htmlspecialchars($formData['lebar'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if (isset($errors['lebar'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['lebar']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notes (Optional) -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Add any additional information..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Conveyor Selection -->
            <div class="mb-6">
                <label for="conveyor_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Conveyor <span class="text-gray-500">(optional)</span>
                </label>
                <?php if (isset($errors['conveyor_id'])): ?>
                    <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        <?php echo $errors['conveyor_id']; ?>
                    </div>
                <?php endif; ?>
                <select 
                    id="conveyor_id"
                    name="conveyor_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">-- Select Conveyor --</option>
                    <?php if (isset($conveyors)): ?>
                        <?php foreach ($conveyors as $conveyor): ?>
                            <option value="<?php echo $conveyor->id; ?>" <?php echo (isset($formData['conveyor_id']) && $formData['conveyor_id'] == $conveyor->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($conveyor->conveyor_name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Shift Selection -->
            <div class="mb-6">
                <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">
                    Shift <span class="text-gray-500">(optional)</span>
                </label>
                <?php if (isset($errors['shift'])): ?>
                    <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        <?php echo $errors['shift']; ?>
                    </div>
                <?php endif; ?>
                <select 
                    id="shift"
                    name="shift" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">-- Select Shift --</option>
                    <?php if (isset($shifts)): ?>
                        <?php foreach ($shifts as $shiftOption): ?>
                            <option value="<?php echo $shiftOption; ?>" <?php echo (isset($formData['shift']) && $formData['shift'] === $shiftOption) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($shiftOption); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Submit Request
                </button>
                <a href="<?php echo url('/request-id'); ?>" class="flex-1 bg-gray-300 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-400 font-medium text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js is loaded from main layout -->
<script>
function requestForm() {
    return {
        selectedType: '<?php echo $formData['id_type'] ?? ''; ?>',
        initForm() {
            // Initialize form state
        }
    };
}
</script>
