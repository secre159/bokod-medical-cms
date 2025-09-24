/**
 * Phone Number Validation and Formatting Utility
 * Handles Philippine phone number formats and validation
 */

class PhoneNumberValidator {
    constructor() {
        this.philippinesMobilePattern = /^(09|\+639)[0-9]{9}$/;
        this.generalPhonePattern = /^[0-9+\s()-]{11,}$/;
    }

    /**
     * Clean phone number by removing all non-numeric characters except +
     */
    cleanPhoneNumber(phone) {
        if (!phone) return '';
        return phone.replace(/[^0-9+]/g, '');
    }

    /**
     * Format phone number for display (adds spaces and formatting)
     */
    formatPhoneNumber(phone) {
        if (!phone) return '';
        
        const cleaned = this.cleanPhoneNumber(phone);
        
        // Philippine format: +63 9XX XXX XXXX
        if (cleaned.match(/^\+639[0-9]{9}$/)) {
            return cleaned.replace(/^\+63(9[0-9]{2})([0-9]{3})([0-9]{4})$/, '+63 $1 $2 $3');
        }
        
        // Local Philippine format: 09XX XXX XXXX
        if (cleaned.match(/^09[0-9]{9}$/)) {
            return cleaned.replace(/^(09[0-9]{2})([0-9]{3})([0-9]{4})$/, '$1 $2 $3');
        }
        
        // General format for other numbers
        if (cleaned.length >= 11) {
            // Try to format as groups of 3-4 digits
            const groups = cleaned.match(/(\+?[0-9]{1,4})([0-9]{3,4})?([0-9]{3,4})?([0-9]{0,4})?/);
            if (groups) {
                return groups.filter(g => g && g.length > 0).join(' ').trim();
            }
        }
        
        return phone; // Return original if no formatting applied
    }

    /**
     * Validate Philippine phone number
     */
    validatePhilippineNumber(phone) {
        if (!phone) return true; // Allow empty values (handle nullable separately)
        
        const cleaned = this.cleanPhoneNumber(phone);
        
        // Check for valid characters
        if (!/^[0-9+\s()-]+$/.test(phone)) {
            return {
                valid: false,
                message: 'Phone number must contain only numbers and valid characters (+, -, (), spaces).'
            };
        }
        
        // Check Philippines formats
        if (cleaned.match(/^\+639[0-9]{9}$/)) {
            return { valid: true, message: 'Valid Philippines international format.' };
        }
        
        if (cleaned.match(/^09[0-9]{9}$/)) {
            return { valid: true, message: 'Valid Philippines local format.' };
        }
        
        // Check minimum length for other formats
        if (cleaned.length >= 11) {
            return { valid: true, message: 'Valid phone number format.' };
        }
        
        return {
            valid: false,
            message: 'Phone number must have at least 11 digits or start with +639.'
        };
    }

    /**
     * Auto-format phone number input as user types
     */
    autoFormat(input) {
        const cursorPosition = input.selectionStart;
        const oldLength = input.value.length;
        const formatted = this.formatPhoneNumber(input.value);
        
        input.value = formatted;
        
        // Adjust cursor position after formatting
        const newLength = formatted.length;
        const newCursorPosition = cursorPosition + (newLength - oldLength);
        
        // Set cursor position after a short delay to ensure formatting is applied
        setTimeout(() => {
            input.setSelectionRange(newCursorPosition, newCursorPosition);
        }, 0);
    }

    /**
     * Add real-time validation to phone number inputs
     */
    addValidationToInput(inputElement, feedbackElement = null) {
        const validator = this;
        
        // Auto-format as user types
        inputElement.addEventListener('input', function(e) {
            validator.autoFormat(this);
            validator.validateInput(this, feedbackElement);
        });

        // Validate on blur
        inputElement.addEventListener('blur', function(e) {
            validator.validateInput(this, feedbackElement);
        });

        // Format on paste
        inputElement.addEventListener('paste', function(e) {
            setTimeout(() => {
                validator.autoFormat(this);
                validator.validateInput(this, feedbackElement);
            }, 10);
        });
    }

    /**
     * Validate input and show feedback
     */
    validateInput(inputElement, feedbackElement = null) {
        const validation = this.validatePhilippineNumber(inputElement.value);
        
        // Remove previous validation classes
        inputElement.classList.remove('is-valid', 'is-invalid');
        
        if (inputElement.value.trim() === '') {
            // Empty value - remove validation styling
            if (feedbackElement) {
                feedbackElement.textContent = '';
                feedbackElement.className = 'form-text text-muted';
            }
            return;
        }
        
        if (validation.valid) {
            inputElement.classList.add('is-valid');
            if (feedbackElement) {
                feedbackElement.textContent = validation.message;
                feedbackElement.className = 'form-text text-success';
            }
        } else {
            inputElement.classList.add('is-invalid');
            if (feedbackElement) {
                feedbackElement.textContent = validation.message;
                feedbackElement.className = 'form-text text-danger';
            }
        }
    }
}

// Initialize phone number validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const phoneValidator = new PhoneNumberValidator();
    
    // Find all phone number inputs and add validation
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name*="phone"]');
    
    phoneInputs.forEach(function(input) {
        // Create or find feedback element
        let feedbackElement = input.parentNode.querySelector('.phone-feedback');
        
        if (!feedbackElement) {
            feedbackElement = document.createElement('small');
            feedbackElement.className = 'form-text text-muted phone-feedback';
            feedbackElement.textContent = 'Enter phone number (e.g., +639XX XXX XXXX or 09XX XXX XXXX)';
            input.parentNode.appendChild(feedbackElement);
        }
        
        // Add validation to the input
        phoneValidator.addValidationToInput(input, feedbackElement);
    });
    
    // Make validator globally available
    window.PhoneValidator = phoneValidator;
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PhoneNumberValidator;
}