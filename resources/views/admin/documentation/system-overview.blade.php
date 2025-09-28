@extends('adminlte::page')

@section('title', 'System Overview - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-info-circle"></i> System Overview
            <small class="text-muted">Understanding your Clinic Management System</small>
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
                        <a class="nav-link" href="#introduction">Introduction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#key-features">Key Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#user-roles">User Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#system-architecture">System Architecture</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#getting-started">Getting Started</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#best-practices">Best Practices</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Introduction Section -->
        <div class="card" id="introduction">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-play-circle"></i> Introduction</h3>
            </div>
            <div class="card-body">
                <p class="lead">
                    Welcome to the comprehensive Clinic Management System (CMS) designed specifically for healthcare facilities. 
                    This system provides a complete solution for managing patients, appointments, medicines, and communications 
                    in a healthcare environment.
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About the CMS:</strong> 
                    This Clinic Management System is designed specifically for clinic operations, providing comprehensive tools for managing patients, appointments, medicines, and healthcare data.
                </div>
                </div>
            </div>
        </div>

        <!-- Key Features Section -->
        <div class="card" id="key-features">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star"></i> Key Features</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user-injured text-primary"></i> Patient Management</h5>
                        <ul>
                            <li>Patient registration and profile management</li>
                            <li>Medical history tracking</li>
                            <li>Patient search and filtering</li>
                            <li>Patient archiving and data management</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-calendar-check text-success"></i> Appointment System</h5>
                        <ul>
                            <li>Appointment scheduling and management</li>
                            <li>Calendar view with drag-and-drop</li>
                            <li>Approval workflow for appointments</li>
                            <li>Automated notifications</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5><i class="fas fa-pills text-warning"></i> Medicine Management</h5>
                        <ul>
                            <li>Medicine inventory and stock tracking</li>
                            <li>Prescription management</li>
                            <li>Low stock alerts</li>
                            <li>Dispensing history</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-comments text-info"></i> Communication</h5>
                        <ul>
                            <li>Internal messaging system</li>
                            <li>Patient-admin communication</li>
                            <li>Email notifications</li>
                            <li>Message archiving</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles Section -->
        <div class="card" id="user-roles">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users-cog"></i> User Roles & Permissions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5><i class="fas fa-user-shield"></i> Administrator</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Full system access including:</strong></p>
                                <ul>
                                    <li>User management</li>
                                    <li>System settings</li>
                                    <li>All patient operations</li>
                                    <li>Reports and analytics</li>
                                    <li>Medicine inventory management</li>
                                    <li>Registration approvals</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5><i class="fas fa-user"></i> Patient</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Limited access including:</strong></p>
                                <ul>
                                    <li>Personal profile management</li>
                                    <li>View medical history</li>
                                    <li>Appointment scheduling</li>
                                    <li>Messaging with admins</li>
                                    <li>View prescriptions</li>
                                    <li>Update personal information</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Architecture Section -->
        <div class="card" id="system-architecture">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sitemap"></i> System Architecture</h3>
            </div>
            <div class="card-body">
                <p>The system is built using modern web technologies:</p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-code"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Backend</span>
                                <span class="info-box-number">Laravel PHP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-desktop"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Frontend</span>
                                <span class="info-box-number">AdminLTE</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-database"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Database</span>
                                <span class="info-box-number">MySQL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Getting Started Section -->
        <div class="card" id="getting-started">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-play"></i> Getting Started - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>First Time Setup:</strong> 
                    Follow these steps in order to set up your CMS system properly.
                </div>

                <h5><i class="fas fa-step-forward"></i> Step 1: Initial Login & Dashboard</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Access the system:</strong> Go to your CMS URL (e.g., http://your-domain.com)</li>
                            <li><strong>Login:</strong> Enter your administrator username and password</li>
                            <li><strong>Dashboard Review:</strong> Take a moment to familiarize yourself with the dashboard layout
                                <ul>
                                    <li>Left sidebar: Main navigation menu</li>
                                    <li>Top cards: Key statistics (patients, appointments, etc.)</li>
                                    <li>Recent activity: Shows latest system activity</li>
                                </ul>
                            </li>
                        </ol>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                            Change your default password immediately after first login!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward"></i> Step 2: Configure System Settings</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Settings:</strong> Click "Settings" in the left sidebar</li>
                            <li><strong>General Settings:</strong>
                                <ul>
                                    <li>Set your clinic/hospital name</li>
                                    <li>Upload your logo</li>
                                    <li>Set contact information</li>
                                </ul>
                            </li>
                            <li><strong>Email Configuration:</strong>
                                <ul>
                                    <li>Configure SMTP settings for notifications</li>
                                    <li>Test email functionality</li>
                                </ul>
                            </li>
                            <li><strong>Save Changes:</strong> Always click "Save" after making changes</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward"></i> Step 3: Create Your First Patient</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Patients:</strong> Click "Patients" → "Add Patient" in sidebar</li>
                            <li><strong>Fill Required Information:</strong>
                                <ul>
                                    <li>Personal details (Name, Date of Birth, etc.)</li>
                                    <li>Contact information (Phone, Email, Address)</li>
                                    <li>Emergency contact details</li>
                                </ul>
                            </li>
                            <li><strong>Set Login Credentials:</strong>
                                <ul>
                                    <li>Create username for patient portal access</li>
                                    <li>System will generate temporary password</li>
                                </ul>
                            </li>
                            <li><strong>Submit:</strong> Click "Create Patient" button</li>
                            <li><strong>Verify:</strong> Patient should appear in the patients list</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward"></i> Step 4: Schedule Your First Appointment</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Appointments:</strong> Click "Appointments" in sidebar</li>
                            <li><strong>Click "Create Appointment"</strong></li>
                            <li><strong>Select Patient:</strong> Choose from dropdown or search</li>
                            <li><strong>Set Date & Time:</strong> Use the date/time pickers</li>
                            <li><strong>Add Details:</strong>
                                <ul>
                                    <li>Reason for visit</li>
                                    <li>Duration (if needed)</li>
                                    <li>Any special notes</li>
                                </ul>
                            </li>
                            <li><strong>Save:</strong> Click "Schedule Appointment"</li>
                            <li><strong>Verify:</strong> Check the calendar view to see your appointment</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward"></i> Step 5: Add Medicines to Inventory</h5>
                <div class="card border-secondary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Medicines:</strong> Click "Medicines" → "Add Medicine"</li>
                            <li><strong>Enter Medicine Details:</strong>
                                <ul>
                                    <li>Medicine name and generic name</li>
                                    <li>Dosage form (tablet, capsule, syrup, etc.)</li>
                                    <li>Strength/concentration</li>
                                </ul>
                            </li>
                            <li><strong>Set Inventory:</strong>
                                <ul>
                                    <li>Initial stock quantity</li>
                                    <li>Minimum stock level (for alerts)</li>
                                    <li>Unit price</li>
                                </ul>
                            </li>
                            <li><strong>Save:</strong> Click "Add Medicine"</li>
                            <li><strong>Check Stock:</strong> Visit "Stock Management" to verify</li>
                        </ol>
                    </div>
                </div>

                <div class="alert alert-success mt-4">
                    <i class="fas fa-check-circle"></i> <strong>Congratulations!</strong> 
                    You've completed the basic setup. Your system is ready for daily operations!
                </div>

                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-lightbulb"></i> Next Steps:</h6>
                        <ul class="mb-0">
                            <li>Explore each documentation section for detailed guides</li>
                            <li>Set up user accounts for your staff</li>
                            <li>Configure backup settings</li>
                            <li>Test patient portal functionality</li>
                            <li>Review and customize notification templates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Practices Section -->
        <div class="card" id="best-practices">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle"></i> Best Practices</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-shield-alt text-danger"></i> Security</h5>
                        <ul>
                            <li>Regularly update passwords</li>
                            <li>Use strong authentication</li>
                            <li>Monitor user access logs</li>
                            <li>Keep system updated</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-database text-primary"></i> Data Management</h5>
                        <ul>
                            <li>Regular data backups</li>
                            <li>Data validation procedures</li>
                            <li>Patient data privacy compliance</li>
                            <li>Archive old records appropriately</li>
                        </ul>
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
    z-index: 100;
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

.info-box {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    border-radius: 0.25rem;
    margin-bottom: 1rem;
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
    // Smooth scroll for table of contents links
    $('.nav-link').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target.startsWith('#')) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    // Highlight active section in table of contents
    $(window).scroll(function() {
        var scrollPos = $(window).scrollTop() + 150;
        $('.nav-link').removeClass('active');
        
        $('div[id]').each(function() {
            var currLink = $('a[href="#' + $(this).attr('id') + '"]');
            if ($(this).offset().top <= scrollPos && $(this).offset().top + $(this).height() > scrollPos) {
                currLink.addClass('active');
            }
        });
    });
});
</script>
@stop