@extends('adminlte::page')

@section('title', 'User Management - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-users-cog"></i> User Management
            <small class="text-muted">Complete step-by-step user management guide</small>
        </h1>
        <a href="{{ route('admin.documentation.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Documentation
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Table of Contents -->
        <div class="sticky-toc">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Table of Contents</h3>
                </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#user-roles">Understanding User Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#create-users">Creating New Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#manage-users">Managing Existing Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#registration-approval">Registration Approvals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#password-management">Password Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#user-security">User Security</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- User Roles Section -->
        <div class="card" id="user-roles">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-tag"></i> Understanding User Roles</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Important:</strong> 
                    Your CMS has two main user roles with different permissions and access levels.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5><i class="fas fa-user-shield"></i> Administrator</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Full System Access:</strong></p>
                                <ul>
                                    <li>Manage all patients and appointments</li>
                                    <li>Access medicine inventory and prescriptions</li>
                                    <li>View all reports and analytics</li>
                                    <li>Manage system settings</li>
                                    <li>Create and manage other users</li>
                                    <li>Approve patient registrations</li>
                                </ul>
                                <div class="alert alert-warning">
                                    <small><i class="fas fa-exclamation-triangle"></i> Use admin privileges responsibly!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5><i class="fas fa-user"></i> Patient</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Limited Personal Access:</strong></p>
                                <ul>
                                    <li>View and edit personal profile</li>
                                    <li>Schedule appointments</li>
                                    <li>View medical history</li>
                                    <li>View prescriptions</li>
                                    <li>Send messages to admins</li>
                                    <li>Update contact information</li>
                                </ul>
                                <div class="alert alert-info">
                                    <small><i class="fas fa-info-circle"></i> Patients can only access their own data.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creating New Users Section -->
        <div class="card" id="create-users">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> Creating New Users - Step by Step</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Creating an Administrator User</h5>
                <div class="card border-primary mb-4">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Users:</strong> Click "Users" in the left sidebar</li>
                            <li><strong>Click "Add User" button</strong> (usually green button in top-right)</li>
                            <li><strong>Fill in Personal Information:</strong>
                                <ul>
                                    <li>First Name and Last Name</li>
                                    <li>Email address (must be unique)</li>
                                    <li>Phone number</li>
                                </ul>
                            </li>
                            <li><strong>Set Login Credentials:</strong>
                                <ul>
                                    <li>Username (must be unique)</li>
                                    <li>Temporary password (user will change on first login)</li>
                                </ul>
                            </li>
                            <li><strong>Select Role:</strong> Choose "Administrator" from dropdown</li>
                            <li><strong>Set Status:</strong> Choose "Active" to allow immediate login</li>
                            <li><strong>Save:</strong> Click "Create User" button</li>
                            <li><strong>Verify:</strong> Check that the user appears in the users list</li>
                        </ol>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong> 
                            The new admin user can now log in and access all system features.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Creating a Patient User</h5>
                <div class="card border-success mb-4">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            Patient users are usually created through the "Patients" section, not the "Users" section.
                        </div>
                        <ol>
                            <li><strong>Navigate to Patients:</strong> Click "Patients" → "Add Patient"</li>
                            <li><strong>Fill Patient Details:</strong>
                                <ul>
                                    <li>Personal information (name, date of birth, etc.)</li>
                                    <li>Contact details (address, phone, email)</li>
                                    <li>Emergency contact information</li>
                                    <li>Medical information (if available)</li>
                                </ul>
                            </li>
                            <li><strong>Create Login Account:</strong>
                                <ul>
                                    <li>Check the "Create user account" checkbox</li>
                                    <li>Enter a username for the patient</li>
                                    <li>System will auto-generate a temporary password</li>
                                </ul>
                            </li>
                            <li><strong>Save:</strong> Click "Create Patient" button</li>
                            <li><strong>Patient receives:</strong> Email with login credentials (if email configured)</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managing Existing Users Section -->
        <div class="card" id="manage-users">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users-cog"></i> Managing Existing Users</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-edit text-warning"></i> How to Edit User Information</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Users List:</strong> Click "Users" in sidebar</li>
                            <li><strong>Find the User:</strong> Use search or browse the list</li>
                            <li><strong>Click "Edit" button</strong> (pencil icon) next to the user</li>
                            <li><strong>Make Changes:</strong> Update any information as needed</li>
                            <li><strong>Save Changes:</strong> Click "Update User" button</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-user-slash text-danger"></i> How to Deactivate a User</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the User:</strong> Go to Users list and locate the user</li>
                            <li><strong>Click "Status" button</strong> or "Edit" the user</li>
                            <li><strong>Change Status:</strong> Select "Inactive" from dropdown</li>
                            <li><strong>Confirm:</strong> Click "Update Status" or "Save"</li>
                        </ol>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> 
                            Inactive users cannot log in but their data is preserved.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-user-check text-success"></i> How to Reactivate a User</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the User:</strong> Look for users with "Inactive" status</li>
                            <li><strong>Edit the User:</strong> Click the "Edit" button</li>
                            <li><strong>Change Status:</strong> Select "Active" from dropdown</li>
                            <li><strong>Save:</strong> Click "Update User" button</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Approval Section -->
        <div class="card" id="registration-approval">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-check"></i> Patient Registration Approvals</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About Registration Approvals:</strong> 
                    When patients register themselves online, their accounts need admin approval before they can log in.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> How to Approve Patient Registrations</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Check for Pending Approvals:</strong>
                                <ul>
                                    <li>Dashboard shows pending registration count</li>
                                    <li>Or click "Registration Approvals" in sidebar</li>
                                </ul>
                            </li>
                            <li><strong>Review Registration Details:</strong>
                                <ul>
                                    <li>Click "View" to see full patient information</li>
                                    <li>Verify the information looks legitimate</li>
                                    <li>Check for complete contact details</li>
                                </ul>
                            </li>
                            <li><strong>Make Decision:</strong>
                                <ul>
                                    <li><strong>To Approve:</strong> Click "Approve" button</li>
                                    <li><strong>To Reject:</strong> Click "Reject" and provide reason</li>
                                </ul>
                            </li>
                            <li><strong>Confirmation:</strong> System will send email notification to patient</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-list-check text-info"></i> Bulk Approval Process</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Registration Approvals</strong></li>
                            <li><strong>Select Multiple Users:</strong> Check boxes next to registrations</li>
                            <li><strong>Choose Action:</strong> Click "Bulk Approve" button</li>
                            <li><strong>Confirm:</strong> Click "Yes" to approve all selected</li>
                        </ol>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Caution:</strong> 
                            Review each registration carefully before bulk approval.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Management Section -->
        <div class="card" id="password-management">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-key"></i> Password Management</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-warning"></i> How to Reset a User's Password</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the User:</strong> Go to Users list and locate the user</li>
                            <li><strong>Click "Reset Password" button</strong> (key icon)</li>
                            <li><strong>Confirm Action:</strong> Click "Yes" to confirm password reset</li>
                            <li><strong>New Password:</strong> System generates temporary password</li>
                            <li><strong>Notify User:</strong> Share the temporary password securely</li>
                            <li><strong>User Must Change:</strong> User will be forced to change password on next login</li>
                        </ol>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Security:</strong> 
                            Never share passwords via email or unsecured channels!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-shield-alt text-success"></i> Password Security Guidelines</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ul>
                            <li><strong>Strong Passwords Must Have:</strong>
                                <ul>
                                    <li>At least 8 characters</li>
                                    <li>Upper and lower case letters</li>
                                    <li>Numbers and special characters</li>
                                </ul>
                            </li>
                            <li><strong>Password Policies:</strong>
                                <ul>
                                    <li>Passwords expire after 90 days (configurable)</li>
                                    <li>Cannot reuse last 5 passwords</li>
                                    <li>Account locks after 5 failed attempts</li>
                                </ul>
                            </li>
                            <li><strong>Best Practices:</strong>
                                <ul>
                                    <li>Encourage users to use password managers</li>
                                    <li>Regular password updates</li>
                                    <li>Never share passwords</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Security Section -->
        <div class="card" id="user-security">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt"></i> User Security & Monitoring</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-eye text-info"></i> Monitoring User Activity</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>What to Monitor:</strong></p>
                        <ul>
                            <li>Failed login attempts</li>
                            <li>Unusual login times or locations</li>
                            <li>Multiple concurrent sessions</li>
                            <li>Data access patterns</li>
                        </ul>
                        
                        <p><strong>How to Check:</strong></p>
                        <ol>
                            <li>Review dashboard for security alerts</li>
                            <li>Check user activity logs in Settings</li>
                            <li>Monitor system health reports</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-exclamation-triangle text-danger"></i> Security Incident Response</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>If you suspect unauthorized access:</strong></p>
                        <ol>
                            <li><strong>Immediate Action:</strong> Deactivate the affected user account</li>
                            <li><strong>Reset Password:</strong> Generate new password immediately</li>
                            <li><strong>Review Logs:</strong> Check what data may have been accessed</li>
                            <li><strong>Contact User:</strong> Verify legitimate activity with the user</li>
                            <li><strong>Document:</strong> Record the incident and actions taken</li>
                            <li><strong>Strengthen:</strong> Update security measures if needed</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Sticky Table of Contents */
.sticky-toc {
    position: -webkit-sticky;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    z-index: 10;
}

.sticky-toc .card {
    margin-bottom: 0;
}

.nav-pills .nav-link {
    color: #495057;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
    color: white;
}

.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-success {
    border-left-color: #28a745;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-danger {
    border-left-color: #dc3545;
}

h5 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.card .card-body ul {
    padding-left: 1.5rem;
}

.card .card-body li {
    margin-bottom: 0.5rem;
}

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .sticky-toc {
        position: relative;
        top: auto;
        max-height: none;
        overflow-y: visible;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Smooth scroll for table of contents links (only within sticky-toc)
    $('.sticky-toc .nav-link').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target.startsWith('#')) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    // Highlight active section in table of contents (only within sticky-toc)
    $(window).scroll(function() {
        var scrollPos = $(window).scrollTop() + 150;
        $('.sticky-toc .nav-link').removeClass('active');
        
        $('div[id]').each(function() {
            var currLink = $('.sticky-toc a[href="#' + $(this).attr('id') + '"]');
            if ($(this).offset().top <= scrollPos && $(this).offset().top + $(this).height() > scrollPos) {
                currLink.addClass('active');
            }
        });
    });
});
</script>
@stop
