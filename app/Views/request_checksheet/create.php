<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Request Checksheet</h1>
            <p class="text-gray-600 mt-2">Fill in the form below to create a new request</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($message = session('message')): ?>
            <div class="mb-6 p-4 rounded-lg" style="background-color: <?php echo session('message_type') === 'success' ? '#dcfce7' : '#fee2e2'; ?>; border: 1px solid <?php echo session('message_type') === 'success' ? '#86efac' : '#fca5a5'; ?>;">
                <p style="color: <?php echo session('message_type') === 'success' ? '#166534' : '#991b1b'; ?>"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
            <form method="POST" action="<?php echo url('request_checksheet/store'); ?>" class="space-y-6">
                <input type="hidden" name="_csrf_token" value="<?php echo csrfToken(); ?>">

                <!-- Checksheet Selection -->
                <div x-data="checksheetsearch()" class="space-y-2">
                    <label for="checksheet" class="block text-sm font-medium text-gray-700">
                        Checksheet <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="checksheet" 
                            name="checksheet_search"
                            @input="search()"
                            @focus="showResults = true"
                            @click.outside="showResults = false"
                            placeholder="Search checksheet..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['checksheet_id']) ? 'border-red-500' : ''; ?>"
                            autocomplete="off"
                        >
                        <input type="hidden" id="checksheet_id" name="checksheet_id" :value="selectedChecksheet?.id">
                        
                        <!-- Search Results Dropdown -->
                        <div 
                            x-show="showResults && results.length > 0"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto"
                        >
                            <template x-for="cs in results" :key="cs.id">
                                <button 
                                    type="button"
                                    @click="selectChecksheet(cs)"
                                    class="w-full text-left px-4 py-2 hover:bg-blue-50 border-b last:border-b-0"
                                >
                                    <div class="font-medium text-gray-900" x-text="cs.nama_checksheet"></div>
                                    <div class="text-xs text-gray-500" x-text="cs.kode_checksheet"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                    <?php if (isset($errors['checksheet_id'])): ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['checksheet_id']; ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500 mt-1">Search by checksheet code or title</p>
                </div>

                <!-- Quantity -->
                <div class="space-y-2">
                    <label for="qty" class="block text-sm font-medium text-gray-700">
                        Quantity <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="qty" 
                        name="qty" 
                        value="<?php echo htmlspecialchars($qty ?? ''); ?>"
                        min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['qty']) ? 'border-red-500' : ''; ?>"
                    >
                    <?php if (isset($errors['qty'])): ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['qty']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Conveyor Selection -->
                <div class="space-y-2">
                    <label for="conveyor_id" class="block text-sm font-medium text-gray-700">
                        Conveyor <span class="text-gray-500">(optional)</span>
                    </label>
                    <select 
                        id="conveyor_id"
                        name="conveyor_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['conveyor_id']) ? 'border-red-500' : ''; ?>"
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
                    <?php if (isset($errors['conveyor_id'])): ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['conveyor_id']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Shift Selection -->
                <div class="space-y-2">
                    <label for="shift" class="block text-sm font-medium text-gray-700">
                        Shift <span class="text-gray-500">(optional)</span>
                    </label>
                    <select 
                        id="shift"
                        name="shift" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo isset($errors['shift']) ? 'border-red-500' : ''; ?>"
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
                    <?php if (isset($errors['shift'])): ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['shift']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notes <span class="text-gray-500">(optional)</span>
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes"
                        rows="4"
                        placeholder="Add any notes or special instructions..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    ><?php echo htmlspecialchars($notes ?? ''); ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
                    >
                        Create Request
                    </button>
                    <a 
                        href="<?php echo url('request_checksheet'); ?>" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg text-center transition"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-semibold text-blue-900 mb-2">Request Information</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Your request will be reviewed by the admin team</li>
                <li>• A unique request number will be generated automatically</li>
                <li>• You can track the status of your requests in the main list</li>
            </ul>
        </div>
    </div>
</div>

<script>
    function checksheetsearch() {
        return {
            results: [],
            selectedChecksheet: null,
            showResults: false,
            query: '',
            allChecksheets: <?php echo json_encode($checksheets); ?>,
            search() {
                const input = document.getElementById('checksheet').value;
                this.query = input.toLowerCase();
                
                if (this.query.length < 1) {
                    this.results = [];
                    return;
                }
                
                this.results = this.allChecksheets.filter(cs => 
                    (cs.nama_checksheet && cs.nama_checksheet.toLowerCase().includes(this.query)) ||
                    (cs.kode_checksheet && cs.kode_checksheet.toLowerCase().includes(this.query))
                );
            },
            selectChecksheet(cs) {
                this.selectedChecksheet = cs;
                document.getElementById('checksheet').value = cs.nama_checksheet;
                document.getElementById('checksheet_id').value = cs.id;
                this.showResults = false;
            }
        }
    }
</script>
