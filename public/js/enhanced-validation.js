/**
 * Enhanced Input Validation with Character Limits and Real-time Format Checking
 * Features:
 * - Phone number character limit enforcement (15 digits max for international)
 * - Real-time email format validation
 * - Visual feedback for valid/invalid inputs
 * - Auto-formatting for phone numbers
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        phone: {
            maxLength: {
                philippinesLocal: 11,      // 09XXXXXXXXX
                philippinesInternational: 13, // +639XXXXXXXXX  
                international: 15          // Max international length
            },
            patterns: {
                philippinesLocal: /^09[0-9]{9}$/,
                philippinesInternational: /^\+639[0-9]{9}$/,
                validChars: /^[0-9+\s()-]*$/
            },
            validNetworkCodes: ['17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '46', '47', '48', '49', '73', '74', '81', '82', '88', '89', '92', '93', '94', '95', '96', '97', '98', '99']
        },
        email: {
            maxLength: 254, // RFC standard
            localPartMaxLength: 64,
            pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
        }
    };
    
    /**
     * Phone Number Validation and Formatting
     */
    class PhoneValidator {
        constructor(input) {
            this.input = input;
            this.feedback = this.getFeedbackElement();
            this.setupEventListeners();
            this.lastValidValue = '';
        }
        
        getFeedbackElement() {
            let feedback = this.input.parentElement.querySelector('.phone-feedback');
            if (!feedback) {
                feedback = document.createElement('small');
                feedback.className = 'form-text phone-feedback';
                this.input.parentElement.appendChild(feedback);
            }
            return feedback;
        }
        
        setupEventListeners() {
            // Prevent invalid characters
            this.input.addEventListener('keypress', (e) => this.handleKeyPress(e));
            
            // Handle input events
            this.input.addEventListener('input', (e) => this.handleInput(e));
            
            // Handle paste events
            this.input.addEventListener('paste', (e) => this.handlePaste(e));
        }
        
        handleKeyPress(e) {
            const char = e.key;
            const currentValue = this.input.value;
            
            // Allow control keys
            if (e.ctrlKey || e.metaKey || ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(char)) {
                return;
            }
            
            // Only allow valid phone characters
            if (!CONFIG.phone.patterns.validChars.test(char)) {
                e.preventDefault();
                this.showError('Only numbers, +, spaces, parentheses, and hyphens are allowed');
                return;
            }
            
            // Check character limits
            if (this.wouldExceedLimit(currentValue + char)) {
                e.preventDefault();
                this.showError('Phone number has reached maximum length');
                return;
            }
        }
        
        handleInput(e) {
            const value = e.target.value;
            
            // Remove invalid characters
            const cleanValue = value.replace(/[^0-9+\s()-]/g, '');
            if (cleanValue !== value) {
                this.input.value = cleanValue;
            }
            
            // Check if exceeds limit
            if (this.exceedsLimit(cleanValue)) {
                this.input.value = this.lastValidValue;
                this.showError('Phone number exceeds maximum length');
                return;
            }
            
            // Validate and format
            this.validateAndFormat(cleanValue);
            this.lastValidValue = cleanValue;
        }
        
        handlePaste(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const cleanPaste = paste.replace(/[^0-9+\s()-]/g, '');
            
            if (this.exceedsLimit(cleanPaste)) {
                this.showError('Pasted content exceeds maximum phone number length');
                return;
            }
            
            this.input.value = cleanPaste;
            this.validateAndFormat(cleanPaste);
        }
        
        wouldExceedLimit(value) {
            const digitsOnly = value.replace(/[^0-9]/g, '');
            
            if (value.startsWith('+63')) {
                return digitsOnly.length > CONFIG.phone.maxLength.philippinesInternational;
            } else if (value.startsWith('09')) {
                return digitsOnly.length > CONFIG.phone.maxLength.philippinesLocal;
            } else {
                return digitsOnly.length > CONFIG.phone.maxLength.international;
            }
        }
        
        exceedsLimit(value) {
            const digitsOnly = value.replace(/[^0-9]/g, '');
            
            if (value.startsWith('+63')) {
                return digitsOnly.length > CONFIG.phone.maxLength.philippinesInternational;
            } else if (value.startsWith('09')) {
                return digitsOnly.length > CONFIG.phone.maxLength.philippinesLocal;
            } else {
                return digitsOnly.length > CONFIG.phone.maxLength.international;
            }
        }
        
        validateAndFormat(value) {
            if (!value.trim()) {
                this.clearFeedback();
                return;
            }
            
            const digitsOnly = value.replace(/[^0-9]/g, '');
            
            // Philippines local format
            if (value.startsWith('09')) {
                if (digitsOnly.length === 11) {
                    const networkCode = digitsOnly.substring(2, 4);
                    if (CONFIG.phone.validNetworkCodes.includes(networkCode)) {
                        this.showSuccess('Valid Philippines mobile number');
                        this.formatPhilippinesLocal(value);
                    } else {
                        this.showError('Invalid Philippines network code');
                    }
                } else if (digitsOnly.length < 11) {
                    this.showWarning(`Need ${11 - digitsOnly.length} more digits`);
                } else {
                    this.showError('Philippines numbers should be exactly 11 digits');
                }
            }
            // Philippines international format
            else if (value.startsWith('+63')) {
                if (digitsOnly.length === 13) {
                    const networkCode = digitsOnly.substring(4, 6);
                    if (CONFIG.phone.validNetworkCodes.includes(networkCode)) {
                        this.showSuccess('Valid Philippines international number');
                        this.formatPhilippinesInternational(value);
                    } else {
                        this.showError('Invalid Philippines network code');
                    }
                } else if (digitsOnly.length < 13) {
                    this.showWarning(`Need ${13 - digitsOnly.length} more digits`);
                } else {
                    this.showError('Philippines international numbers should be exactly 13 digits');
                }
            }
            // Other international numbers
            else if (value.startsWith('+')) {
                if (digitsOnly.length >= 11 && digitsOnly.length <= 15) {
                    this.showSuccess('Valid international number');
                } else if (digitsOnly.length < 11) {
                    this.showWarning(`Need ${11 - digitsOnly.length} more digits`);
                } else {
                    this.showError('International numbers should be 11-15 digits');
                }
            }
            // Local numbers (non-Philippines)
            else if (digitsOnly.length === 11) {
                this.showSuccess('Valid 11-digit number');
            } else if (digitsOnly.length > 0 && digitsOnly.length < 11) {
                this.showWarning(`Need ${11 - digitsOnly.length} more digits`);
            } else if (digitsOnly.length > 11) {
                this.showError('Local numbers should be exactly 11 digits');
            }
        }
        
        formatPhilippinesLocal(value) {
            const digitsOnly = value.replace(/[^0-9]/g, '');
            if (digitsOnly.length === 11) {
                const formatted = `${digitsOnly.substring(0, 4)} ${digitsOnly.substring(4, 7)} ${digitsOnly.substring(7)}`;
                if (this.input.value !== formatted) {
                    this.input.value = formatted;
                }
            }
        }
        
        formatPhilippinesInternational(value) {
            const digitsOnly = value.replace(/[^0-9]/g, '');
            if (digitsOnly.length === 13) {
                const formatted = `+${digitsOnly.substring(0, 2)} ${digitsOnly.substring(2, 5)} ${digitsOnly.substring(5, 8)} ${digitsOnly.substring(8)}`;
                if (this.input.value !== formatted) {
                    this.input.value = formatted;
                }
            }
        }
        
        showSuccess(message) {
            this.feedback.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i>${message}`;
            this.feedback.className = 'form-text phone-feedback text-success';
            this.input.classList.remove('is-invalid');
            this.input.classList.add('is-valid');
        }
        
        showError(message) {
            this.feedback.innerHTML = `<i class="fas fa-exclamation-triangle text-danger mr-1"></i>${message}`;
            this.feedback.className = 'form-text phone-feedback text-danger';
            this.input.classList.remove('is-valid');
            this.input.classList.add('is-invalid');
        }
        
        showWarning(message) {
            this.feedback.innerHTML = `<i class="fas fa-info-circle text-warning mr-1"></i>${message}`;
            this.feedback.className = 'form-text phone-feedback text-warning';
            this.input.classList.remove('is-valid', 'is-invalid');
        }
        
        clearFeedback() {
            this.feedback.innerHTML = 'Enter phone number (e.g., +639XX XXX XXXX or 09XX XXX XXXX)';
            this.feedback.className = 'form-text phone-feedback text-muted';
            this.input.classList.remove('is-valid', 'is-invalid');
        }
    }
    
    /**
     * Email Validation
     */
    class EmailValidator {
        constructor(input) {
            this.input = input;
            this.feedback = this.getFeedbackElement();
            this.setupEventListeners();
        }
        
        getFeedbackElement() {
            let feedback = this.input.parentElement.querySelector('.email-feedback');
            if (!feedback) {
                feedback = document.createElement('small');
                feedback.className = 'form-text email-feedback';
                this.input.parentElement.appendChild(feedback);
            }
            return feedback;
        }
        
        setupEventListeners() {
            // Prevent exceeding max length
            this.input.addEventListener('keypress', (e) => this.handleKeyPress(e));
            
            // Validate on input
            this.input.addEventListener('input', (e) => this.handleInput(e));
            
            // Handle paste events
            this.input.addEventListener('paste', (e) => this.handlePaste(e));
        }
        
        handleKeyPress(e) {
            // Allow control keys
            if (e.ctrlKey || e.metaKey || ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
                return;
            }
            
            // Check length limit
            if (this.input.value.length >= CONFIG.email.maxLength) {
                e.preventDefault();
                this.showError(`Email address cannot exceed ${CONFIG.email.maxLength} characters`);
                return;
            }
        }
        
        handleInput(e) {
            const value = e.target.value;
            
            // Enforce max length
            if (value.length > CONFIG.email.maxLength) {
                this.input.value = value.substring(0, CONFIG.email.maxLength);
                this.showError(`Email address cannot exceed ${CONFIG.email.maxLength} characters`);
                return;
            }
            
            this.validateEmail(value);
        }
        
        handlePaste(e) {
            setTimeout(() => {
                const value = this.input.value;
                if (value.length > CONFIG.email.maxLength) {
                    this.input.value = value.substring(0, CONFIG.email.maxLength);
                    this.showError(`Email address cannot exceed ${CONFIG.email.maxLength} characters`);
                }
                this.validateEmail(this.input.value);
            }, 0);
        }
        
        validateEmail(email) {
            if (!email.trim()) {
                this.clearFeedback();
                return;
            }
            
            // Basic format check
            if (!email.includes('@')) {
                this.showError('Email must contain @ symbol');
                return;
            }
            
            const parts = email.split('@');
            if (parts.length !== 2) {
                this.showError('Email must contain exactly one @ symbol');
                return;
            }
            
            const [localPart, domainPart] = parts;
            
            // Check local part length
            if (localPart.length > CONFIG.email.localPartMaxLength) {
                this.showError(`Email username cannot exceed ${CONFIG.email.localPartMaxLength} characters`);
                return;
            }
            
            // Check for empty parts
            if (!localPart) {
                this.showError('Email cannot start with @');
                return;
            }
            
            if (!domainPart) {
                this.showError('Email must have a domain after @');
                return;
            }
            
            // Check for consecutive dots
            if (email.includes('..')) {
                this.showError('Email cannot contain consecutive dots');
                return;
            }
            
            // Check starting/ending dots
            if (localPart.startsWith('.') || localPart.endsWith('.')) {
                this.showError('Email username cannot start or end with a dot');
                return;
            }
            
            // Check domain format
            if (!domainPart.includes('.')) {
                this.showError('Email domain must contain a dot');
                return;
            }
            
            const domainParts = domainPart.split('.');
            const tld = domainParts[domainParts.length - 1];
            
            // Check TLD
            if (tld.length < 2) {
                this.showError('Email domain extension must be at least 2 characters');
                return;
            }
            
            if (/^\d+$/.test(tld)) {
                this.showError('Email domain extension cannot be all numbers');
                return;
            }
            
            // Final regex validation
            if (CONFIG.email.pattern.test(email)) {
                this.showSuccess('Valid email format');
            } else {
                this.showError('Invalid email format');
            }
        }
        
        showSuccess(message) {
            this.feedback.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i>${message}`;
            this.feedback.className = 'form-text email-feedback text-success';
            this.input.classList.remove('is-invalid');
            this.input.classList.add('is-valid');
        }
        
        showError(message) {
            this.feedback.innerHTML = `<i class="fas fa-exclamation-triangle text-danger mr-1"></i>${message}`;
            this.feedback.className = 'form-text email-feedback text-danger';
            this.input.classList.remove('is-valid');
            this.input.classList.add('is-invalid');
        }
        
        clearFeedback() {
            this.feedback.innerHTML = 'This will be used for login credentials and notifications';
            this.feedback.className = 'form-text email-feedback text-muted';
            this.input.classList.remove('is-valid', 'is-invalid');
        }
    }
    
    /**
     * Initialize validation when DOM is ready
     */
    function initializeValidation() {
        // Initialize phone validation for all phone inputs
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            new PhoneValidator(input);
        });
        
        // Initialize email validation for all email inputs
        document.querySelectorAll('input[type="email"]').forEach(input => {
            new EmailValidator(input);
        });
        
        console.log('Enhanced validation initialized successfully');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeValidation);
    } else {
        initializeValidation();
    }
    
    // Also initialize when jQuery is ready (for compatibility)
    if (typeof $ !== 'undefined') {
        $(document).ready(initializeValidation);
    }
    
})();