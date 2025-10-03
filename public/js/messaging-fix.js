/**
 * Messaging System JavaScript Fix
 * Fixes stuck send button and improves error handling
 */

// Add timeout protection for AJAX requests
function addTimeoutProtection() {
    // Override the default AJAX setup to add timeout
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({
            timeout: 10000 // 10 seconds timeout
        });
        
        console.log('✅ AJAX timeout protection enabled (10s)');
    }
}

// Force re-enable send button after timeout
function forceReEnableSendButton() {
    setTimeout(() => {
        const sendBtn = document.querySelector('#send-btn, .send-btn, button[type="submit"]');
        const textarea = document.querySelector('#message-textarea, .message-textarea');
        
        if (sendBtn && sendBtn.disabled) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            console.log('🔄 Force re-enabled stuck send button');
        }
        
        if (textarea && textarea.disabled) {
            textarea.disabled = false;
            console.log('🔄 Force re-enabled textarea');
        }
    }, 15000); // Force re-enable after 15 seconds
}

// Enhanced error handling for message sending
function enhanceMessageSending() {
    if (typeof $ === 'undefined') {
        console.warn('⚠️  jQuery not loaded - using vanilla JS fallbacks');
        return;
    }
    
    // Intercept form submission
    $(document).on('submit', '#message-form', function(e) {
        console.log('🚀 Message form submitted');
        
        const sendBtn = $(this).find('#send-btn');
        const textarea = $(this).find('#message-textarea');
        
        // Set timeout protection
        forceReEnableSendButton();
        
        // Store original states for recovery
        window.messagingState = {
            originalButtonHtml: sendBtn.html(),
            formSubmitted: true,
            submitTime: Date.now()
        };
    });
    
    // Intercept AJAX complete events
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.url && settings.url.includes('/send')) {
            console.log('📨 Message AJAX completed', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseTime: Date.now() - (window.messagingState?.submitTime || 0) + 'ms'
            });
            
            // Always re-enable button after AJAX complete
            setTimeout(() => {
                const sendBtn = $('#send-btn');
                const textarea = $('#message-textarea');
                
                if (sendBtn.length && sendBtn.prop('disabled')) {
                    sendBtn.prop('disabled', false)
                          .html(window.messagingState?.originalButtonHtml || '<i class="fas fa-paper-plane"></i>');
                    console.log('✅ Send button re-enabled after AJAX complete');
                }
                
                if (textarea.length && textarea.prop('disabled')) {
                    textarea.prop('disabled', false);
                    console.log('✅ Textarea re-enabled after AJAX complete');
                }
            }, 100);
        }
    });
    
    // Handle AJAX errors
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        if (settings.url && settings.url.includes('/send')) {
            console.error('❌ Message AJAX failed', {
                status: xhr.status,
                statusText: xhr.statusText,
                error: thrownError,
                responseText: xhr.responseText
            });
            
            // Always re-enable on error
            const sendBtn = $('#send-btn');
            const textarea = $('#message-textarea');
            
            sendBtn.prop('disabled', false)
                   .html(window.messagingState?.originalButtonHtml || '<i class="fas fa-paper-plane"></i>');
            textarea.prop('disabled', false);
            
            // Show user-friendly error
            if (typeof showNotification === 'function') {
                showNotification('Message sending failed. Please try again.', 'error');
            } else {
                alert('Message sending failed. Please try again.');
            }
        }
    });
}

// Emergency button reset function (can be called from console)
window.resetMessagingUI = function() {
    const sendBtn = document.querySelector('#send-btn, .send-btn');
    const textarea = document.querySelector('#message-textarea, .message-textarea');
    
    if (sendBtn) {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        console.log('🔄 Emergency reset: Send button enabled');
    }
    
    if (textarea) {
        textarea.disabled = false;
        textarea.value = '';
        console.log('🔄 Emergency reset: Textarea enabled and cleared');
    }
    
    console.log('✅ Emergency UI reset completed. You can now send messages.');
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        addTimeoutProtection();
        enhanceMessageSending();
        console.log('✅ Messaging fixes initialized');
    });
} else {
    addTimeoutProtection();
    enhanceMessageSending();
    console.log('✅ Messaging fixes initialized (DOM already ready)');
}

// Debug information
console.log('🔧 Messaging Fix Script Loaded');
console.log('💡 To manually reset UI, run: resetMessagingUI()');