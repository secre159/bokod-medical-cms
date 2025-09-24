/**
 * Global Error Handler
 * Catches and handles JavaScript errors across the application
 * Prevents JSON parsing errors and AJAX failures from breaking the UI
 */

(function() {
    'use strict';

    // Global error handler for uncaught JavaScript errors
    window.addEventListener('error', function(event) {
        console.error('Global JavaScript Error:', {
            message: event.message,
            filename: event.filename,
            line: event.lineno,
            column: event.colno,
            error: event.error
        });

        // Check if it's a JSON parsing error
        if (event.message && event.message.includes('JSON')) {
            console.warn('JSON parsing error detected and handled globally');
            event.preventDefault(); // Prevent default error handling
            return true; // Prevent the error from bubbling up
        }

        return false; // Let other errors bubble normally
    });

    // Global promise rejection handler
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled Promise Rejection:', event.reason);

        // Handle JSON-related promise rejections
        if (event.reason && typeof event.reason === 'string' && event.reason.includes('JSON')) {
            console.warn('JSON-related promise rejection handled globally');
            event.preventDefault();
        }
    });

    // Enhanced JSON.parse wrapper with detailed error reporting
    const originalJSONParse = JSON.parse;
    JSON.parse = function(text) {
        try {
            return originalJSONParse.call(this, text);
        } catch (error) {
            console.error('JSON.parse failed:', {
                text: text,
                textType: typeof text,
                textLength: text ? text.length : 0,
                error: error.message,
                stack: error.stack
            });

            // Provide more specific error handling
            if (typeof text === 'string') {
                if (text.trim() === '') {
                    console.warn('Attempting to parse empty string as JSON, returning null');
                    return null;
                }

                if (text.includes('<html') || text.includes('<!DOCTYPE')) {
                    console.warn('Attempting to parse HTML as JSON, returning empty object');
                    return {};
                }

                if (text.startsWith('Appointment') || text.includes('Error') || text.includes('Warning')) {
                    console.warn('Attempting to parse text/message as JSON, returning empty object');
                    return {};
                }
            }

            // Re-throw the error if it's legitimate JSON that's malformed
            throw error;
        }
    };

    // AJAX error handler for jQuery
    $(document).ready(function() {
        // Set up global AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            console.error('AJAX Error:', {
                url: settings.url,
                type: settings.type,
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText ? xhr.responseText.substring(0, 200) + '...' : '',
                error: thrownError
            });

            // Handle common error scenarios
            if (xhr.status === 404) {
                console.warn('AJAX 404 error - endpoint not found');
            } else if (xhr.status === 500) {
                console.warn('AJAX 500 error - server error');
            } else if (xhr.status === 0) {
                console.warn('AJAX network error or request cancelled');
            }

            // Try to parse response as JSON and handle gracefully
            if (xhr.responseText) {
                try {
                    JSON.parse(xhr.responseText);
                } catch (e) {
                    console.warn('AJAX response is not valid JSON:', xhr.responseText.substring(0, 100));
                }
            }
        });

        // Global AJAX setup for consistent error handling
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                // Add CSRF token to all requests
                const token = $('meta[name="csrf-token"]').attr('content');
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }

                // Set default content type only for non-FormData requests
                // Don't set Content-Type when contentType is explicitly set to false (FormData)
                // or when processData is false (indicates FormData/file upload)
                if (!settings.contentType && settings.type === 'POST' && 
                    settings.contentType !== false && settings.processData !== false) {
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                }
            },
            error: function(xhr, status, error) {
                // This will be caught by the ajaxError handler above
                console.log('AJAX request failed:', status, error);
            }
        });
    });

    // Safe data attribute parsing
    window.safeDataParse = function(element, attribute, defaultValue) {
        try {
            const dataStr = element.getAttribute('data-' + attribute);
            if (!dataStr || dataStr.trim() === '') {
                return defaultValue || null;
            }
            return JSON.parse(dataStr);
        } catch (e) {
            console.warn(`Failed to parse data-${attribute}:`, dataStr, 'Error:', e.message);
            return defaultValue || null;
        }
    };

    // Safe JSON response handler for fetch API
    window.safeFetchJSON = async function(url, options = {}) {
        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const text = await response.text();
            
            try {
                return JSON.parse(text);
            } catch (jsonError) {
                console.warn('Response is not valid JSON:', text.substring(0, 100));
                return { error: 'Invalid JSON response', text: text };
            }
        } catch (error) {
            console.error('Fetch error:', error);
            return { error: error.message };
        }
    };

    // Initialize safe parsing for existing data attributes
    $(document).ready(function() {
        $('[data-json]').each(function() {
            const $element = $(this);
            const jsonData = $element.attr('data-json');
            
            try {
                JSON.parse(jsonData);
            } catch (e) {
                console.warn('Invalid JSON in data-json attribute, removing:', jsonData);
                $element.removeAttr('data-json');
                $element.attr('data-json-error', 'Invalid JSON removed');
            }
        });
    });

    console.log('Global error handler initialized successfully');

})();