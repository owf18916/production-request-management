<!-- Reusable Confirmation Modal -->
<div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-sm mx-4 animate-modal">
        <!-- Modal Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 id="confirmationTitle" class="text-lg font-semibold text-gray-900">
                Confirm Action
            </h3>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4">
            <p id="confirmationMessage" class="text-gray-700 text-sm">
                Are you sure?
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4 flex gap-3">
            <button 
                id="confirmationCancel"
                type="button"
                onclick="closeConfirmationModal()"
                class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition duration-200"
            >
                Cancel
            </button>
            <button 
                id="confirmationConfirm"
                type="button"
                onclick="executeConfirmation()"
                class="flex-1 px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition duration-200"
            >
                Confirm
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-modal {
        animation: slideUp 0.3s ease-out;
    }
</style>

<script>
let confirmationCallback = null;
let modalInitialized = false;

/**
 * Open confirmation modal with custom title and message
 * @param {string} title - Modal title
 * @param {string} message - Confirmation message
 * @param {Function} callback - Callback function to execute on confirmation
 * @param {string} confirmButtonText - Custom confirm button text (optional)
 * @param {string} confirmButtonColor - Custom confirm button color (optional)
 */
function openConfirmationModal(title, message, callback, confirmButtonText = 'Confirm', confirmButtonColor = 'red') {
    // Validate callback is a function
    if (typeof callback !== 'function') {
        console.error('Callback must be a function');
        return;
    }
    
    confirmationCallback = callback;
    
    const titleEl = document.getElementById('confirmationTitle');
    const messageEl = document.getElementById('confirmationMessage');
    const confirmBtn = document.getElementById('confirmationConfirm');
    
    if (!titleEl || !messageEl || !confirmBtn) {
        console.error('Modal elements not found');
        return;
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    confirmBtn.textContent = confirmButtonText;
    
    // Update button color using data attribute instead of dynamic classes
    confirmBtn.setAttribute('data-color', confirmButtonColor);
    
    // Set appropriate classes based on color
    const colorClasses = {
        'red': 'bg-red-600 hover:bg-red-700',
        'blue': 'bg-blue-600 hover:bg-blue-700',
        'green': 'bg-green-600 hover:bg-green-700',
        'yellow': 'bg-yellow-600 hover:bg-yellow-700',
        'purple': 'bg-purple-600 hover:bg-purple-700'
    };
    
    const baseClasses = 'flex-1 px-4 py-2 text-white rounded-lg font-medium transition duration-200';
    const colorClass = colorClasses[confirmButtonColor] || colorClasses['red'];
    confirmBtn.className = baseClasses + ' ' + colorClass;
    
    // Show modal with animation
    const modal = document.getElementById('confirmationModal');
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.add('animate-modal');
    }, 10);
}

/**
 * Close confirmation modal
 */
function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    if (!modal) return;
    
    modal.classList.remove('animate-modal');
    modal.style.display = 'none';
    confirmationCallback = null;
}

/**
 * Execute the confirmation callback
 */
function executeConfirmation() {
    if (typeof confirmationCallback === 'function') {
        confirmationCallback();
        closeConfirmationModal();
    } else {
        console.error('Callback is not a function or is null');
        closeConfirmationModal();
    }
}

/**
 * Initialize modal event listeners (only once)
 */
function initializeModal() {
    if (modalInitialized) return;
    
    const modal = document.getElementById('confirmationModal');
    if (!modal) return;
    
    // Close modal when clicking outside (on backdrop)
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeConfirmationModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('confirmationModal');
            if (modal && modal.style.display !== 'none') {
                closeConfirmationModal();
            }
        }
    });
    
    modalInitialized = true;
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeModal);
} else {
    // DOM already loaded
    initializeModal();
}
</script>
