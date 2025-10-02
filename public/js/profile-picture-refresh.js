/**
 * Enhanced Profile Picture Refresh System
 * Handles aggressive cache-busting and real-time profile picture updates
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile Picture Refresh System: Initializing...');
    
    // Check for profile update indicators
    const profileUpdateIndicators = [
        // URL parameters
        new URLSearchParams(window.location.search).has('profile_updated'),
        new URLSearchParams(window.location.search).has('updated'),
        
        // Session storage flags
        sessionStorage.getItem('profilePictureUpdated'),
        sessionStorage.getItem('profileUpdated'),
        
        // Success messages on page
        document.querySelector('.alert-success'),
        document.querySelector('.alert.alert-success'),
        
        // Check if we're on a profile-related page after form submission
        window.location.pathname.includes('profile'),
        document.querySelector('form[action*="profile"]'),
        
        // Check for profile picture upload forms
        document.querySelector('input[name="profile_picture"]')
    ];
    
    const shouldRefreshProfiles = profileUpdateIndicators.some(indicator => indicator);
    
    if (shouldRefreshProfiles) {
        console.log('Profile Picture Refresh: Indicators detected, starting comprehensive refresh');
        setTimeout(forceRefreshAllProfilePictures, 200);
        
        // Also refresh again after a short delay to catch any delayed loads
        setTimeout(forceRefreshAllProfilePictures, 1000);
        
        // Clear session storage flags
        sessionStorage.removeItem('profilePictureUpdated');
        sessionStorage.removeItem('profileUpdated');
    } else {
        console.log('Profile Picture Refresh: No indicators found, performing light refresh');
        setTimeout(lightRefreshProfilePictures, 100);
    }
    
    // Set up profile picture upload handling
    setupProfilePictureUploadHandling();
});

/**
 * Comprehensive profile picture refresh with aggressive cache-busting
 */
function forceRefreshAllProfilePictures(userId = null) {
    console.log('FORCE REFRESH: Starting comprehensive profile picture refresh', { userId });
    
    const selectors = [
        '.user-avatar img',
        '.profile-user-img',
        '.profile-picture-preview img',
        '.avatar-preview img',
        'img[alt*="profile"]',
        'img[alt*="avatar"]',
        '.img-thumbnail',
        '[data-user-id] img',
        'img[src*="ibb.co"]',
        'img[src*="cloudinary.com"]',
        'img[src*="res.cloudinary.com"]'
    ];
    
    let refreshCount = 0;
    
    selectors.forEach(selector => {
        const images = document.querySelectorAll(selector);
        images.forEach(img => {
            if (shouldRefreshImage(img)) {
                refreshProfileImage(img);
                refreshCount++;
            }
        });
    });
    
    console.log(`FORCE REFRESH: Refreshed ${refreshCount} profile images`);
    
    // Also refresh any profile widgets or components
    refreshProfileWidgets();
}

/**
 * Light refresh for normal page loads
 */
function lightRefreshProfilePictures() {
    const profileImages = document.querySelectorAll('.user-avatar img, .profile-user-img');
    let refreshCount = 0;
    
    profileImages.forEach(img => {
        if (shouldRefreshImage(img) && isExternalImage(img.src)) {
            refreshProfileImage(img);
            refreshCount++;
        }
    });
    
    if (refreshCount > 0) {
        console.log(`LIGHT REFRESH: Refreshed ${refreshCount} external profile images`);
    }
}

/**
 * Determine if an image should be refreshed
 */
function shouldRefreshImage(img) {
    return img && 
           img.src && 
           img.src !== '' && 
           !img.src.startsWith('data:image/svg') && // Don't refresh SVG initials
           !img.src.includes('default-avatar.svg'); // Don't refresh default avatars
}

/**
 * Check if image is from external service (Cloudinary, ImgBB, etc.)
 */
function isExternalImage(src) {
    return src.includes('ibb.co') || 
           src.includes('cloudinary.com') || 
           src.includes('res.cloudinary.com') ||
           src.includes('imgur.com') ||
           src.startsWith('http');
}

/**
 * Refresh a single profile image with comprehensive cache-busting
 */
