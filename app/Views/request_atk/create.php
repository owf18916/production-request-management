<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Request ATK</h1>
            <p class="mt-2 text-gray-600">Submit a new ATK request</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-8">
            <form method="POST" action="<?php echo url('requests/atk/store'); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- ATK Selection -->
                <div class="mb-6" x-data="{ open: false, search: '', selected: null, results: [] }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ATK Item <span class="text-red-600">*</span>
                    </label>
                    <?php if (isset($errors['atk_id'])): ?>
                        <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                            <?php echo $errors['atk_id']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="search"
                            @input="
                                if (search.length >= 1) {
                                    fetch('<?php echo url('api/atk/search'); ?>?q=' + search)
                                        .then(r => r.json())
                                        .then(data => { results = data.results; open = true; })
                                } else {
                                    results = [];
                                    open = false;
                                }
                            "
                            @focus="open = true"
                            @keydown.escape="open = false"
                            placeholder="Search ATK items..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            autocomplete="off"
                        >
                        <input type="hidden" name="atk_id" x-model="selected" value="<?php echo $atk_id ?? ''; ?>">

                        <div 
                            x-show="open && results.length > 0"
                            class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-60 overflow-y-auto"
                        >
                            <template x-for="item in results" :key="item.id">
                                <button
                                    type="button"
                                    @click="selected = item.id; search = item.nama_barang; open = false;"
                                    class="w-full text-left px-4 py-2 hover:bg-blue-50 border-b border-gray-200 last:border-b-0"
                                >
                                    <div class="font-medium text-gray-900" x-text="item.nama_barang"></div>
                                    <div class="text-sm text-gray-500" x-text="'Kode: ' + item.kode_barang"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Conveyor Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Conveyor <span class="text-gray-500">(optional)</span>
                    </label>
                    <?php if (isset($errors['conveyor_id'])): ?>
                        <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                            <?php echo $errors['conveyor_id']; ?>
                        </div>
                    <?php endif; ?>
                    <select 
                        name="conveyor_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">-- Select Conveyor --</option>
                        <?php if (isset($conveyors)): ?>
                            <?php foreach ($conveyors as $conveyor): ?>
                                <option value="<?php echo $conveyor->id; ?>" <?php echo (isset($conveyor_id) && $conveyor_id == $conveyor->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($conveyor->conveyor_name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Shift Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Shift <span class="text-gray-500">(optional)</span>
                    </label>
                    <?php if (isset($errors['shift'])): ?>
                        <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                            <?php echo $errors['shift']; ?>
                        </div>
                    <?php endif; ?>
                    <select 
                        name="shift" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">-- Select Shift --</option>
                        <?php if (isset($shifts)): ?>
                            <?php foreach ($shifts as $shiftOption): ?>
                                <option value="<?php echo $shiftOption; ?>" <?php echo (isset($shift) && $shift === $shiftOption) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($shiftOption); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-600">*</span>
                    </label>
                    <?php if (isset($errors['qty'])): ?>
                        <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                            <?php echo $errors['qty']; ?>
                        </div>
                    <?php endif; ?>
                    <input 
                        type="number" 
                        name="qty" 
                        min="1" 
                        max="9999"
                        value="<?php echo $qty ?? ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes <span class="text-gray-500">(optional)</span>
                    </label>
                    <textarea 
                        name="notes" 
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    ><?php echo $notes ?? ''; ?></textarea>
                </div>

                <!-- Submit -->
                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                    >
                        Submit Request
                    </button>
                    <a 
                        href="<?php echo url('requests/atk'); ?>" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 font-medium text-center"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="font-medium text-blue-900">About ATK Requests</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        ATK (Alat Tulis Kantor) requests are for office stationery supplies. Your request will be reviewed by the administrator. 
                        You can track the status of your requests from the main dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js is loaded from main layout -->
