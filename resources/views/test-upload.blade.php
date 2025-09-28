<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Picture Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .preview-container {
            max-width: 300px;
            margin: 20px 0;
        }
        .preview-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .upload-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Profile Picture Upload Test</h4>
                        <small class="text-muted">Test ImgBB integration with fallback to local storage</small>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select User:</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Choose a user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture:</label>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                                <div class="form-text">
                                    Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB
                                </div>
                            </div>
                            
                            <div class="preview-container" id="preview-container" style="display: none;">
                                <label class="form-label">Preview:</label>
                                <img id="preview-image" class="preview-image" alt="Preview">
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span id="submit-text">Upload Profile Picture</span>
                                <span id="loading" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                            </button>
                        </form>
                        
                        <div id="result" class="upload-result" style="display: none;"></div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Test Links</h5>
                    </div>
                    <div class="card-body">
                        <a href="/test-imgbb-config" class="btn btn-outline-info me-2" target="_blank">Check ImgBB Config</a>
                        <a href="/debug/profile-picture" class="btn btn-outline-secondary" target="_blank">View Profile Picture Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Uploading...';
            loading.style.display = 'inline-block';
            result.style.display = 'none';
            
            const formData = new FormData(this);
            
            fetch('/test-upload-submit', {
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
                submitText.textContent = 'Upload Profile Picture';
                loading.style.display = 'none';
                
                // Show result
                result.style.display = 'block';
                
                if (data.success) {
                    result.className = 'upload-result success';
                    result.innerHTML = `
                        <h6>✅ ${data.message}</h6>
                        ${data.fallback_used ? '<p><strong>Note:</strong> Used local storage fallback</p>' : ''}
                        ${data.imgbb_result ? `
                            <p><strong>ImgBB URL:</strong> <a href="${data.imgbb_result.url}" target="_blank">${data.imgbb_result.url}</a></p>
                            <p><strong>Thumbnail:</strong> <a href="${data.imgbb_result.thumb_url}" target="_blank">${data.imgbb_result.thumb_url}</a></p>
                        ` : ''}
                        ${data.local_url ? `<p><strong>Local URL:</strong> <a href="${data.local_url}" target="_blank">${data.local_url}</a></p>` : ''}
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                } else {
                    result.className = 'upload-result error';
                    result.innerHTML = `
                        <h6>❌ Upload Failed</h6>
                        <p><strong>Error:</strong> ${data.error}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                }
            })
            .catch(error => {
                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Upload Profile Picture';
                loading.style.display = 'none';
                
                // Show error
                result.style.display = 'block';
                result.className = 'upload-result error';
                result.innerHTML = `
                    <h6>❌ Request Failed</h6>
                    <p><strong>Error:</strong> ${error.message}</p>
                `;
            });
        });
    </script>
</body>
</html>