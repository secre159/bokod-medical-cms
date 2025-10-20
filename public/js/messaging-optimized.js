/**
 * Optimized Messaging System JavaScript
 * Fixes performance issues and stuck loading states
 */

// Configuration
const MESSAGING_CONFIG = {
    AJAX_TIMEOUT: 8000, // 8 seconds
    TYPING_INTERVAL: 3000, // Check typing every 3 seconds  
    MESSAGE_REFRESH_INTERVAL: 5000, // Refresh messages every 5 seconds
    MAX_RETRIES: 2,
    DEBUG: false
};

// State management
const MessagingState = {
    isLoading: false,
    typingTimer: null,
    messageTimer: null,
    retryCount: 0,
    lastMessageId: null
};

/**
 * Enhanced AJAX setup with better error handling
 */
function initializeAjax() {
    if (typeof $ === 'undefined') {
        console.warn('⚠️  jQuery not available - messaging may not work properly');
        return;
    }
    
    $.ajaxSetup({
        timeout: MESSAGING_CONFIG.AJAX_TIMEOUT,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            // Global loading state management
            if (MESSAGING_CONFIG.DEBUG) {
                console.log('🌐 AJAX request started');
            }
        },
        complete: function() {
            if (MESSAGING_CONFIG.DEBUG) {
                console.log('🌐 AJAX request completed');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: error,
                responseText: xhr.responseText
            });
        }
    });
}

/**
 * Enhanced message sending with loading state management
 */
function initializeMessageSending() {
    $(document).on('submit', '#message-form', function(e) {
        e.preventDefault();
        
        if (MessagingState.isLoading) {
            console.warn('⚠️  Message already being sent, ignoring duplicate request');
            return false;
        }
        
        const form = $(this);
        const sendBtn = form.find('#send-btn, .send-btn, button[type="submit"]');
        const messageInput = form.find('#message-textarea, textarea[name="message"]');
        const fileInput = form.find('input[type="file"]');
        
        // Check if we have content to send
        const hasMessage = messageInput.val() && messageInput.val().trim().length > 0;
        const hasFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
        
        if (!hasMessage && !hasFile) {
            showAlert('Please enter a message or select a file to send.', 'warning');
            return false;
        }
        
        // Set loading state
        MessagingState.isLoading = true;
        const originalButtonHtml = sendBtn.html();
        
        // Update UI
        sendBtn.prop('disabled', true)
               .html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        messageInput.prop('disabled', true);
        
        // Create form data
        const formData = new FormData(form[0]);
        
        // Send message with timeout protection
        const sendPromise = $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: MESSAGING_CONFIG.AJAX_TIMEOUT
        });
        
        // Handle response
        sendPromise
            .done(function(response) {
                if (response.success) {
                    // Clear form
                    messageInput.val('').focus();
                    if (fileInput.length) fileInput.val('');
                    
                    // Add message to chat
                    if (response.html) {
                        addMessageToChat(response.html);
                    }
                    
                    // Show success feedback
                    showAlert('Message sent successfully!', 'success');
                    
                    // Reset retry count on success
                    MessagingState.retryCount = 0;
                    
                } else {
                    showAlert(response.error || 'Failed to send message', 'error');
                }
            })
            .fail(function(xhr, status, error) {
                let errorMessage = 'Failed to send message. Please try again.';
                
                if (status === 'timeout') {
                    errorMessage = 'Message sending timed out. Please check your connection.';
                } else if (xhr.status === 422) {
                    // Validation error
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || response.error || errorMessage;
                    } catch (e) {
                        // Keep default message
                    }
                }
                
                showAlert(errorMessage, 'error');
                
                // Increment retry count
                MessagingState.retryCount++;
            })
            .always(function() {
                // Always restore UI state
                MessagingState.isLoading = false;
                sendBtn.prop('disabled', false).html(originalButtonHtml);
                messageInput.prop('disabled', false);
                
                if (MESSAGING_CONFIG.DEBUG) {
                    console.log('✅ Message send operation completed');
                }
            });
        
        // Force timeout protection
        setTimeout(function() {
            if (MessagingState.isLoading) {
                console.warn('🕐 Force timeout - restoring UI after 10 seconds');
                MessagingState.isLoading = false;
                sendBtn.prop('disabled', false).html(originalButtonHtml);
                messageInput.prop('disabled', false);
            }
        }, 10000);
        
        return false;
    });
}

/**
 * Add message to chat UI
 */
