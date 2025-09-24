/**
 * Universal Modal System
 * Replaces all alert(), confirm(), and prompt() with professional modals
 * Author: Bokod CMS System
 */

(function() {
    'use strict';
    
    /**
     * Create the universal modal structure
     */
    function createUniversalModal() {
        if (document.getElementById('universalModal')) {
            return; // Already exists
        }
        
        const modalHTML = `
            <!-- Universal Modal -->
            <div class="modal fade" id="universalModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" id="universalModalHeader">
                            <h4 class="modal-title" id="universalModalTitle">
                                <i class="fas fa-info-circle mr-2" id="universalModalIcon"></i>
                                <span id="universalModalTitleText">Information</span>
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="universalModalBody">
                            <div class="text-center py-3" id="universalModalContent">
                                <div id="universalModalMessage"></div>
                                <div id="universalModalInput" style="display: none;">
                                    <div class="form-group mt-3">
                                        <input type="text" class="form-control" id="universalModalInputField" placeholder="Enter value...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" id="universalModalFooter">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="universalModalCancel">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="universalModalConfirm">
                                <i class="fas fa-check mr-2"></i>OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add modal styles
        const style = document.createElement('style');
        style.textContent = `
            .universal-modal-success .modal-header {
                background-color: #28a745;
                color: white;
            }
            
            .universal-modal-error .modal-header {
                background-color: #dc3545;
                color: white;
            }
            
            .universal-modal-warning .modal-header {
                background-color: #ffc107;
                color: #212529;
            }
            
            .universal-modal-info .modal-header {
                background-color: #17a2b8;
                color: white;
            }
            
            .universal-modal-question .modal-header {
                background-color: #6c757d;
                color: white;
            }
            
            .universal-modal-large .modal-dialog {
                max-width: 600px;
            }
            
            .universal-modal-small .modal-dialog {
                max-width: 400px;
            }
            
            #universalModalMessage {
                white-space: pre-wrap;
                word-wrap: break-word;
                text-align: left;
            }
            
            .modal-icon-large {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
        `;
        document.head.appendChild(style);
        
        console.log('Universal modal system initialized');
    }
    
    /**
     * Hide modal with proper cleanup
     */
    function hideModal(modal) {
        if (typeof $ !== 'undefined' && $.fn.modal) {
            $('#universalModal').modal('hide');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            } else {
                manualHideModal(modal);
            }
        } else {
            manualHideModal(modal);
        }
    }
    
    /**
     * Manual modal hide for fallback
     */
    function manualHideModal(modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Remove backdrop
        if (modal._backdrop) {
            document.body.removeChild(modal._backdrop);
            modal._backdrop = null;
        }
    }
    
    /**
     * Show modal with different types
     */
    function showModal(options) {
        const modal = document.getElementById('universalModal');
        const modalContent = modal.querySelector('.modal-content');
        const header = document.getElementById('universalModalHeader');
        const title = document.getElementById('universalModalTitleText');
        const icon = document.getElementById('universalModalIcon');
        const message = document.getElementById('universalModalMessage');
        const input = document.getElementById('universalModalInput');
        const inputField = document.getElementById('universalModalInputField');
        const footer = document.getElementById('universalModalFooter');
        const cancelBtn = document.getElementById('universalModalCancel');
        const confirmBtn = document.getElementById('universalModalConfirm');
        
        // Reset classes
        modalContent.className = 'modal-content';
        
        // Set type-specific styling
        const type = options.type || 'info';
        modalContent.classList.add('universal-modal-' + type);
        
        // Set icon based on type
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
            question: 'fa-question-circle'
        };
        
        icon.className = `fas ${icons[type] || icons.info} mr-2`;
        
        // Set title
        title.textContent = options.title || type.charAt(0).toUpperCase() + type.slice(1);
        
        // Set message
        message.innerHTML = options.message || '';
        
        // Handle input field for prompt
        if (options.showInput) {
            input.style.display = 'block';
            inputField.value = options.defaultValue || '';
            inputField.placeholder = options.placeholder || 'Enter value...';
        } else {
            input.style.display = 'none';
        }
        
        // Configure buttons
        if (options.showCancel !== false && (options.type === 'question' || options.showInput)) {
            cancelBtn.style.display = 'inline-block';
        } else {
            cancelBtn.style.display = 'none';
        }
        
        // Set button text
        confirmBtn.innerHTML = `<i class="fas ${options.confirmIcon || 'fa-check'} mr-2"></i>${options.confirmText || 'OK'}`;
        cancelBtn.innerHTML = `<i class="fas fa-times mr-2"></i>${options.cancelText || 'Cancel'}`;
        
        // Set button style
        confirmBtn.className = `btn ${options.confirmClass || 'btn-primary'}`;
        
        // Show modal with fallback methods
        if (typeof $ !== 'undefined' && $.fn.modal) {
            $('#universalModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            // Fallback: Manual modal display
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Create backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
            
            // Store backdrop reference for cleanup
            modal._backdrop = backdrop;
        }
        
        // Return promise for async handling
        return new Promise((resolve, reject) => {
            const cleanup = () => {
                confirmBtn.onclick = null;
                cancelBtn.onclick = null;
                
                // Clean up jQuery event handlers
                if (typeof $ !== 'undefined' && $.fn.off) {
                    $('#universalModal').off('hidden.bs.modal.universalModal');
                }
                
                // Clean up manual event handlers
                const closeBtn = modal.querySelector('.close');
                if (closeBtn) {
                    closeBtn.onclick = null;
                }
                
                if (modal._backdrop) {
                    modal._backdrop.onclick = null;
                }
            };
            
            confirmBtn.onclick = () => {
                const result = options.showInput ? inputField.value : true;
                cleanup();
                hideModal(modal);
                resolve(result);
            };
            
            cancelBtn.onclick = () => {
                cleanup();
                hideModal(modal);
                resolve(false);
            };
            
            // Handle close button and backdrop clicks
            const closeHandler = () => {
                cleanup();
                resolve(false);
            };
            
            // Add event listeners for close button
            const closeBtn = modal.querySelector('.close');
            if (closeBtn) {
                closeBtn.onclick = () => {
                    hideModal(modal);
                    closeHandler();
                };
            }
            
            // Handle modal events based on available library
            if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#universalModal').on('hidden.bs.modal.universalModal', closeHandler);
            } else {
                // For manual modal, handle backdrop click
                const backdrop = modal._backdrop;
                if (backdrop) {
                    backdrop.onclick = () => {
                        hideModal(modal);
                        closeHandler();
                    };
                }
            }
        });
    }
    
    /**
     * Replace native alert()
     */
    function universalAlert(message, title = 'Alert', type = 'info') {
        return showModal({
            type: type,
            title: title,
            message: message,
            showCancel: false
        });
    }
    
    /**
     * Replace native confirm()
     */
    function universalConfirm(message, title = 'Confirm', options = {}) {
        return showModal({
            type: 'question',
            title: title,
            message: message,
            showCancel: true,
            confirmText: options.confirmText || 'Yes',
            cancelText: options.cancelText || 'No',
            confirmClass: options.confirmClass || 'btn-primary',
            confirmIcon: options.confirmIcon || 'fa-check'
        });
    }
    
    /**
     * Replace native prompt()
     */
    function universalPrompt(message, defaultValue = '', title = 'Input Required') {
        return showModal({
            type: 'question',
            title: title,
            message: message,
            showInput: true,
            defaultValue: defaultValue,
            showCancel: true
        });
    }
    
    /**
     * Success alert
     */
    function successAlert(message, title = 'Success') {
        return showModal({
            type: 'success',
            title: title,
            message: message,
            showCancel: false,
            confirmText: 'Great!',
            confirmIcon: 'fa-thumbs-up'
        });
    }
    
    /**
     * Error alert
     */
    function errorAlert(message, title = 'Error') {
        return showModal({
            type: 'error',
            title: title,
            message: message,
            showCancel: false,
            confirmText: 'OK',
            confirmClass: 'btn-danger',
            confirmIcon: 'fa-times'
        });
    }
    
    /**
     * Warning alert
     */
    function warningAlert(message, title = 'Warning') {
        return showModal({
            type: 'warning',
            title: title,
            message: message,
            showCancel: false,
            confirmText: 'Understood',
            confirmClass: 'btn-warning',
            confirmIcon: 'fa-exclamation-triangle'
        });
    }
    
    /**
     * Initialize the modal system
     */
    function init() {
        // Function to set up the modal
        const setupModal = () => {
            createUniversalModal();
            
            // Override native functions (optional, can be disabled)
            if (window.REPLACE_NATIVE_ALERTS !== false) {
                window.originalAlert = window.alert;
                window.originalConfirm = window.confirm;
                window.originalPrompt = window.prompt;
                
                window.alert = universalAlert;
                window.confirm = universalConfirm;
                window.prompt = universalPrompt;
            }
        };
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupModal);
        } else {
            setupModal();
        }
        
        // Also wait for jQuery if it's being loaded asynchronously
        if (typeof $ === 'undefined' && typeof window.jQuery === 'undefined') {
            // Set up a check for jQuery availability
            let jQueryCheckInterval;
            const checkForJQuery = () => {
                if (typeof $ !== 'undefined' || typeof window.jQuery !== 'undefined') {
                    clearInterval(jQueryCheckInterval);
                    console.log('jQuery detected, modal system ready');
                }
            };
            
            jQueryCheckInterval = setInterval(checkForJQuery, 100);
            
            // Clear the interval after 5 seconds to prevent infinite checking
            setTimeout(() => {
                if (jQueryCheckInterval) {
                    clearInterval(jQueryCheckInterval);
                    console.log('Universal Modal: Operating without jQuery');
                }
            }, 5000);
        }
    }
    
    // Make functions globally available
    window.UniversalModal = {
        show: showModal,
        alert: universalAlert,
        confirm: universalConfirm,
        prompt: universalPrompt,
        success: successAlert,
        error: errorAlert,
        warning: warningAlert,
        init: init
    };
    
    // Auto-initialize
    init();
    
})();

/**
 * Convenience functions for backward compatibility
 */

// Safe modal functions with fallback to native alerts
function modalAlert(message, title, type) {
    try {
        if (window.UniversalModal && window.UniversalModal.alert) {
            return window.UniversalModal.alert(message, title, type);
        } else {
            // Fallback to native alert
            alert((title ? title + ': ' : '') + message);
            return Promise.resolve(true);
        }
    } catch (e) {
        console.error('Modal error:', e);
        alert((title ? title + ': ' : '') + message);
        return Promise.resolve(true);
    }
}

function modalConfirm(message, title, options) {
    try {
        if (window.UniversalModal && window.UniversalModal.confirm) {
            return window.UniversalModal.confirm(message, title, options);
        } else {
            // Fallback to native confirm
            return Promise.resolve(confirm((title ? title + ': ' : '') + message));
        }
    } catch (e) {
        console.error('Modal error:', e);
        return Promise.resolve(confirm((title ? title + ': ' : '') + message));
    }
}

function modalPrompt(message, defaultValue, title) {
    try {
        if (window.UniversalModal && window.UniversalModal.prompt) {
            return window.UniversalModal.prompt(message, defaultValue, title);
        } else {
            // Fallback to native prompt
            return Promise.resolve(prompt((title ? title + ': ' : '') + message, defaultValue));
        }
    } catch (e) {
        console.error('Modal error:', e);
        return Promise.resolve(prompt((title ? title + ': ' : '') + message, defaultValue));
    }
}

function modalSuccess(message, title) {
    try {
        if (window.UniversalModal && window.UniversalModal.success) {
            return window.UniversalModal.success(message, title);
        } else {
            alert((title || 'Success') + ': ' + message);
            return Promise.resolve(true);
        }
    } catch (e) {
        console.error('Modal error:', e);
        alert((title || 'Success') + ': ' + message);
        return Promise.resolve(true);
    }
}

function modalError(message, title) {
    try {
        if (window.UniversalModal && window.UniversalModal.error) {
            return window.UniversalModal.error(message, title);
        } else {
            alert((title || 'Error') + ': ' + message);
            return Promise.resolve(true);
        }
    } catch (e) {
        console.error('Modal error:', e);
        alert((title || 'Error') + ': ' + message);
        return Promise.resolve(true);
    }
}

function modalWarning(message, title) {
    try {
        if (window.UniversalModal && window.UniversalModal.warning) {
            return window.UniversalModal.warning(message, title);
        } else {
            alert((title || 'Warning') + ': ' + message);
            return Promise.resolve(true);
        }
    } catch (e) {
        console.error('Modal error:', e);
        alert((title || 'Warning') + ': ' + message);
        return Promise.resolve(true);
    }
}

console.log('Universal Modal System loaded successfully');