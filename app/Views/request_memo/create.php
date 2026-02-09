<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Internal Memo Request</h1>
            <p class="mt-2 text-gray-600">Submit internal memos with multiple items</p>
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

            <form method="POST" id="request-form" action="<?php echo url('requests/memo/store'); ?>">
                <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?? ''; ?>">

                <!-- Items Container -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Memo Items</h2>
                    
                    <div id="items-container" x-ref="itemsContainer">
                        <!-- Items will be added here -->
                    </div>

                    <!-- Add Item Button -->
                    <button 
                        type="button" 
                        @click="addItem()"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                    >
                        + Add Memo
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
                        href="<?php echo url('requests/memo'); ?>"
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
                <div class="item-row mb-6 p-4 border border-gray-200 rounded-lg" x-ref="itemRow_${index}">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-gray-900">Memo <span class="item-number">${index + 1}</span></h3>
                        ${index > 0 ? `<button type="button" onclick="removeItemByElement(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>` : ''}
                    </div>

                    <!-- Memo Content -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Memo Content <span class="text-red-600">*</span>
                        </label>
                        <textarea 
                            name="items[${index}][memo_content]"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your internal memo content here... (minimum 10 characters)"
                        ></textarea>
                        <div class="mt-1 text-xs text-gray-500">
                            <span class="char-count">0</span> / 5000 characters
                        </div>
                    </div>
                </div>
            `;
            
            itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            
            // Add character counter listener
            const textarea = itemsContainer.querySelector(`.item-row:last-child textarea`);
            if (textarea) {
                const charSpan = itemsContainer.querySelector(`.item-row:last-child .char-count`);
                textarea.addEventListener('input', function() {
                    charSpan.textContent = this.value.length;
                });
            }
            
            updateItemNumbers();
        }
    }
}

function removeItemByElement(btn) {
    btn.closest('.item-row').remove();
    updateItemNumbers();
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
            // Parse items[0][memo_content] format
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
        showToast('Minimal harus ada 1 memo', 'error');
        return;
    }
    
    // Check all items have memo_content
    for (let i = 0; i < data.items.length; i++) {
        if (!data.items[i].memo_content || data.items[i].memo_content.length < 10) {
            showToast('Memo ' + (i + 1) + ': Isi memo minimal 10 karakter', 'error');
            return;
        }
        if (data.items[i].memo_content.length > 5000) {
            showToast('Memo ' + (i + 1) + ': Memo maksimal 5000 karakter', 'error');
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
                    window.location.href = '<?php echo url('requests/memo'); ?>';
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    const form = requestForm();
    form.init();
});
</script>
