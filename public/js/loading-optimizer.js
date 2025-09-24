// Fast Loading Optimizer - Removes slow preloaders
document.addEventListener('DOMContentLoaded', function() {
    // Remove any loading screens immediately when DOM is ready
    const loadingElements = document.querySelectorAll('.preloader, .loading, [class*="loading"]');
    loadingElements.forEach(element => {
        if (element) {
            element.style.display = 'none';
            element.remove();
        }
    });
    
    // Hide AdminLTE preloader if it exists
    if (typeof $ !== 'undefined') {
        $('.preloader').fadeOut('fast');
        $('[class*="preloader"]').hide();
    }
    
    // Force show body content
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
    
    console.log('Loading optimizer executed - removed preloaders');
});

// Also run immediately in case DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready - optimizing loading');
    });
} else {
    console.log('DOM already loaded - running optimizer immediately');
    
    const loadingElements = document.querySelectorAll('.preloader, .loading, [class*="loading"]');
    loadingElements.forEach(element => {
        if (element) {
            element.style.display = 'none';
        }
    });
    
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
}

// Fast Loading Optimizer
document.addEventListener('DOMContentLoaded', function() {
    // Remove any loading screens immediately when DOM is ready
    const loadingElements = document.querySelectorAll('.preloader, .loading, [class*="loading"]');
    loadingElements.forEach(element => {
        if (element) {
            element.style.display = 'none';
            element.remove();
        }
    });
    
    // Hide AdminLTE preloader if it exists
    if (typeof $ !== 'undefined') {
        $('.preloader').fadeOut('fast');
        $('[class*="preloader"]').hide();
    }
    
    // Force show body content
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
    
    // Console log for debugging
    console.log('Loading optimizer executed - removed preloaders');
});

// Also run immediately in case DOM is already loaded
if (document.readyState === 'loading') {
    // DOM not ready yet
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM ready - optimizing loading');
    });
} else {
    // DOM already loaded
    console.log('DOM already loaded - running optimizer immediately');
    
    // Remove preloaders immediately
    const loadingElements = document.querySelectorAll('.preloader, .loading, [class*="loading"]');
    loadingElements.forEach(element => {
        if (element) {
            element.style.display = 'none';
        }
    });
    
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
}