function refreshProfileImage(img) {
    const originalSrc = img.src;
    
    // Remove all existing cache-busting parameters
    let baseSrc = originalSrc.split(/[?&](t|v|r|bust|cache|refresh|_)=/)[0];
    
    // Create comprehensive cache-busting parameters
    const timestamp = Date.now();
    const random = Math.floor(Math.random() * 1000000);
    const microtime = performance.now();
    const dateString = new Date().toISOString().replace(/[:.]/g, '');
    
    const cacheBustParams = [
        `t=${timestamp}`,
        `v=${random}`,
        `r=${Math.floor(Math.random() * 10000)}`,
        `bust=${microtime}`,
        `refresh=${dateString}`,
        `_=${timestamp}`
    ].join('&');
    
    const separator = baseSrc.includes('?') ? '&' : '?';
    const newSrc = baseSrc + separator + cacheBustParams;
    
    console.log('REFRESH IMAGE:', {
        element: img,
        originalSrc,
        newSrc,
        classList: img.className
    });
    
    // Visual feedback during refresh
    img.style.opacity = '0.7';
    img.style.transition = 'opacity 0.3s ease';
    
    // Update the source
    img.src = newSrc;
    
    // Handle successful load
    img.onload = function() {
        this.style.opacity = '1';
        console.log('REFRESH SUCCESS:', { src: this.src });
    };
    
    // Handle load errors
    img.onerror = function() {
        console.error('REFRESH ERROR:', { src: this.src });
        this.style.opacity = '1';
        
        // Try without cache-busting as fallback
        if (this.src !== originalSrc) {
            console.log('FALLBACK: Trying original source');
            this.src = originalSrc;
        }
    };
}

/**
 * Refresh profile widgets and components
 */
function refreshProfileWidgets() {
    // Refresh AdminLTE profile widgets
    const profileWidgets = document.querySelectorAll('.widget-user-image img, .user-panel img');
    profileWidgets.forEach(img => {
        if (shouldRefreshImage(img)) {
            refreshProfileImage(img);
        }
    });
    
    // Refresh dropdown profile images
    const dropdownImages = document.querySelectorAll('.navbar-nav img, .dropdown-menu img');
    dropdownImages.forEach(img => {
        if (shouldRefreshImage(img)) {
            refreshProfileImage(img);
        }
    });
}

/**
 * Set up profile picture upload handling
 */
function setupProfilePictureUploadHandling() {
    // Monitor forms that contain profile picture uploads
    const profileForms = document.querySelectorAll('form:has(input[name="profile_picture"])');
    
    profileForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const profileInput = form.querySelector('input[name="profile_picture"]');
            if (profileInput && profileInput.files && profileInput.files.length > 0) {
                console.log('UPLOAD: Profile picture form submitted, setting refresh flag');
                sessionStorage.setItem('profilePictureUpdated', 'true');
                sessionStorage.setItem('profileUploadTimestamp', Date.now().toString());
            }
        });
    });
    
    // Also monitor for any AJAX-based profile updates
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const url = args[0];
        const options = args[1] || {};
        
        // Check if this looks like a profile update request
        if ((typeof url === 'string' && (url.includes('profile') || url.includes('avatar'))) ||
            (options.body && options.body.has && options.body.has('profile_picture'))) {
            console.log('AJAX: Profile-related request detected, setting refresh flag');
            sessionStorage.setItem('profilePictureUpdated', 'true');
        }
        
        return originalFetch.apply(this, args).then(response => {
            // If it was a successful profile update, refresh images
            if (response.ok && typeof url === 'string' && url.includes('profile')) {
                setTimeout(() => {
                    console.log('AJAX SUCCESS: Refreshing profile pictures');
                    forceRefreshAllProfilePictures();
                }, 500);
            }
            return response;
        });
    };
}

/**
 * Manual refresh function that can be called from anywhere
 */
window.refreshProfilePictures = function(userId = null) {
    console.log('MANUAL REFRESH: Triggered by external call');
    forceRefreshAllProfilePictures(userId);
};

/**
 * Handle page visibility changes (user returns to tab)
 */
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        const lastUpload = sessionStorage.getItem('profileUploadTimestamp');
        if (lastUpload && Date.now() - parseInt(lastUpload) < 300000) { // 5 minutes
            console.log('PAGE VISIBLE: Recent profile upload detected, refreshing');
            setTimeout(forceRefreshAllProfilePictures, 100);
        }
    }
});

/**
 * Handle browser back/forward navigation
 */
window.addEventListener('pageshow', function(event) {
    if (event.persisted || performance.navigation.type === 2) {
        console.log('PAGE SHOW: Page loaded from cache, refreshing profiles');
        setTimeout(lightRefreshProfilePictures, 100);
    }
});

console.log('Profile Picture Refresh System: Loaded and ready');