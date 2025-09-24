/**
 * AJAX Request Debugger
 * Comprehensive debugging tool to track AJAX requests and identify JSON parsing issues
 * This file can be temporarily included to debug specific issues
 */

(function() {
    'use strict';

    // Debug mode - set to false in production
    const DEBUG_MODE = true;
    const LOG_PREFIX = '[AJAX-DEBUG]';

    if (!DEBUG_MODE) {
        console.log(LOG_PREFIX + ' Debug mode is disabled');
        return;
    }

    // Track all AJAX requests
    const requestTracker = {
        requests: [],
        addRequest: function(request) {
            this.requests.push({
                ...request,
                timestamp: new Date().toISOString()
            });
            
            // Keep only last 50 requests
            if (this.requests.length > 50) {
                this.requests.shift();
            }
        },
        getRequests: function() {
            return this.requests;
        },
        getFailedRequests: function() {
            return this.requests.filter(req => !req.success);
        }
    };

    // Enhanced XMLHttpRequest monitoring
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    const originalXHRSetRequestHeader = XMLHttpRequest.prototype.setRequestHeader;

    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        this._debugInfo = {
            method: method,
            url: url,
            headers: {},
            startTime: performance.now()
        };
        
        console.log(LOG_PREFIX + ` Opening ${method} request to: ${url}`);
        return originalXHROpen.call(this, method, url, async, user, password);
    };

    XMLHttpRequest.prototype.setRequestHeader = function(name, value) {
        if (this._debugInfo) {
            this._debugInfo.headers[name] = value;
        }
        return originalXHRSetRequestHeader.call(this, name, value);
    };

    XMLHttpRequest.prototype.send = function(data) {
        if (this._debugInfo) {
            this._debugInfo.requestBody = data;
            
            const xhr = this;
            const originalOnReadyStateChange = this.onreadystatechange;
            
            this.onreadystatechange = function() {
                if (this.readyState === 4) {
                    const endTime = performance.now();
                    const duration = endTime - xhr._debugInfo.startTime;
                    
                    const requestInfo = {
                        method: xhr._debugInfo.method,
                        url: xhr._debugInfo.url,
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        responseType: xhr.responseType,
                        headers: xhr._debugInfo.headers,
                        requestBody: xhr._debugInfo.requestBody,
                        duration: duration,
                        success: xhr.status >= 200 && xhr.status < 300
                    };

                    console.group(LOG_PREFIX + ` ${xhr._debugInfo.method} ${xhr._debugInfo.url} - ${xhr.status} (${duration.toFixed(2)}ms)`);
                    
                    if (requestInfo.success) {
                        console.log('✅ Request succeeded');
                    } else {
                        console.warn('❌ Request failed');
                    }
                    
                    console.log('Request Details:', {
                        url: requestInfo.url,
                        method: requestInfo.method,
                        status: `${requestInfo.status} ${requestInfo.statusText}`,
                        duration: `${duration.toFixed(2)}ms`
                    });
                    
                    if (Object.keys(requestInfo.headers).length > 0) {
                        console.log('Request Headers:', requestInfo.headers);
                    }
                    
                    if (requestInfo.requestBody) {
                        console.log('Request Body:', requestInfo.requestBody);
                    }
                    
                    // Check response content type
                    const contentType = xhr.getResponseHeader('content-type');
                    console.log('Response Content-Type:', contentType);
                    
                    // Analyze response
                    if (xhr.responseText) {
                        const responsePreview = xhr.responseText.substring(0, 500);
                        console.log('Response Preview:', responsePreview);
                        
                        // Check if response looks like JSON
                        const looksLikeJSON = xhr.responseText.trim().startsWith('{') || xhr.responseText.trim().startsWith('[');
                        const isJSONContentType = contentType && contentType.includes('application/json');
                        
                        if (isJSONContentType && !looksLikeJSON) {
                            console.error('🚨 POTENTIAL ISSUE: Response has JSON content-type but doesn\'t look like JSON!');
                            console.log('Full Response:', xhr.responseText);
                        }
                        
                        if (!isJSONContentType && looksLikeJSON) {
                            console.warn('⚠️  Response looks like JSON but content-type is not JSON');
                        }
                        
                        // Try to parse as JSON
                        if (looksLikeJSON || isJSONContentType) {
                            try {
                                const parsed = JSON.parse(xhr.responseText);
                                console.log('✅ JSON Parse Successful:', parsed);
                            } catch (jsonError) {
                                console.error('🚨 JSON PARSE ERROR:', jsonError.message);
                                console.log('Response that failed to parse:', xhr.responseText);
                            }
                        }
                    } else {
                        console.log('Empty response');
                    }
                    
                    console.groupEnd();
                    
                    requestTracker.addRequest(requestInfo);
                }
                
                if (originalOnReadyStateChange) {
                    return originalOnReadyStateChange.apply(this, arguments);
                }
            };
        }
        
        return originalXHRSend.call(this, data);
    };

    // Monitor fetch API
    const originalFetch = window.fetch;
    window.fetch = function(resource, init = {}) {
        const url = resource instanceof Request ? resource.url : resource;
        const method = init.method || (resource instanceof Request ? resource.method : 'GET');
        const startTime = performance.now();
        
        console.log(LOG_PREFIX + ` Fetch ${method} request to: ${url}`);
        
        return originalFetch.call(this, resource, init)
            .then(async (response) => {
                const endTime = performance.now();
                const duration = endTime - startTime;
                
                // Clone response to read it without consuming the original
                const responseClone = response.clone();
                let responseText = '';
                
                try {
                    responseText = await responseClone.text();
                } catch (e) {
                    console.warn(LOG_PREFIX + ' Could not read response text:', e.message);
                }
                
                const requestInfo = {
                    method: method,
                    url: url,
                    status: response.status,
                    statusText: response.statusText,
                    responseText: responseText,
                    headers: Object.fromEntries(response.headers.entries()),
                    duration: duration,
                    success: response.ok
                };
                
                console.group(LOG_PREFIX + ` Fetch ${method} ${url} - ${response.status} (${duration.toFixed(2)}ms)`);
                
                if (response.ok) {
                    console.log('✅ Fetch succeeded');
                } else {
                    console.warn('❌ Fetch failed');
                }
                
                console.log('Response Headers:', requestInfo.headers);
                
                if (responseText) {
                    const responsePreview = responseText.substring(0, 500);
                    console.log('Response Preview:', responsePreview);
                }
                
                console.groupEnd();
                
                requestTracker.addRequest(requestInfo);
                
                return response;
            })
            .catch((error) => {
                const endTime = performance.now();
                const duration = endTime - startTime;
                
                console.error(LOG_PREFIX + ` Fetch ${method} ${url} failed (${duration.toFixed(2)}ms):`, error);
                
                requestTracker.addRequest({
                    method: method,
                    url: url,
                    status: 0,
                    statusText: 'Network Error',
                    error: error.message,
                    duration: duration,
                    success: false
                });
                
                throw error;
            });
    };

    // jQuery AJAX monitoring (if jQuery is available)
    $(document).ready(function() {
        if (typeof $ !== 'undefined' && $.ajaxSetup) {
            // Enhanced jQuery AJAX monitoring
            $(document).ajaxStart(function() {
                console.log(LOG_PREFIX + ' jQuery AJAX request started');
            });
            
            $(document).ajaxComplete(function(event, xhr, settings) {
                console.group(LOG_PREFIX + ` jQuery ${settings.type} ${settings.url} - ${xhr.status}`);
                console.log('Settings:', settings);
                
                if (xhr.responseText) {
                    const responsePreview = xhr.responseText.substring(0, 500);
                    console.log('Response Preview:', responsePreview);
                    
                    // Check for JSON parsing issues
                    const contentType = xhr.getResponseHeader('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            console.log('✅ JSON Parse Successful');
                        } catch (e) {
                            console.error('🚨 JQUERY AJAX JSON PARSE ERROR:', e.message);
                            console.log('Response that failed:', xhr.responseText);
                        }
                    }
                }
                
                console.groupEnd();
            });
            
            $(document).ajaxError(function(event, xhr, settings, thrownError) {
                console.error(LOG_PREFIX + ` jQuery AJAX Error - ${settings.type} ${settings.url}:`, {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: thrownError,
                    responseText: xhr.responseText ? xhr.responseText.substring(0, 200) : ''
                });
            });
        }
    });

    // Make debugging tools globally available
    window.AjaxDebugger = {
        getRequests: () => requestTracker.getRequests(),
        getFailedRequests: () => requestTracker.getFailedRequests(),
        clearHistory: () => { requestTracker.requests = []; },
        exportLogs: () => {
            const logs = {
                timestamp: new Date().toISOString(),
                requests: requestTracker.getRequests(),
                failedRequests: requestTracker.getFailedRequests()
            };
            console.log('AJAX Debug Export:', JSON.stringify(logs, null, 2));
            return logs;
        },
        findJSONErrors: () => {
            return requestTracker.getRequests().filter(req => 
                req.responseText && 
                (req.responseText.includes('JSON.parse') || 
                 req.responseText.includes('Unexpected token') ||
                 req.responseText.includes('Appointment'))
            );
        }
    };

    console.log(LOG_PREFIX + ' AJAX Debugger initialized. Available commands:');
    console.log('- AjaxDebugger.getRequests() - Get all requests');
    console.log('- AjaxDebugger.getFailedRequests() - Get failed requests');
    console.log('- AjaxDebugger.findJSONErrors() - Find potential JSON errors');
    console.log('- AjaxDebugger.exportLogs() - Export debug logs');
    console.log('- AjaxDebugger.clearHistory() - Clear request history');

})();