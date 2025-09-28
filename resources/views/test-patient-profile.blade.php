<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Patient Profile Update</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Patient Profile Update</h4>
                        <small class="text-muted">This form tests the patient profile update flow with the same logic as /my-profile/edit</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Note:</strong> This form bypasses authentication for testing purposes.
                            It uses the same controller logic as the real patient profile update.
                        </div>
                        
                        <form id="patientProfileForm" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Patient User ID:</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="12">User 12 - secre (Patient)</option>
                                </select>
                                <small class="text-muted">Only patient users can update profiles</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="current_picture" class="form-label">Current Profile Picture:</label>
                                <div id="current_picture_display">
                                    <img src="https://i.ibb.co/C5YxMDfz/profile-picture-user-12-1758892208.webp" 
                                         alt="Current profile picture" 
                                         style="max-width: 150px; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">New Profile Picture:</label>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                                <div class="form-text">
                                    Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB
                                </div>
                            </div>
                            
                            <div class="preview-container" id="preview-container" style="display: none;">
                                <label class="form-label">Preview:</label>
                                <img id="preview-image" class="img-thumbnail" style="max-width: 200px;" alt="Preview">
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span id="submit-text">Update Profile Picture</span>
                                <span id="loading" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                            </button>
                        </form>
                        
                        <div id="result" class="mt-4" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview functionality
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('patientProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const userId = document.getElementById('user_id').value;
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Updating...';
            loading.style.display = 'inline-block';
            result.style.display = 'none';
            
            const formData = new FormData(this);
            
            fetch(`/debug/patient-profile-update/${userId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Update Profile Picture';
                loading.style.display = 'none';
                
                // Show result
                result.style.display = 'block';
                
                if (data.success) {
                    result.className = 'alert alert-success';
                    result.innerHTML = `
                        <h6>✅ ${data.message}</h6>
                        <p><strong>Upload Method:</strong> ${data.upload_method}</p>
                        ${data.old_url ? `<p><strong>Old URL:</strong> ${data.old_url}</p>` : ''}
                        <p><strong>New URL:</strong> <a href="${data.new_url}" target="_blank">${data.new_url}</a></p>
                        <details>
                            <summary>Full Response</summary>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </details>
                        
                        <div class="mt-3">
                            <strong>Updated Image:</strong><br>
                            <img src="${data.new_url}" alt="Updated profile picture" style="max-width: 200px; border-radius: 8px;">
                        </div>
                    `;
                } else {
                    result.className = 'alert alert-danger';
                    result.innerHTML = `
                        <h6>❌ Update Failed</h6>
                        <p><strong>Error:</strong> ${data.error}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Update Profile Picture';
                loading.style.display = 'none';
                
                // Show error
                result.style.display = 'block';
                result.className = 'alert alert-danger';
                result.innerHTML = `
                    <h6>❌ Request Failed</h6>
                    <p><strong>Error:</strong> ${error.message}</p>
                `;
            });
        });
    </script>
</body>
</html>