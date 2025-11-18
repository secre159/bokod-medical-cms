@extends('adminlte::page')

@section('title', 'User Management | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i> {{ $errors->first('error') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active_users'] ?? 0 }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['admin_users'] ?? 0 }}</h3>
                    <p>Admin Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['patient_users'] ?? 0 }}</h3>
                    <p>Patient Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card {{ (request('role') || request('status') || request('search')) ? '' : 'collapsed-card' }}" id="filtersCard">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Search & Filter</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-card-widget="collapse" id="filterToggle" title="Toggle Filters">
                    <i class="fas {{ (request('role') || request('status') || request('search')) ? 'fa-minus' : 'fa-plus' }}" id="toggleIcon"></i>
                    <span class="ml-1" id="toggleText">{{ (request('role') || request('status') || request('search')) ? 'Hide' : 'Show' }}</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="patient" {{ request('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by name or email..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Users Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>Users List
            </h3>
            <div class="card-tools">
                <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>Add New User
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if($users->count() > 0)
                <table class="table table-bordered table-striped table-sm" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar mr-2">
                                        <x-user-avatar :user="$user" size="thumbnail" width="32px" height="32px" class="img-circle elevation-1" />
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->role === 'patient' && $user->patient && $user->patient->position)
                                            <br><small class="text-muted">{{ $user->patient->position }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-masked-email :email="$user->email" />
                                @if($user->phone)
                                    <br><small class="text-muted">
                                        <i class="fas fa-phone mr-1"></i>{{ $user->phone }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'success' }}">
                                    <i class="fas fa-{{ $user->role == 'admin' ? 'shield-alt' : 'user' }} mr-1"></i>
                                    {{ ucfirst($user->role) }}
                                    @if($user->role === 'patient')
                                        <i class="fas fa-comments ml-1" title="Can chat with this user"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times-circle mr-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                    <br><small class="text-muted">{{ $user->last_login_at->format('M d, Y g:i A') }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('users.show', $user) }}">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                        
                                        @if($user->role === 'patient')
                                            <a class="dropdown-item" href="#" onclick="startChatWithUser({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-comments mr-2"></i>Start Chat
                                            </a>
                                            <div class="dropdown-divider"></div>
                                        @endif
                                        
                                        @if($user->id != auth()->id())
                                            <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                                <i class="fas fa-edit mr-2"></i>Edit User
                                            </a>
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            @if($user->status == 'active')
                                                <a class="dropdown-item text-warning" href="#" 
                                                   onclick="changeUserStatus({{ $user->id }}, 'inactive')">
                                                    <i class="fas fa-user-slash mr-2"></i>Deactivate
                                                </a>
                                            @else
                                                <a class="dropdown-item text-success" href="#" 
                                                   onclick="changeUserStatus({{ $user->id }}, 'active')">
                                                    <i class="fas fa-user-check mr-2"></i>Activate
                                                </a>
                                            @endif
                                            
                                            
                                            <a class="dropdown-item text-primary" href="#" 
                                               onclick="resetPassword({{ $user->id }})">
                                                <i class="fas fa-key mr-2"></i>Reset Password
                                            </a>
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                    <i class="fas fa-trash mr-2"></i>Delete User
                                                </button>
                                            </form>
                                        @else
                                            <span class="dropdown-item-text text-muted">
                                                <i class="fas fa-info-circle mr-2"></i>Cannot modify own account
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No users found</h5>
                    <p class="text-muted">Try adjusting your search criteria or add a new user.</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add First User
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Change User Status</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to <span id="statusAction"></span> this user?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        This will <span id="statusEffect"></span> the user's access to the system.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusChange">
                        Confirm Change
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reset User Password</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        A password reset link will be sent to the user's email address.
                    </div>
                    <p>Are you sure you want to send a password reset link to this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPasswordReset">
                        Send Reset Link
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Start Chat Modal -->
    <div class="modal fade" id="startChatModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-comments mr-2"></i>Start Chat with <span id="chatUserName"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="initialMessage">Initial Message (Optional)</label>
                        <textarea class="form-control" id="initialMessage" rows="4" 
                                  placeholder="Enter your first message to start the conversation..."
                                  maxlength="1000"></textarea>
                        <small class="form-text text-muted">
                            You can start chatting immediately or leave this blank to begin with a simple greeting.
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        This will start a new conversation with the patient. They will receive a notification about your message.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStartChat">
                        <i class="fas fa-paper-plane mr-2"></i>Start Conversation
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<style>
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }
    .table td { white-space: nowrap; }

    /* Sticky table header */
    #usersTable thead th { position: sticky; top: 0; z-index: 3; background: #f8f9fa; }

    /* Column resize grips (colResizable) */
    .JCLRgrips { height: 0; position: relative; }
    .JCLRgrip { position: absolute; z-index: 5; }
    .JCLRgrip .JColResizer { position: absolute; background: transparent; width: 8px; margin-left: -4px; cursor: col-resize; height: 100vh; top: 0; }
    .dragging .JColResizer { border-left: 2px dashed #007bff; }
    
    /* Ensure dropdown shows above sticky header and outside table wrapper */
    .dropdown-menu {
        min-width: 180px;
        z-index: 2000; /* above sticky thead and card content */
    }

    /* Allow overflow so dropdown is clickable inside responsive table */
    .card-body.table-responsive {
        overflow: visible !important;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .user-avatar {
        display: inline-block;
    }
    
    .avatar-placeholder {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    /* Collapsed card functionality */
    .card.collapsed-card .card-body {
        display: none;
    }
    
    /* Filter toggle button styling */
    #filterToggle {
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    #filterToggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    #filtersCard {
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    /* Chat functionality styling */
    .dropdown-item .fas.fa-comments {
        color: #28a745;
    }
    
    #startChatModal .modal-dialog {
        max-width: 550px;
    }
    
    #initialMessage {
        resize: vertical;
        min-height: 100px;
    }
    
    #confirmStartChat:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Toast notification positioning */
    .swal2-toast {
        font-size: 14px;
    }
    
    /* Patient badge chat icon */
    .badge .fa-comments {
        opacity: 0.8;
        font-size: 0.8em;
    }
    
    .badge:hover .fa-comments {
        opacity: 1;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/colresizable/colResizable-1.6.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[title]').tooltip();
    
    // Auto-submit form on filter change
    $('#role, #status').change(function() {
        $('#filterForm').submit();
    });

    // DataTable for sorting only (pagination handled by Laravel below)
    @if($users->count() > 0)
    $('#usersTable').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[ 0, "desc" ]], // Sort by ID
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting on actions column
        ]
    });

    // Enable drag-to-resize columns (header + body stay in sync)
    $('#usersTable').colResizable({
        liveDrag: true,
        resizeMode: 'overflow',
        draggingClass: 'dragging',
        minWidth: 60,
    });
    @endif
    
    // Handle filter card toggle behavior
    const hasActiveFilters = {{ (request('role') || request('status') || request('search')) ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        console.log('Active filters detected, keeping card expanded');
        $('#filtersCard').removeClass('collapsed-card');
        $('#toggleIcon').removeClass('fa-plus').addClass('fa-minus');
        $('#toggleText').text('Hide');
    }
    
    // Manual filter card toggle handler
    $('#filterToggle').on('click', function() {
        console.log('Filter toggle clicked');
        const card = $('#filtersCard');
        const icon = $('#toggleIcon');
        const text = $('#toggleText');
        
        setTimeout(function() {
            if (card.hasClass('collapsed-card')) {
                icon.removeClass('fa-minus').addClass('fa-plus');
                text.text('Show');
                console.log('Card collapsed');
            } else {
                icon.removeClass('fa-plus').addClass('fa-minus');
                text.text('Hide');
                console.log('Card expanded');
            }
        }, 100);
    });
});

