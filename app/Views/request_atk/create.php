<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Request ATK</h1>
            <p class="mt-2 text-gray-600">Submit a new ATK request with multiple items</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-8" x-data="requestForm()">
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

            <form method="POST" id="request-form" action="<?php echo url('requests/atk/store'); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- Items Container -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Items</h2>
                    
                    <div id="items-container" x-ref="itemsContainer">
                        <!-- Items will be added here -->
                        <?php if (isset($items) && !empty($items)): ?>
                            <?php foreach ($items as $index => $item): ?>
                                <div class="item-row mb-6 p-4 border border-gray-200 rounded-lg" x-ref="itemRow_<?php echo $index; ?>">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="font-medium text-gray-900">Item <span class="item-number"><?php echo ($index + 1); ?></span></h3>
                                        <button type="button" @click="removeItem(<?php echo $index; ?>)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </div>

                                    <!-- ATK Selection -->
                                    <div class="mb-4" x-data="{ open: false, search: '', selected: '<?php echo $item['atk_id'] ?? ''; ?>', results: [] }">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            ATK Item <span class="text-red-600">*</span>
                                        </label>
                                        <?php if (isset($errors["items.{$index}.atk_id"])): ?>
                                            <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                                <?php echo $errors["items.{$index}.atk_id"]; ?>
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
                                            <input type="hidden" name="items[<?php echo $index; ?>][atk_id]" x-model="selected">

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

                                    <!-- Quantity -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Quantity <span class="text-red-600">*</span>
                                        </label>
                                        <?php if (isset($errors["items.{$index}.qty"])): ?>
                                            <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                                <?php echo $errors["items.{$index}.qty"]; ?>
                                            </div>
                                        <?php endif; ?>
                                        <input 
                                            type="number" 
                                            name="items[<?php echo $index; ?>][qty]" 
                                            min="1" 
                                            max="9999"
                                            value="<?php echo $item['qty'] ?? ''; ?>"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        >
                                    </div>

                                    <!-- Notes per Item -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Notes <span class="text-gray-500">(optional)</span>
                                        </label>
                                        <textarea 
                                            name="items[<?php echo $index; ?>][notes]" 
                                            rows="2"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        ><?php echo $item['notes'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Add Item Button -->
                    <button 
                        type="button" 
                        @click="addItem()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Tambah Item
                    </button>
                </div>

                <!-- Submit -->
                <div class="flex gap-4">
                    <button 
                        type="button" 
                        id="submit-btn"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                        onclick="submitForm(event)"
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
                        ATK (Alat Tulis Kantor) requests are for office stationery supplies. Anda dapat menambahkan beberapa item dalam 1 request.
                        Conveyor dan Shift yang digunakan sudah di-setup di Dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Script for Dynamic Items -->
<script>
function requestForm() {
    return {
        itemCount: <?php echo isset($items) && !empty($items) ? count($items) : 0; ?>,
        
        init() {
            // Initialize first item if empty
            const container = document.getElementById('items-container');
            if (this.itemCount === 0 && container.children.length === 0) {
                this.addItem();
            }
        },

        addItem() {
            const index = this.itemCount;
            const itemHtml = `
                <div class="item-row mb-6 p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-gray-900">Item <span class="item-number">${index + 1}</span></h3>
                        <button type="button" onclick="removeItemByElement(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Delete
                        </button>
                    </div>

                    <!-- ATK Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ATK Item <span class="text-red-600">*</span>
                        </label>

                        <div class="relative atk-search" data-index="${index}">
                            <input 
                                type="text" 
                                class="atk-search-input w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Search ATK items..."
                                autocomplete="off"
                            >
                            <input type="hidden" name="items[${index}][atk_id]" class="atk-id-input">

                            <div class="atk-results absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-10 max-h-60 overflow-y-auto hidden">
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="items[${index}][qty]" 
                            min="1" 
                            max="9999"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <!-- Notes per Item -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes <span class="text-gray-500">(optional)</span>
                        </label>
                        <textarea 
                            name="items[${index}][notes]" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>
                </div>
            `;
            
            const container = document.getElementById('items-container');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = itemHtml;
            const newItem = tempDiv.firstElementChild;
            container.appendChild(newItem);
            
            // Setup search for this item
            setupATKSearch(newItem, index);
            
            this.itemCount++;
        }
    };
}

function setupATKSearch(itemElement, index) {
    const searchInput = itemElement.querySelector('.atk-search-input');
    const idInput = itemElement.querySelector('.atk-id-input');
    const resultsDiv = itemElement.querySelector('.atk-results');
    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value;

        if (query.length < 1) {
            resultsDiv.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch('<?php echo url('api/atk/search'); ?>?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(item => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'w-full text-left px-4 py-2 hover:bg-blue-50 border-b border-gray-200 last:border-b-0';
                            btn.innerHTML = `
                                <div class="font-medium text-gray-900">${item.nama_barang}</div>
                                <div class="text-sm text-gray-500">Kode: ${item.kode_barang}</div>
                            `;
                            btn.onclick = (e) => {
                                e.preventDefault();
                                idInput.value = item.id;
                                searchInput.value = item.nama_barang;
                                resultsDiv.classList.add('hidden');
                            };
                            resultsDiv.appendChild(btn);
                        });
                        resultsDiv.classList.remove('hidden');
                    } else {
                        resultsDiv.classList.add('hidden');
                    }
                })
                .catch(e => console.error('Error searching ATK:', e));
        }, 300);
    });

    searchInput.addEventListener('focus', () => {
        if (idInput.value) {
            // Show selected item
        } else if (searchInput.value.length > 0) {
            resultsDiv.classList.remove('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!itemElement.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
}

function removeItemByElement(element) {
    const itemRow = element.closest('.item-row');
    if (itemRow) {
        itemRow.remove();
        updateItemNumbers();
    }
}

function updateItemNumbers() {
    const items = document.querySelectorAll('.item-row .item-number');
    items.forEach((item, index) => {
        item.textContent = index + 1;
    });
}

function submitForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('request-form');
    const submitBtn = document.getElementById('submit-btn');
    const originalText = submitBtn.textContent;
    
    console.log('Form submit started');
    
    // Collect form data
    const formData = new FormData(form);
    
    // Convert FormData to JSON-compatible object
    const data = {};
    const items = [];
    
    for (let [key, value] of formData.entries()) {
        console.log('FormData entry:', key, value);
        if (key.startsWith('items[')) {
            // Parse items[0][atk_id] format
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
    
    console.log('Collected data:', data);
    
    // Validate
    if (!data.items || data.items.length === 0) {
        showToast('Minimal harus ada 1 item', 'error');
        return;
    }
    
    // Check all items have atk_id and qty
    for (let i = 0; i < data.items.length; i++) {
        if (!data.items[i].atk_id) {
            showToast('Item ' + (i + 1) + ': Pilih ATK item', 'error');
            return;
        }
        if (!data.items[i].qty) {
            showToast('Item ' + (i + 1) + ': Isi quantity', 'error');
            return;
        }
    }
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    console.log('Sending request to:', form.action);
    
    // Submit via AJAX
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(r => {
        console.log('Response status:', r.status);
        return r.json().then(resp => {
            console.log('Response data:', resp);
            if (resp.success) {
                showToast('Request berhasil dibuat!', 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo url('requests/atk'); ?>';
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
        console.error('Error:', e);
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    const form = requestForm();
    form.init();
});
</script>

