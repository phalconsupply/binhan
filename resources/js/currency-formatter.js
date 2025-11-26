/**
 * Currency Formatter
 * Auto format currency input fields with thousand separators
 */

// Format number with thousand separator
function formatCurrency(value) {
    // Remove all non-digit characters
    let num = value.toString().replace(/\D/g, '');
    
    // Add thousand separator
    return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Remove formatting to get raw number
function unformatCurrency(value) {
    return value.toString().replace(/,/g, '');
}

// Initialize currency inputs
function initCurrencyInputs() {
    // Find all currency input fields
    const currencyInputs = document.querySelectorAll('input[data-currency]');
    
    console.log('Found currency inputs:', currencyInputs.length);
    
    currencyInputs.forEach(input => {
        // Change type to text for better control
        if (input.type === 'number') {
            input.type = 'text';
        }
        input.inputMode = 'numeric';
        
        // Format on load if has value
        if (input.value) {
            input.value = formatCurrency(input.value);
        }
        
        // Format on input
        input.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            const oldLength = oldValue.length;
            
            // Format the value
            const newValue = formatCurrency(oldValue);
            this.value = newValue;
            
            // Restore cursor position
            const newLength = newValue.length;
            const diff = newLength - oldLength;
            this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });
        
        // On blur, ensure formatting
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = formatCurrency(this.value);
            }
        });
    });
    
    // Handle form submission
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const currencyInputs = form.querySelectorAll('input[data-currency]');
        
        // Store original values to restore if needed
        const originalValues = new Map();
        
        currencyInputs.forEach(input => {
            if (input.value && input.name) {
                // Store original formatted value
                originalValues.set(input, input.value);
                
                // Replace formatted value with raw number
                input.value = unformatCurrency(input.value);
            }
        });
        
        // If form submission fails (validation error), restore formatted values
        // This will happen if the page reloads and shows errors
        setTimeout(() => {
            originalValues.forEach((value, input) => {
                if (document.contains(input)) {
                    input.value = value;
                }
            });
        }, 100);
    }, true);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCurrencyInputs);
} else {
    initCurrencyInputs();
}

// Re-initialize for dynamic content
document.addEventListener('DOMContentLoaded', function() {
    // Watch for dynamic content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    const newInputs = node.querySelectorAll ? node.querySelectorAll('input[data-currency]') : [];
                    if (newInputs.length > 0) {
                        initCurrencyInputs();
                    }
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Export for manual use
window.initCurrencyInputs = initCurrencyInputs;
window.formatCurrency = formatCurrency;
window.unformatCurrency = unformatCurrency;
