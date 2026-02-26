<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create ID Request</h1>
            <p class="mt-2 text-gray-600">Submit a new ID request with multiple items</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-8">
            <!-- Active Conveyor & Shift Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Active Setup:</p>
                        <p class="text-sm text-blue-700">
                            Conveyor: <strong><?php echo htmlspecialchars($active_conveyor_name); ?></strong> | 
                            Shift: <strong><?php echo htmlspecialchars($active_shift); ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" id="request-form" action="<?php echo url('request-id/store'); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- Items Container -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">ID Items</h2>
                    
                    <div id="items-container" x-ref="itemsContainer">
                        <!-- Items will be added here -->
                    </div>

                    <!-- Add Item Button -->
                    <button 
                        type="button" 
                        onclick="requestForm().addItem()"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                    >
                        + Add Item
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button 
                        type="button"
                        id="submit-btn"
                        onclick="submitForm(event)"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg"
                    >
                        Submit Request
                    </button>
                    <a 
                        href="<?php echo url('request-id'); ?>"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg text-center"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function requestForm() {
    return {
        init() {
            this.addItem();
        },
        addItem() {
            const itemsContainer = document.getElementById('items-container');
            const index = itemsContainer.children.length;
            
            const itemHtml = `
                <div class="item-row mb-6 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-gray-900">Item <span class="item-number">${index + 1}</span></h3>
                        ${index > 0 ? `<button type="button" onclick="removeItemByElement(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>` : ''}
                    </div>

                    <!-- ID Type Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ID Type <span class="text-red-600">*</span>
                        </label>
                        
                        <select 
                            name="items[${index}][id_type]"
                            onchange="handleTypeChange(this, ${index})"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 mb-4"
                        >
                            <option value="">-- Select Type --</option>
                            <option value="id_punggung">ID Punggung</option>
                            <option value="pin_4m">PIN 4M</option>
                            <option value="id_kaki">ID Kaki</option>
                            <option value="job_psd">Job PSD</option>
                            <option value="id_other">ID Other</option>
                        </select>

                        <!-- ID Punggung Fields -->
                        <div id="fields_${index}_id_punggung" class="type-fields field-section space-y-3 p-3 bg-gray-50 rounded">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Job <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][job]" placeholder="Job" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                                <select name="items[${index}][keterangan]" required class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    <option value="Asli">Asli</option>
                                    <option value="Perbaikan">Perbaikan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                                <input type="number" name="items[${index}][qty]" placeholder="Quantity" min="1" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Warna <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][warna]" placeholder="Warna" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- PIN 4M Fields -->
                        <div id="fields_${index}_pin_4m" class="type-fields field-section hidden space-y-3 p-3 bg-gray-50 rounded">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][nama]" placeholder="Nama" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">NIK <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][nik]" placeholder="NIK" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Matrix Skill <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][matrix_skill]" placeholder="Matrix Skill" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                                <select name="items[${index}][keterangan]" required class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    <option value="Bulat">Bulat</option>
                                    <option value="Kotak">Kotak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">PIN <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][pin]" placeholder="PIN" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                                <input type="number" name="items[${index}][qty]" placeholder="Quantity" min="1" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- ID Kaki Fields -->
                        <div id="fields_${index}_id_kaki" class="type-fields field-section hidden space-y-3 p-3 bg-gray-50 rounded">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Job <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][job]" placeholder="Job" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                                <select name="items[${index}][keterangan]" required class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    <option value="Bulat">Bulat</option>
                                    <option value="Kotak">Kotak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Qty <span class="text-red-600">*</span></label>
                                <input type="number" name="items[${index}][qty]" placeholder="Quantity" min="1" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Job PSD Fields -->
                        <div id="fields_${index}_job_psd" class="type-fields field-section hidden space-y-3 p-3 bg-gray-50 rounded">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Remarks <span class="text-red-600">*</span></label>
                                <textarea name="items[${index}][remarks]" placeholder="Remarks" rows="2" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>

                        <!-- ID Other Fields -->
                        <div id="fields_${index}_id_other" class="type-fields field-section hidden space-y-3 p-3 bg-gray-50 rounded">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama ID <span class="text-red-600">*</span></label>
                                <input type="text" name="items[${index}][nama_id]" placeholder="Nama ID" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Panjang (cm) <span class="text-red-600">*</span></label>
                                <input type="number" name="items[${index}][panjang]" placeholder="Panjang" min="0" step="0.1" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Lebar (cm) <span class="text-red-600">*</span></label>
                                <input type="number" name="items[${index}][lebar]" placeholder="Lebar" min="0" step="0.1" required
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea 
                            name="items[${index}][notes]"
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Optional notes for this item"
                        ></textarea>
                    </div>
                </div>
            `;
            
            itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            
            // Set initial display state for field sections
            const newItem = itemsContainer.lastElementChild;
            const fieldSections = newItem.querySelectorAll('.field-section');
            fieldSections.forEach(section => {
                // Extract type from section id: "fields_0_id_punggung" -> "id_punggung"
                const sectionId = section.id;
                const parts = sectionId.split('_');
                const sectionType = parts.slice(2).join('_'); // Everything after "fields_0"
                
                // Show only id_punggung initially, hide others
                if (sectionType === 'id_punggung') {
                    section.style.display = 'block';
                    section.classList.remove('hidden');
                } else {
                    section.style.display = 'none';
                    section.classList.add('hidden');
                    // Disable inputs in hidden sections so FormData doesn't collect them
                    section.querySelectorAll('input, select, textarea').forEach(el => {
                        el.disabled = true;
                    });
                }
            });
            
            updateItemNumbers();
        }
    }
}

function removeItemByElement(btn) {
    btn.closest('.item-row').remove();
    updateItemNumbers();
}

function handleTypeChange(selectElement, itemIndex) {
    const selectedType = selectElement.value;
    const itemRow = selectElement.closest('.item-row');
    
    console.log(`handleTypeChange called: itemIndex=${itemIndex}, selectedType="${selectedType}"`);
    
    // Find all field sections in this item row
    const fieldSections = itemRow.querySelectorAll('.field-section');
    console.log(`Found ${fieldSections.length} field sections`);
    
    fieldSections.forEach(section => {
        // Extract type from section id:  "fields_0_id_punggung" -> "id_punggung"
        const sectionId = section.id;
        const parts = sectionId.split('_');
        const sectionType = parts.slice(2).join('_'); // Everything after "fields_0"
        
        console.log(`Section: ${sectionId} -> extracted type: "${sectionType}"`);
        
        if (selectedType && sectionType === selectedType) {
            // SHOW this section
            console.log(`  -> SHOWING ${sectionType}`);
            section.style.display = 'block';
            section.classList.remove('hidden');
            // ENABLE all inputs in this section
            section.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });
        } else {
            // HIDE this section
            console.log(`  -> HIDING ${sectionType}`);
            section.style.display = 'none';
            section.classList.add('hidden');
            // DISABLE all inputs in this section so FormData doesn't collect them
            section.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = true;
            });
        }
    });
}

function updateItemNumbers() {
    const itemRows = document.querySelectorAll('.item-row');
    itemRows.forEach((row, index) => {
        row.querySelector('.item-number').textContent = index + 1;
        // Update input names
        row.querySelectorAll('[name]').forEach(input => {
            const oldName = input.name;
            input.name = oldName.replace(/items\[\d+\]/, `items[${index}]`);
        });
        
        // Remove delete button from first item
        const deleteBtn = row.querySelector('button[onclick*="removeItemByElement"]');
        if (index === 0 && deleteBtn) {
            deleteBtn.remove();
        } else if (index > 0 && !deleteBtn) {
            const header = row.querySelector('.flex');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = function() { removeItemByElement(this); };
            btn.className = 'text-red-600 hover:text-red-800 text-sm font-medium';
            btn.textContent = 'Delete';
            header.appendChild(btn);
        }
    });
}

function submitForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('request-form');
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    
    // Collect form data
    const formData = new FormData(form);
    
    // Convert FormData to JSON-compatible object
    const data = {};
    const items = [];
    
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('items[')) {
            // Parse items[0][id_type] format
            const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = parseInt(match[1]);
                const field = match[2];
                if (!items[index]) items[index] = {};
                items[index][field] = value;
            }
        } else {
            data[key] = value;
        }
    }
    
    // Filter out empty items
    data.items = items.filter(item => item !== undefined);
    
    // Validate
    if (!data.items || data.items.length === 0) {
        showToast('Minimal harus ada 1 item', 'error');
        return;
    }
    
    // Check all items have id_type
    for (let i = 0; i < data.items.length; i++) {
        if (!data.items[i].id_type) {
            showToast('Item ' + (i + 1) + ': Pilih ID Type', 'error');
            return;
        }
    }
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    // Submit via AJAX
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        return r.json().then(resp => {
            if (resp.success) {
                showToast('Request berhasil dibuat!', 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo url('request-id'); ?>';
                }, 1500);
            } else {
                // Show error with details if available
                let errorMsg = resp.error || 'Gagal membuat request';
                if (resp.details && Array.isArray(resp.details) && resp.details.length > 0) {
                    errorMsg = resp.details.join('\n');
                }
                showToast(errorMsg, 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    })
    .catch(e => {
        showToast('Gagal membuat request: ' + e.message, 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white font-medium shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', () => {
    const form = requestForm();
    form.init();
    
    // Log for debugging
    console.log('Form initialized');
});
</script>
