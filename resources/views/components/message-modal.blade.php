<!-- Global Message Modal Component -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0" id="messageModalHeader">
                <h5 class="modal-title d-flex align-items-center" id="messageModalLabel">
                    <i id="messageModalIcon" class="fas fa-info-circle mr-2"></i>
                    <span id="messageModalTitle">Message</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <p id="messageModalText" class="mb-0"></p>
                <ul id="messageModalList" class="mb-0 mt-2" style="display: none;"></ul>
            </div>
            <div class="modal-footer border-0" id="messageModalFooter">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="messageModalCancel">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="messageModalAction" style="display: none;">
                    <i class="fas fa-check mr-1"></i>OK
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal styling for different message types */
.modal-success .modal-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.modal-success .modal-header .close {
    color: white;
    opacity: 0.8;
}

.modal-success .modal-header .close:hover {
    opacity: 1;
}

.modal-error .modal-header,
.modal-danger .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    color: white;
}

.modal-error .modal-header .close,
.modal-danger .modal-header .close {
    color: white;
    opacity: 0.8;
}

.modal-error .modal-header .close:hover,
.modal-danger .modal-header .close:hover {
    opacity: 1;
}

.modal-warning .modal-header {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.modal-info .modal-header {
    background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
    color: white;
}

.modal-info .modal-header .close {
    color: white;
    opacity: 0.8;
}

.modal-info .modal-header .close:hover {
    opacity: 1;
}

/* Animation effects */
.modal.fade .modal-dialog {
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translate(0, 0);
}

/* Icon styling */
#messageModalIcon {
    font-size: 1.25rem;
}

/* List styling for errors */
#messageModalList {
    padding-left: 1.25rem;
}

#messageModalList li {
    margin-bottom: 0.25rem;
}

/* HTML content styling */
#messageModalText {
    line-height: 1.5;
}

#messageModalText strong {
    font-weight: 600;
    color: inherit;
}

#messageModalText br {
    margin-bottom: 0.5rem;
}

/* Style for success messages with HTML */
.modal-success #messageModalText strong {
    color: #155724;
}

/* Style for warning messages with HTML */
.modal-warning #messageModalText strong {
    color: #721c24;
}

/* Style for info messages with HTML */
.modal-info #messageModalText strong {
    color: #0c5460;
}
</style>