function addMessageToChat(messageHtml) {
    const messagesContainer = $('.messages-container, #messages-container');
    if (messagesContainer.length) {
        messagesContainer.append(messageHtml);
        // Scroll to bottom
        messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
    }
}

/**
 * Show alert/notification to user
 */
function showAlert(message, type = 'info') {
    // Try multiple notification methods
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'error' ? 'Error' : type === 'success' ? 'Success' : 'Info',
            text: message,
            icon: type === 'error' ? 'error' : type === 'success' ? 'success' : 'info',
            timer: 3000
        });
    } else {
        // Fallback to browser alert
        alert(message);
    }
}

/**
 * Optimized typing status checking
 */
function initializeTypingStatus() {
    const conversationId = $('[data-conversation-id]').data('conversation-id');
    if (!conversationId) return;
    
    let typingTimeout;
    let isTyping = false;
    
    // Send typing status
    $(document).on('input', '#message-textarea, textarea[name="message"]', function() {
        if (!isTyping) {
            isTyping = true;
            sendTypingStatus(conversationId, true);
        }
        
        // Clear existing timeout
        clearTimeout(typingTimeout);
        
        // Set timeout to stop typing
        typingTimeout = setTimeout(() => {
            isTyping = false;
            sendTypingStatus(conversationId, false);
        }, 2000);
    });
    
    // Check others' typing status (less frequently)
    MessagingState.typingTimer = setInterval(() => {
        checkTypingStatus(conversationId);
    }, MESSAGING_CONFIG.TYPING_INTERVAL);
}

/**
 * Send typing status to server
 */
function sendTypingStatus(conversationId, typing) {
    if (typeof $ === 'undefined') return;
    
    $.ajax({
        url: '/admin-messages/typing',
        method: 'POST',
        data: {
            conversation_id: conversationId,
            is_typing: typing
        },
        timeout: 3000, // Shorter timeout for typing
        success: function(response) {
            if (MESSAGING_CONFIG.DEBUG) {
                console.log('📝 Typing status sent:', typing);
            }
        },
        error: function() {
            // Silently fail - typing status is not critical
            if (MESSAGING_CONFIG.DEBUG) {
                console.warn('⚠️  Failed to send typing status');
            }
        }
    });
}

/**
 * Check others' typing status
 */
function checkTypingStatus(conversationId) {
    if (typeof $ === 'undefined') return;
    
    $.ajax({
        url: '/admin-messages/typing',
        method: 'GET', 
        data: { conversation_id: conversationId },
        timeout: 3000,
        success: function(response) {
            updateTypingIndicator(response.is_typing, response.user_name);
        },
        error: function() {
            // Silently fail
            if (MESSAGING_CONFIG.DEBUG) {
                console.warn('⚠️  Failed to check typing status');
            }
        }
    });
}

/**
 * Update typing indicator UI
 */
function updateTypingIndicator(isTyping, userName) {
    const indicator = $('.typing-indicator');
    if (indicator.length) {
        if (isTyping && userName) {
            indicator.text(`${userName} is typing...`).show();
        } else {
            indicator.hide();
        }
    }
}

/**
 * Emergency reset function
 */
window.resetMessagingUI = function() {
    MessagingState.isLoading = false;
    MessagingState.retryCount = 0;
    
    // Reset all buttons and inputs
    $('#send-btn, .send-btn, button[type="submit"]').prop('disabled', false)
        .html('<i class="fas fa-paper-plane"></i>');
    $('#message-textarea, textarea[name="message"]').prop('disabled', false);
    
    console.log('✅ Emergency messaging UI reset completed');
};

/**
 * Cleanup function
 */
function cleanup() {
    if (MessagingState.typingTimer) {
        clearInterval(MessagingState.typingTimer);
    }
    if (MessagingState.messageTimer) {
        clearInterval(MessagingState.messageTimer);
    }
}

/**
 * Initialize everything
 */
function initializeMessaging() {
    if (MESSAGING_CONFIG.DEBUG) {
        console.log('🚀 Initializing optimized messaging system...');
    }
    
    initializeAjax();
    initializeMessageSending();
    initializeTypingStatus();
    
    console.log('✅ Optimized messaging system initialized');
}

// Initialize when DOM is ready
$(document).ready(function() {
    initializeMessaging();
});

// Cleanup on page unload
$(window).on('beforeunload', cleanup);

// Debug info
console.log('📨 Messaging Optimized Script Loaded');
console.log('🛠️  Emergency reset available: resetMessagingUI()');