let currentUserId = null;
let currentStatus = null;

function changeUserStatus(userId, status) {
    currentUserId = userId;
    currentStatus = status;
    
    const action = status === 'active' ? 'activate' : 'deactivate';
    const effect = status === 'active' ? 'restore' : 'remove';
    
    $('#statusAction').text(action);
    $('#statusEffect').text(effect);
    $('#statusModal').modal('show');
}

$('#confirmStatusChange').click(function() {
    if (currentUserId && currentStatus) {
        // AJAX call to change status
        $.ajax({
            url: `/users/${currentUserId}/status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: currentStatus
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    modalError('Error changing user status: ' + response.message, 'Status Change Failed');
                }
            },
            error: function() {
                modalError('Error changing user status. Please try again.', 'Network Error');
            }
        });
    }
    
    $('#statusModal').modal('hide');
});

function resetPassword(userId) {
    currentUserId = userId;
    $('#resetPasswordModal').modal('show');
}

$('#confirmPasswordReset').click(function() {
    if (currentUserId) {
        // AJAX call to reset password
        $.ajax({
            url: `/users/${currentUserId}/reset-password`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    modalSuccess('Password reset link sent successfully!', 'Reset Link Sent');
                } else {
                    modalError('Error sending reset link: ' + response.message, 'Reset Failed');
                }
            },
            error: function() {
                modalError('Error sending reset link. Please try again.', 'Network Error');
            }
        });
    }
    
    $('#resetPasswordModal').modal('hide');
});

// Chat functionality
let currentPatientId = null;
let currentPatientName = null;

function startChatWithUser(userId, userName) {
    currentPatientId = userId;
    currentPatientName = userName;
    
    $('#chatUserName').text(userName);
    $('#initialMessage').val(''); // Clear previous message
    $('#startChatModal').modal('show');
}

$('#confirmStartChat').click(function() {
    if (currentPatientId) {
        const initialMessage = $('#initialMessage').val().trim();
        
        // Show loading state
        const button = $(this);
        const originalText = button.html();
        button.html('<i class="fas fa-spinner fa-spin mr-2"></i>Starting...').prop('disabled', true);
        
        // AJAX call to start conversation
        $.ajax({
            url: '{{ route("admin.messages.startWithPatient") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                patient_id: currentPatientId,
                initial_message: initialMessage
            },
            success: function(response) {
                if (response.success) {
                    $('#startChatModal').modal('hide');
                    
                    // Show success message
                    showSuccessToast('Conversation started successfully with ' + currentPatientName);
                    
                    // Redirect to messages page with the conversation
                    window.location.href = response.redirect_url;
                } else {
                    showErrorToast('Error starting conversation: ' + response.error);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error starting conversation. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showErrorToast(errorMessage);
            },
            complete: function() {
                // Reset button state
                button.html(originalText).prop('disabled', false);
            }
        });
    }
});

// Helper functions for toast notifications
function showSuccessToast(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Fallback to alert if SweetAlert2 not available
        alert(message);
    }
}

function showErrorToast(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            timer: 5000,
            showConfirmButton: true,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Fallback to alert if SweetAlert2 not available
        alert('Error: ' + message);
    }
}

// Clear modal data when hidden
$('#startChatModal').on('hidden.bs.modal', function () {
    currentPatientId = null;
    currentPatientName = null;
    $('#initialMessage').val('');
});

</script>
@endsection
