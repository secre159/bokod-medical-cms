/**
 * Universal Numeric Input Validation
 * Ensures all number inputs only accept valid numeric values
 * Author: Bokod CMS System
 */

(function() {
    'use strict';
    
    /**
     * Apply numeric validation to all relevant input fields
     */
    function applyNumericValidation() {
        // Define selectors for numeric inputs
        const numericSelectors = [
            'input[type="number"]',
            'input[id*="quantity"]',
            'input[id*="stock"]',
            'input[id*="amount"]',
            'input[id*="price"]',
            'input[id*="minimum"]',
            'input[id*="balance"]',
            'input[id*="count"]',
            'input[id*="shortage"]',
            'input[id*="overage"]',
            'input[name*="quantity"]',
            'input[name*="stock"]',
            'input[name*="amount"]',
            'input[name*="price"]',
            'input[name*="minimum"]',
            'input[name*="balance"]',
            'input[name*="count"]',
            'input[name*="shortage"]',
            'input[name*="overage"]',
            // Specific field IDs
            '#quickQuantity',
            '#bulkQuantity',
            '#bulkQuantityInput',
            '#stockInputQuantity',
            '#dispenseAmount',
            '#minimum_stock',
            '#stock_quantity',
            '#balance_per_card',
            '#on_hand_per_count',
            '#shortage_overage'
        ];
        
        // Get all matching elements
        const numericInputs = document.querySelectorAll(numericSelectors.join(', '));
        
        numericInputs.forEach(input => {
            // Skip if already processed
            if (input.hasAttribute('data-numeric-validated')) {
                return;
            }
            
            // Mark as processed
            input.setAttribute('data-numeric-validated', 'true');
            
            // Set input type to number if not already
            if (input.type !== 'number') {
                input.type = 'number';
            }
            
            // Set minimum value if not set
            if (!input.hasAttribute('min')) {
                input.setAttribute('min', '0');
            }
            
            // Set step attribute for decimal inputs if not set
            if (!input.hasAttribute('step')) {
                input.setAttribute('step', 'any');
            }
            
            // Add input event listener for real-time validation
            input.addEventListener('input', function(e) {
                validateNumericInput(e.target);
            });
            
            // Add keypress event to prevent non-numeric characters
            input.addEventListener('keypress', function(e) {
                preventNonNumericKeypress(e);
            });
            
            // Add paste event listener
            input.addEventListener('paste', function(e) {
                setTimeout(() => {
                    validateNumericInput(e.target);
                }, 1);
            });
            
            // Add blur event for final validation
            input.addEventListener('blur', function(e) {
                finalizeNumericInput(e.target);
            });
            
            console.log('Numeric validation applied to:', input.id || input.name || input.className);
        });
        
        console.log(`Applied numeric validation to ${numericInputs.length} input fields`);
    }
    
    /**
     * Validate numeric input in real-time
     */
    function validateNumericInput(input) {
        const value = input.value;
        
        // Remove any non-numeric characters except decimal point and minus
        const cleanValue = value.replace(/[^0-9.-]/g, '');
        
        // Handle multiple decimal points - keep only the first one
        const parts = cleanValue.split('.');
        let finalValue = parts[0];
        if (parts.length > 1) {
            finalValue += '.' + parts.slice(1).join('');
        }
        
        // Handle multiple minus signs - keep only if at beginning
        if (finalValue.includes('-')) {
            const minusCount = (finalValue.match(/-/g) || []).length;
            if (minusCount > 1 || finalValue.indexOf('-') > 0) {
                finalValue = finalValue.replace(/-/g, '');
                if (value.startsWith('-')) {
                    finalValue = '-' + finalValue;
                }
            }
        }
        
        // Update input if value changed
        if (input.value !== finalValue) {
            const cursorPos = input.selectionStart;
            input.value = finalValue;
            
            // Restore cursor position
            const newCursorPos = Math.min(cursorPos, finalValue.length);
            input.setSelectionRange(newCursorPos, newCursorPos);
        }
        
        // Validate against min/max constraints
        validateConstraints(input);
    }
    
    /**
     * Prevent non-numeric key presses
     */
    function preventNonNumericKeypress(e) {
        const char = String.fromCharCode(e.which);
        const value = e.target.value;
        
        // Allow control keys (backspace, delete, tab, etc.)
        if (e.which < 32 || e.ctrlKey || e.altKey) {
            return true;
        }
        
        // Allow numeric characters
        if (/[0-9]/.test(char)) {
            return true;
        }
        
        // Allow decimal point if not already present
        if (char === '.' && value.indexOf('.') === -1) {
            return true;
        }
        
        // Allow minus sign only at the beginning
        if (char === '-' && e.target.selectionStart === 0 && value.indexOf('-') === -1) {
            const min = parseFloat(e.target.getAttribute('min'));
            if (isNaN(min) || min < 0) {
                return true;
            }
        }
        
        // Prevent all other characters
        e.preventDefault();
        return false;
    }
    
    /**
     * Validate input constraints (min, max, step)
     */
    function validateConstraints(input) {
        const value = parseFloat(input.value);
        const min = parseFloat(input.getAttribute('min'));
        const max = parseFloat(input.getAttribute('max'));
        const step = parseFloat(input.getAttribute('step'));
        
        // Remove existing validation classes
        input.classList.remove('is-invalid', 'is-valid', 'numeric-error');
        
        // Remove existing error messages
        const existingError = input.parentElement.querySelector('.numeric-validation-error');
        if (existingError) {
            existingError.remove();
        }
        
        if (isNaN(value)) {
            return; // Empty or invalid input, will be handled by required validation
        }
        
        let errorMessage = '';
        
        // Check minimum value
        if (!isNaN(min) && value < min) {
            errorMessage = `Minimum value is ${min}`;
            input.classList.add('is-invalid', 'numeric-error');
        }
        
        // Check maximum value
        if (!isNaN(max) && value > max) {
            errorMessage = `Maximum value is ${max}`;
            input.classList.add('is-invalid', 'numeric-error');
        }
        
        // Check step constraint
        if (!isNaN(step) && step !== 0) {
            const remainder = (value - (isNaN(min) ? 0 : min)) % step;
            if (Math.abs(remainder) > 0.001) {
                errorMessage = `Value must be in steps of ${step}`;
                input.classList.add('is-invalid', 'numeric-error');
            }
        }
        
        // Show error message if any
        if (errorMessage) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger small mt-1 numeric-validation-error';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage;
            
            const parent = input.parentElement;
            if (parent.classList.contains('input-group')) {
                parent.parentElement.appendChild(errorDiv);
            } else {
                parent.appendChild(errorDiv);
            }
        } else {
            // Valid input
            input.classList.add('is-valid');
        }
    }
    
    /**
     * Finalize numeric input on blur
     */
    function finalizeNumericInput(input) {
        const value = input.value.trim();
        
        if (value === '' || value === '-' || value === '.') {
            input.value = '';
            return;
        }
        
        const numericValue = parseFloat(value);
        if (!isNaN(numericValue)) {
            // Format the number (remove trailing zeros for integers)
            if (numericValue % 1 === 0) {
                input.value = numericValue.toString();
            } else {
                input.value = numericValue.toString();
            }
        }
        
        validateConstraints(input);
    }
    
    /**
     * Add visual feedback styles
     */
    function addValidationStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .numeric-error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            
            .numeric-validation-error {
                animation: fadeIn 0.3s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            /* Prevent spinner arrows on number inputs where not needed */
            .no-spinner::-webkit-outer-spin-button,
            .no-spinner::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            
            .no-spinner[type=number] {
                -moz-appearance: textfield;
            }
        `;
        document.head.appendChild(style);
    }
    
    /**
     * Initialize numeric validation
     */
    function init() {
        console.log('Initializing universal numeric input validation...');
        
        // Add validation styles
        addValidationStyles();
        
        // Apply validation to existing inputs
        applyNumericValidation();
        
        // Watch for dynamically added inputs
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Check if the node itself is a numeric input
                            const selectors = [
                                'input[type="number"]',
                                'input[id*="quantity"]',
                                'input[id*="stock"]',
                                'input[id*="amount"]',
                                'input[name*="quantity"]',
                                'input[name*="stock"]',
                                'input[name*="amount"]'
                            ];
                            
                            if (node.matches && node.matches(selectors.join(', '))) {
                                applyNumericValidation();
                            }
                            
                            // Check for numeric inputs within the added node
                            if (node.querySelectorAll) {
                                const numericInputs = node.querySelectorAll(selectors.join(', '));
                                if (numericInputs.length > 0) {
                                    setTimeout(applyNumericValidation, 100);
                                }
                            }
                        }
                    });
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        console.log('Universal numeric validation initialized successfully');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Make functions available globally if needed
    window.NumericValidation = {
        apply: applyNumericValidation,
        validate: validateNumericInput
    };
    
})();