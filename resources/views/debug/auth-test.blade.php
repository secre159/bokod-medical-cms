<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication System Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Authentication System Test</h1>
                <p class="text-muted">Test different user authentication scenarios</p>
                
                <div class="row">
                    <!-- Current Authentication Status -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Current Authentication Status</h5>
                            </div>
                            <div class="card-body">
                                @auth
                                    <div class="alert alert-success">
                                        <strong>Logged In:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})
                                        <br><strong>Role:</strong> {{ Auth::user()->role }}
                                        <br><strong>Status:</strong> {{ Auth::user()->status }}
                                        <br><strong>Registration Status:</strong> {{ Auth::user()->registration_status }}
                                        
                                        <div class="mt-3">
                                            <strong>Checks:</strong>
                                            <ul class="mb-0">
                                                <li>Active: {{ Auth::user()->isActive() ? '✅' : '❌' }}</li>
                                                <li>Admin: {{ Auth::user()->isAdmin() ? '✅' : '❌' }}</li>
                                                <li>Patient: {{ Auth::user()->isPatient() ? '✅' : '❌' }}</li>
                                                <li>Registration Approved: {{ Auth::user()->isRegistrationApproved() ? '✅' : '❌' }}</li>
                                                <li>Registration Pending: {{ Auth::user()->isRegistrationPending() ? '✅' : '❌' }}</li>
                                                <li>Registration Rejected: {{ Auth::user()->isRegistrationRejected() ? '✅' : '❌' }}</li>
                                            </ul>
                                        </div>
                                        
                                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <strong>Not Logged In</strong>
                                        <br><a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-2">Go to Login</a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Users Management -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Test Users Management</h5>
                            </div>
                            <div class="card-body">
                                <p>Create test users with different statuses to test authentication:</p>
                                
                                <button onclick="createTestUsers()" class="btn btn-success btn-sm">Create Test Users</button>
                                <button onclick="cleanupTestUsers()" class="btn btn-danger btn-sm">Cleanup Test Users</button>
                                
                                <div id="test-users-result" class="mt-3"></div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Test users will be created with email/password:
                                        <br>• pending@test.com / password123 (Pending approval)
                                        <br>• approved@test.com / password123 (Approved & active)
                                        <br>• rejected@test.com / password123 (Rejected)
                                        <br>• deactivated@test.com / password123 (Approved but deactivated)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Status Checker -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>User Status Checker</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="number" id="user-id-input" class="form-control" placeholder="Enter User ID">
                                    <button onclick="checkUserStatus()" class="btn btn-info">Check Status</button>
                                </div>
                                <div id="user-status-result"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createTestUsers() {
            const resultDiv = document.getElementById('test-users-result');
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Creating test users...';
            
            fetch('/debug/create-test-users', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <strong>Success:</strong> ${data.message}
                            <br>Created ${data.users.length} test users.
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${data.error}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Request Failed:</strong> ${error.message}
                    </div>
                `;
            });
        }
        
        function cleanupTestUsers() {
            const resultDiv = document.getElementById('test-users-result');
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Cleaning up test users...';
            
            fetch('/debug/cleanup-test-users', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-info">
                            <strong>Success:</strong> ${data.message}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${data.error}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Request Failed:</strong> ${error.message}
                    </div>
                `;
            });
        }
        
        function checkUserStatus() {
            const userId = document.getElementById('user-id-input').value;
            const resultDiv = document.getElementById('user-status-result');
            
            if (!userId) {
                resultDiv.innerHTML = '<div class="alert alert-warning">Please enter a user ID.</div>';
                return;
            }
            
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Checking user status...';
            
            fetch(`/debug/user-status/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${data.error}
                        </div>
                    `;
                } else {
                    const canLogin = data.checks.canLogin ? '✅' : '❌';
                    const statusBadge = data.status === 'active' ? 'success' : 'danger';
                    const regStatusBadge = data.registration_status === 'approved' ? 'success' : 
                                         data.registration_status === 'pending' ? 'warning' : 'danger';
                    
                    resultDiv.innerHTML = `
                        <div class="alert alert-info">
                            <h6><strong>${data.name}</strong> (${data.email})</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Basic Info:</strong>
                                    <ul class="mb-0">
                                        <li>Role: <span class="badge bg-secondary">${data.role}</span></li>
                                        <li>Status: <span class="badge bg-${statusBadge}">${data.status}</span></li>
                                        <li>Registration: <span class="badge bg-${regStatusBadge}">${data.registration_status}</span></li>
                                        <li>Can Login: ${canLogin}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>Checks:</strong>
                                    <ul class="mb-0">
                                        <li>Active: ${data.checks.isActive ? '✅' : '❌'}</li>
                                        <li>Admin: ${data.checks.isAdmin ? '✅' : '❌'}</li>
                                        <li>Patient: ${data.checks.isPatient ? '✅' : '❌'}</li>
                                        <li>Reg. Approved: ${data.checks.isRegistrationApproved ? '✅' : '❌'}</li>
                                        <li>Reg. Pending: ${data.checks.isRegistrationPending ? '✅' : '❌'}</li>
                                        <li>Reg. Rejected: ${data.checks.isRegistrationRejected ? '✅' : '❌'}</li>
                                    </ul>
                                </div>
                            </div>
                            ${data.rejection_reason ? `<div class="mt-2"><strong>Rejection Reason:</strong> ${data.rejection_reason}</div>` : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Request Failed:</strong> ${error.message}
                    </div>
                `;
            });
        }
    </script>
</body>
</html>