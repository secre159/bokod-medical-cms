/**
 * Profile Picture Preview and Upload Enhancement
 * Provides instant preview and better user experience for profile picture uploads
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle profile picture preview for all profile picture inputs
    const profilePictureInputs = document.querySelectorAll('input[name="profile_picture"]');
    
    profilePictureInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            handleProfilePicturePreview(e.target);
        });
    });
    
    // Handle form submissions with profile pictures
    const formsWithProfilePicture = document.querySelectorAll('form:has(input[name="profile_picture"])');
    
    formsWithProfilePicture.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const profilePictureInput = form.querySelector('input[name="profile_picture"]');
            if (profilePictureInput && profilePictureInput.files.length > 0) {
                showUploadingState(form);
            }
        });
    });
});

function handleProfilePicturePreview(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file before preview
        if (!validateImageFile(file, input)) {
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            updatePreviewImage(input, e.target.result);
            showPreviewFeedback(input, true);
        };
        
        reader.onerror = function() {
            showPreviewFeedback(input, false, 'Error reading file');
        };
        
        reader.readAsDataURL(file);
    }
}

function validateImageFile(file, input) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    // Check file size
    if (file.size > maxSize) {
        showValidationError(input, 'File size too large. Maximum allowed size is 5MB.');
        return false;
    }
    
    // Check file type
    if (!allowedTypes.includes(file.type)) {
        showValidationError(input, 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        return false;
    }
    
    // Clear any previous errors
    clearValidationError(input);
    return true;
}

function updatePreviewImage(input, imageSrc) {
    // Find preview elements near the input
    const form = input.closest('form');
    
    // Look for various preview elements
    const previewSelectors = [
        '#profile-picture-preview img',
        '#avatar-preview img', 
        '.profile-picture-preview img',
        '.avatar-preview img',
        '.user-avatar img',
        '.img-thumbnail'
    ];
    
    let previewUpdated = false;
    
    previewSelectors.forEach(selector => {
        const previewElements = form.querySelectorAll(selector);
        previewElements.forEach(preview => {
            if (preview && preview.tagName === 'IMG') {
                // Store original URL for potential restoration
                if (!preview.hasAttribute('data-original-src')) {
                    preview.setAttribute('data-original-src', preview.src);
                }
                
                preview.src = imageSrc;
                preview.style.display = 'block';
                
                // Add a preview indicator class
                preview.classList.add('preview-mode');
                
                // Add a subtle animation
                preview.style.opacity = '0';
                setTimeout(() => {
                    preview.style.transition = 'opacity 0.3s ease';
                    preview.style.opacity = '1';
                }, 50);
                
                previewUpdated = true;
            }
        });
    });
    
    // If no existing preview found, create one
    if (!previewUpdated) {
        createNewPreview(input, imageSrc);
    }
}

function createNewPreview(input, imageSrc) {
    const container = input.parentElement;
    
    // Create preview container if it doesn't exist
    let previewContainer = container.querySelector('.profile-picture-preview');
    if (!previewContainer) {
        previewContainer = document.createElement('div');
        previewContainer.className = 'profile-picture-preview mb-3 text-center';
        container.insertBefore(previewContainer, input);
    }
    
    // Create or update preview image
    let previewImg = previewContainer.querySelector('img');
    if (!previewImg) {
        previewImg = document.createElement('img');
        previewImg.className = 'img-thumbnail';
        previewImg.style.maxWidth = '150px';
        previewImg.style.maxHeight = '150px';
        previewImg.style.objectFit = 'cover';
        previewContainer.appendChild(previewImg);
    }
    
    previewImg.src = imageSrc;
    previewImg.style.opacity = '0';
    
    setTimeout(() => {
        previewImg.style.transition = 'opacity 0.3s ease';
        previewImg.style.opacity = '1';
    }, 50);
}

function showPreviewFeedback(input, success, message = '') {
    const container = input.parentElement;
    
    // Remove existing feedback
    const existingFeedback = container.querySelector('.preview-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    if (message) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = `preview-feedback small mt-1 ${success ? 'text-success' : 'text-danger'}`;
        feedbackDiv.innerHTML = `<i class="fas ${success ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-1"></i>${message}`;
        
        container.appendChild(feedbackDiv);
        
        // Auto-remove success messages after 3 seconds
        if (success) {
            setTimeout(() => {
                if (feedbackDiv.parentElement) {
                    feedbackDiv.remove();
                }
            }, 3000);
        }
    } else if (success) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'preview-feedback small text-success mt-1';
        feedbackDiv.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Image preview loaded successfully';
        
        container.appendChild(feedbackDiv);
        
        setTimeout(() => {
            if (feedbackDiv.parentElement) {
                feedbackDiv.remove();
            }
        }, 3000);
    }
}

function showValidationError(input, message) {
    // Clear the input
    input.value = '';
    
    // Show error message
    showPreviewFeedback(input, false, message);
    
    // Add error styling to input
    input.classList.add('is-invalid');
    
    // Remove error styling after user selects a new file
    const removeError = function() {
        input.classList.remove('is-invalid');
        input.removeEventListener('change', removeError);
    };
    input.addEventListener('change', removeError);
}

function clearValidationError(input) {
    input.classList.remove('is-invalid');
    
    const container = input.parentElement;
    const errorFeedback = container.querySelector('.preview-feedback.text-danger');
    if (errorFeedback) {
        errorFeedback.remove();
    }
}

function showUploadingState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    
    if (submitButton) {
        const originalText = submitButton.textContent || submitButton.value;
        const loadingText = 'Uploading...';
        
        // Disable button and show loading state
        submitButton.disabled = true;
        
        if (submitButton.tagName === 'BUTTON') {
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + loadingText;
        } else {
            submitButton.value = loadingText;
        }
        
        // Store original text for restoration if needed
        submitButton.setAttribute('data-original-text', originalText);
    }
    
    // Show uploading feedback
    const profilePictureInput = form.querySelector('input[name="profile_picture"]');
    if (profilePictureInput) {
        showPreviewFeedback(profilePictureInput, true, 'Uploading image to ImgBB CDN...');
    }
}

// Utility function to restore button state (in case of form validation errors)
function restoreButtonState(form) {
    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
    
    if (submitButton) {
        const originalText = submitButton.getAttribute('data-original-text');
        
        submitButton.disabled = false;
        
        if (originalText) {
            if (submitButton.tagName === 'BUTTON') {
                submitButton.textContent = originalText;
            } else {
                submitButton.value = originalText;
            }
        }
    }
}

// Handle successful uploads (can be called from server response)
function handleUploadSuccess(imageUrl, userId) {
    // Update all avatar images for this user
    const avatarImages = document.querySelectorAll(`.user-avatar img, .profile-user-img, [data-user-id="${userId}"] img`);
    
    avatarImages.forEach(img => {
        // Add cache-busting parameter to force reload
        const newUrl = imageUrl + '?t=' + Date.now();
        img.src = newUrl;
        
        // Remove preview mode and original src tracking
        img.classList.remove('preview-mode');
        img.removeAttribute('data-original-src');
        
        // Add subtle fade effect
        img.style.opacity = '0.7';
        setTimeout(() => {
            img.style.transition = 'opacity 0.3s ease';
            img.style.opacity = '1';
        }, 100);
    });
    
    // Hide any initials-based avatars and show image avatars
    const initialAvatars = document.querySelectorAll(`.user-avatar:not(:has(img))`);
    initialAvatars.forEach(avatar => {
        avatar.style.display = 'none';
        
        // Create new image element
        const newImg = document.createElement('img');
        newImg.className = 'user-avatar';
        newImg.src = imageUrl + '?t=' + Date.now();
        newImg.alt = 'Profile Picture';
        newImg.style.width = avatar.style.width || '40px';
        newImg.style.height = avatar.style.height || '40px';
        newImg.style.borderRadius = '50%';
        newImg.style.objectFit = 'cover';
        
        avatar.parentElement.insertBefore(newImg, avatar);
    });
}

// Handle page load with cache-busting for recently updated profile pictures
function refreshProfilePicturesOnLoad() {
    // Check if there's a success message indicating a profile update
    const successMessages = document.querySelectorAll('.alert-success, .alert.alert-success');
    let profileUpdated = false;
    
    successMessages.forEach(message => {
        if (message.textContent.includes('profile') && message.textContent.includes('updated')) {
            profileUpdated = true;
            console.log('PROFILE REFRESH: Success message detected - Profile was updated');
        }
    });
    
    if (profileUpdated) {
        console.log('PROFILE REFRESH: Refreshing all profile pictures with cache-busting');
        // Add cache-busting to all profile pictures on the page
        const profilePictures = document.querySelectorAll('.user-avatar img, .profile-user-img');
        
        profilePictures.forEach(img => {
            const oldSrc = img.src;
            if (img.src && !img.src.includes('?t=')) {
                const separator = img.src.includes('?') ? '&' : '?';
                img.src = img.src + separator + 't=' + Date.now();
                console.log('PROFILE REFRESH: Updated image', { oldSrc, newSrc: img.src });
            }
        });
        
        // Force refresh even images that already have cache-busting parameters
        setTimeout(() => {
            profilePictures.forEach(img => {
                if (img.src.includes('?t=') || img.src.includes('&t=')) {
                    // Remove old cache-buster and add new one
                    const baseUrl = img.src.split(/[?&]t=/)[0];
                    const separator = baseUrl.includes('?') ? '&' : '?';
                    const newUrl = baseUrl + separator + 't=' + Date.now();
                    console.log('PROFILE REFRESH: Force refreshing cached image', { oldUrl: img.src, newUrl });
                    img.src = newUrl;
                }
            });
        }, 500);
    }
}

// Restore original image if upload fails or form validation fails
function restoreOriginalImages() {
    const previewImages = document.querySelectorAll('.user-avatar img.preview-mode');
    
    previewImages.forEach(img => {
        const originalSrc = img.getAttribute('data-original-src');
        if (originalSrc) {
            img.src = originalSrc;
            img.classList.remove('preview-mode');
            img.removeAttribute('data-original-src');
        }
    });
}

// Enhanced profile picture refresh specifically for post-form-submission scenarios
function forceRefreshAllProfilePictures(userId = null) {
    console.log('FORCE REFRESH: Starting comprehensive profile picture refresh', { userId });
    
    // Select all possible profile picture elements
    const selectors = [
        '.user-avatar img',
        '.profile-user-img',
        '.img-thumbnail[alt*="profile"]',
        '.profile-picture-preview img',
        '.avatar-preview img',
        '[data-user-id] img',
        'img[src*="ibb.co"]',
        'img[src*="profile"]'
    ];
    
    selectors.forEach(selector => {
        const images = document.querySelectorAll(selector);
        images.forEach(img => {
            if (img.src && (img.src.includes('ibb.co') || img.src.includes('profile'))) {
                const originalSrc = img.src;
                
                // Create a completely fresh URL with multiple cache-busting parameters
                let baseUrl = originalSrc.split(/[?&](t|v|_|cache|bust)=/)[0];
                const timestamp = Date.now();
                const randomNum = Math.floor(Math.random() * 10000);
                
                const cacheBustParams = `t=${timestamp}&v=${randomNum}&refresh=${Date.now()}`;
                const separator = baseUrl.includes('?') ? '&' : '?';
                const newSrc = baseUrl + separator + cacheBustParams;
                
                console.log('FORCE REFRESH: Updating image', {
                    element: img,
                    originalSrc,
                    newSrc,
                    selector
                });
                
                // Set new src and add visual feedback
                img.style.opacity = '0.7';
                img.src = newSrc;
                
                // Restore opacity once image loads
                img.onload = function() {
                    this.style.opacity = '1';
                    this.style.transition = 'opacity 0.3s ease';
                    console.log('FORCE REFRESH: Image loaded successfully', { src: this.src });
                };
                
                // Handle load errors
                img.onerror = function() {
                    console.error('FORCE REFRESH: Image failed to load', { src: this.src });
                    this.style.opacity = '1';
                };
            }
        });
    });
    
    console.log('FORCE REFRESH: Completed profile picture refresh');
}

// Call refresh function on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(refreshProfilePicturesOnLoad, 100);
    
    // Also do a comprehensive refresh if we detect any profile update indicators
    const urlParams = new URLSearchParams(window.location.search);
    const hasProfileUpdateFlag = sessionStorage.getItem('profilePictureUpdated');
    const hasSuccessMessage = document.querySelector('.alert-success');
    
    if (hasProfileUpdateFlag || hasSuccessMessage || urlParams.has('profile_updated')) {
        console.log('PROFILE LOAD: Detected profile update indicators, forcing comprehensive refresh');
        setTimeout(() => {
            forceRefreshAllProfilePictures();
            // Clear the session storage flag
            sessionStorage.removeItem('profilePictureUpdated');
        }, 200);
    }
});

// Handle browser back/forward buttons
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        // Page was loaded from cache, refresh profile pictures
        refreshProfilePicturesOnLoad();
    }
});