<script>
// Global message modal functions
window.MessageModal = {
    show: function(type, title, message, options = {}) {
        const modal = $('#messageModal');
        const header = $('#messageModalHeader');
        const icon = $('#messageModalIcon');
        const titleEl = $('#messageModalTitle');
        const textEl = $('#messageModalText');
        const listEl = $('#messageModalList');
        const actionBtn = $('#messageModalAction');
        
        // Reset classes
        modal.removeClass('modal-success modal-error modal-danger modal-warning modal-info');
        
        // Set type-specific styling
        let iconClass, modalClass;
        switch(type) {
            case 'success':
                iconClass = 'fas fa-check-circle';
                modalClass = 'modal-success';
                break;
            case 'error':
            case 'danger':
                iconClass = 'fas fa-exclamation-triangle';
                modalClass = 'modal-error';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-circle';
                modalClass = 'modal-warning';
                break;
            case 'info':
            default:
                iconClass = 'fas fa-info-circle';
                modalClass = 'modal-info';
        }
        
        // Apply styling
        modal.addClass(modalClass);
        icon.removeClass().addClass(iconClass + ' mr-2');
        titleEl.text(title);
        
        // Handle message content
        if (Array.isArray(message)) {
            // Multiple messages (errors)
            textEl.hide();
            listEl.empty().show();
            message.forEach(msg => {
                listEl.append(`<li>${msg}</li>`);
            });
        } else {
            // Single message - use html() to allow HTML content
            listEl.hide();
            textEl.html(message).show();
        }
        
        // Handle action button
        if (options.actionText && options.actionCallback) {
            actionBtn.text(options.actionText).show().off('click').on('click', function() {
                options.actionCallback();
                modal.modal('hide');
            });
        } else {
            actionBtn.hide();
        }
        
        // Auto-dismiss option
        if (options.autoDismiss) {
            setTimeout(() => {
                modal.modal('hide');
            }, options.autoDismiss);
        }
        
        // Show modal
        modal.modal('show');
    },
    
    success: function(message, options = {}) {
        this.show('success', 'Success!', message, options);
    },
    
    error: function(message, options = {}) {
        const title = Array.isArray(message) ? 'Please fix the following errors:' : 'Error!';
        this.show('error', title, message, options);
    },
    
    warning: function(message, options = {}) {
        this.show('warning', 'Warning!', message, options);
    },
    
    info: function(message, options = {}) {
        this.show('info', 'Information', message, options);
    },
    
    confirm: function(message, onConfirm, onCancel = null, options = {}) {
        const defaultOptions = {
            title: 'Confirm Action',
            confirmText: 'Confirm',
            confirmClass: 'btn-danger',
            cancelText: 'Cancel',
            icon: 'fas fa-question-circle'
        };
        
        const config = Object.assign(defaultOptions, options);
        
        // Show modal with confirmation styling
        const modal = $('#messageModal');
        const header = $('#messageModalHeader');
        const icon = $('#messageModalIcon');
        const titleEl = $('#messageModalTitle');
        const textEl = $('#messageModalText');
        const listEl = $('#messageModalList');
        const actionBtn = $('#messageModalAction');
        const cancelBtn = $('#messageModalCancel');
        
        // Reset classes
        modal.removeClass('modal-success modal-error modal-danger modal-warning modal-info');
        modal.addClass('modal-warning');
        
        // Set content
        icon.removeClass().addClass(config.icon + ' mr-2');
        titleEl.text(config.title);
        listEl.hide();
        textEl.html(message).show();
        
        // Set up buttons
        cancelBtn.html(`<i class="fas fa-times mr-1"></i>${config.cancelText}`);
        actionBtn.html(`<i class="fas fa-check mr-1"></i>${config.confirmText}`)
                 .removeClass('btn-primary btn-secondary btn-success btn-danger btn-warning')
                 .addClass(config.confirmClass)
                 .show();
        
        // Remove previous event listeners
        actionBtn.off('click.confirm');
        cancelBtn.off('click.cancel');
        modal.off('hidden.bs.modal.confirm');
        
        // Add event listeners
        actionBtn.on('click.confirm', function() {
            modal.modal('hide');
            if (onConfirm && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
        
        if (onCancel) {
            cancelBtn.on('click.cancel', function() {
                modal.modal('hide');
                if (typeof onCancel === 'function') {
                    onCancel();
                }
            });
        }
        
        // Show modal
        modal.modal('show');
    }
};

// Convenience functions for easy migration from confirm() and alert()
window.confirmModal = function(message, onConfirm = null, onCancel = null) {
    MessageModal.confirm(message, onConfirm, onCancel);
};

window.alertModal = function(message, type = 'info') {
    MessageModal[type](message);
};

// Legacy showAlert function for compatibility
window.showAlert = function(type, message, options = {}) {
    MessageModal[type](message, options);
};

// Auto-show messages from session on page load
$(document).ready(function() {
    @if (session('success'))
        MessageModal.success('{{ session('success') }}', { autoDismiss: 4000 });
    @endif
    
    @if (session('error'))
        MessageModal.error('{{ session('error') }}');
    @endif
    
    @if (session('warning'))
        MessageModal.warning('{{ session('warning') }}');
    @endif
    
    @if (session('info'))
        MessageModal.info('{{ session('info') }}');
    @endif
    
    @if ($errors->any())
        MessageModal.error([
            @foreach ($errors->all() as $error)
                '{{ addslashes($error) }}',
            @endforeach
        ]);
    @endif
});
</script>