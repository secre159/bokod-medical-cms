@extends('adminlte::page')

@section('title', 'Patient Management - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-user-injured"></i> Patient Management
            <small class="text-muted">Complete step-by-step patient management guide</small>
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
                        <a class="nav-link" href="#add-patient">Adding New Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#view-patients">Viewing Patient Lists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#edit-patient">Editing Patient Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#patient-history">Medical History Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#search-filter">Searching & Filtering</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#archive-patient">Archiving Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#patient-privacy">Privacy & Security</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Adding New Patients Section -->
        <div class="card" id="add-patient">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> Adding New Patients - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Before You Start:</strong> 
                    Gather all necessary patient information including ID documents, emergency contacts, and medical history.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> Step 1: Access Patient Creation Form</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Patients:</strong> Click "Patients" in the left sidebar</li>
                            <li><strong>Click "Add Patient"</strong> button (usually green button in top-right)</li>
                            <li><strong>Form Opens:</strong> You'll see the patient registration form</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 2: Fill Personal Information</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Required Fields (marked with *):</strong></p>
                        <ol>
                            <li><strong>First Name:</strong> Patient's legal first name</li>
                            <li><strong>Last Name:</strong> Patient's legal last name</li>
                            <li><strong>Date of Birth:</strong> Use date picker (MM/DD/YYYY format)</li>
                            <li><strong>Gender:</strong> Select from dropdown</li>
                            <li><strong>Phone Number:</strong> Primary contact number</li>
                            <li><strong>Email Address:</strong> For system notifications (must be unique)</li>
                        </ol>
                        
                        <p><strong>Optional but Recommended:</strong></p>
                        <ul>
                            <li>Middle name or initial</li>
                            <li>Patient ID number (if you use internal IDs)</li>
                            <li>Preferred name (if different from legal name)</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                            Double-check spelling and dates - these are hard to change later!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-info"></i> Step 3: Add Contact Information</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Address Information:</strong>
                                <ul>
                                    <li>Street address (line 1 and 2 if needed)</li>
                                    <li>City, State, ZIP code</li>
                                    <li>Country (if international patients)</li>
                                </ul>
                            </li>
                            <li><strong>Additional Contact:</strong>
                                <ul>
                                    <li>Alternative phone number (work, mobile)</li>
                                    <li>Emergency contact name and phone</li>
                                    <li>Relationship to emergency contact</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-warning"></i> Step 4: Create User Account (Optional)</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>If patient needs portal access:</strong></p>
                        <ol>
                            <li><strong>Check the Box:</strong> "Create user account for patient portal"</li>
                            <li><strong>Set Username:</strong> Enter unique username (suggest: firstnamelastname)</li>
                            <li><strong>Temporary Password:</strong> System will generate one automatically</li>
                            <li><strong>Email Notification:</strong> Patient will receive login credentials via email</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            You can always create the account later if needed.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-secondary"></i> Step 5: Add Medical Information</h5>
                <div class="card border-secondary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Basic Medical Info:</strong>
                                <ul>
                                    <li>Blood type (if known)</li>
                                    <li>Height and weight</li>
                                    <li>Known allergies (medications, food, environmental)</li>
                                </ul>
                            </li>
                            <li><strong>Medical History:</strong>
                                <ul>
                                    <li>Chronic conditions</li>
                                    <li>Previous surgeries</li>
                                    <li>Current medications</li>
                                </ul>
                            </li>
                            <li><strong>Insurance Information:</strong>
                                <ul>
                                    <li>Insurance provider</li>
                                    <li>Policy number</li>
                                    <li>Group number</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 6: Save and Verify</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Review Information:</strong> Double-check all entered data</li>
                            <li><strong>Click "Create Patient"</strong> button at the bottom</li>
                            <li><strong>Success Message:</strong> System will show confirmation</li>
                            <li><strong>Patient appears:</strong> In the patient list with a unique ID</li>
                            <li><strong>If email configured:</strong> Patient receives welcome email (if account was created)</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong> 
                            Patient is now in the system and ready for appointments.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Viewing Patient Lists Section -->
        <div class="card" id="view-patients">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Viewing Patient Lists & Information</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-table text-primary"></i> Understanding the Patient List</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Accessing Patient List:</strong></p>
                        <ol>
                            <li>Click "Patients" → "All Patients" in sidebar</li>
                            <li>Patient list loads with key information columns</li>
                        </ol>
                        
                        <p><strong>Information Displayed:</strong></p>
                        <ul>
                            <li><strong>Patient ID:</strong> Unique system identifier</li>
                            <li><strong>Name:</strong> Full name of patient</li>
                            <li><strong>Date of Birth:</strong> For verification</li>
                            <li><strong>Phone:</strong> Primary contact number</li>
                            <li><strong>Email:</strong> Email address on file</li>
                            <li><strong>Status:</strong> Active/Archived status</li>
                            <li><strong>Actions:</strong> View/Edit/Archive buttons</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-info"></i> Viewing Detailed Patient Information</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>From Patient List:</strong> Click "View" button (eye icon) next to patient name</li>
                            <li><strong>Patient Profile Opens:</strong> Shows comprehensive information</li>
                            <li><strong>Information Sections:</strong>
                                <ul>
                                    <li>Personal & Contact Information</li>
                                    <li>Medical History & Allergies</li>
                                    <li>Appointment History</li>
                                    <li>Prescription History</li>
                                    <li>Account Status & Login Info</li>
                                </ul>
                            </li>
                            <li><strong>Quick Actions Available:</strong> Edit, Schedule Appointment, View Messages</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Editing Patient Information Section -->
        <div class="card" id="edit-patient">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> Editing Patient Information</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-warning"></i> How to Update Patient Details</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the Patient:</strong>
                                <ul>
                                    <li>Use patient list or search function</li>
                                    <li>Click "Edit" button (pencil icon)</li>
                                </ul>
                            </li>
                            <li><strong>Edit Form Opens:</strong> Pre-filled with current information</li>
                            <li><strong>Make Changes:</strong>
                                <ul>
                                    <li>Update any fields that need changes</li>
                                    <li>Add new information as needed</li>
                                    <li>Correct any errors</li>
                                </ul>
                            </li>
                            <li><strong>Save Changes:</strong> Click "Update Patient" button</li>
                            <li><strong>Verify Updates:</strong> Check that changes appear in patient profile</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Tip:</strong> 
                            Changes are logged and timestamped for audit purposes.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-user-cog text-primary"></i> Updating Login Credentials</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>To change patient's username or reset password:</strong></p>
                        <ol>
                            <li><strong>Edit Patient:</strong> Go to patient edit form</li>
                            <li><strong>Account Section:</strong> Find "User Account" section</li>
                            <li><strong>Username Change:</strong> Enter new username (must be unique)</li>
                            <li><strong>Password Reset:</strong> Check "Reset Password" to generate new temporary password</li>
                            <li><strong>Save:</strong> Click "Update Patient"</li>
                            <li><strong>Notify Patient:</strong> Share new credentials securely</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History Management Section -->
        <div class="card" id="patient-history">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Medical History Management</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-success"></i> Adding Medical History Entries</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Access Patient Profile:</strong> View specific patient details</li>
                            <li><strong>Medical History Tab:</strong> Click on "Medical History" section</li>
                            <li><strong>Add New Entry:</strong> Click "Add History Entry" button</li>
                            <li><strong>Fill Details:</strong>
                                <ul>
                                    <li>Date of visit/treatment</li>
                                    <li>Diagnosis or condition</li>
                                    <li>Treatment provided</li>
                                    <li>Medications prescribed</li>
                                    <li>Notes and observations</li>
                                </ul>
                            </li>
                            <li><strong>Save Entry:</strong> Click "Add to History"</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-prescription-bottle-alt text-info"></i> Managing Prescriptions History</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Prescription history is automatically maintained when you:</strong></p>
                        <ul>
                            <li>Create prescriptions through the system</li>
                            <li>Dispense medications</li>
                            <li>Complete prescription fulfillment</li>
                        </ul>
                        
                        <p><strong>To view prescription history:</strong></p>
                        <ol>
                            <li>Go to patient profile</li>
                            <li>Click "Prescriptions" tab</li>
                            <li>View chronological list of all prescriptions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="card" id="search-filter">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-search"></i> Searching & Filtering Patients</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Quick Search Methods</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Search Box:</strong> Located at top of patient list
                                <ul>
                                    <li>Type patient name (first or last)</li>
                                    <li>Enter phone number</li>
                                    <li>Use email address</li>
                                    <li>Enter patient ID</li>
                                </ul>
                            </li>
                            <li><strong>Live Search:</strong> Results filter as you type</li>
                            <li><strong>Clear Search:</strong> Click "X" or delete search text</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-filter text-success"></i> Advanced Filtering Options</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Filter by Status:</strong></p>
                        <ul>
                            <li><strong>Active Patients:</strong> Currently in system</li>
                            <li><strong>Archived Patients:</strong> Inactive/historical records</li>
                            <li><strong>All Patients:</strong> Both active and archived</li>
                        </ul>
                        
                        <p><strong>Filter by Date:</strong></p>
                        <ul>
                            <li>Registration date range</li>
                            <li>Last appointment date</li>
                            <li>Last visit date</li>
                        </ul>
                        
                        <p><strong>How to Apply Filters:</strong></p>
                        <ol>
                            <li>Click "Filters" button above patient list</li>
                            <li>Select your filter criteria</li>
                            <li>Click "Apply Filters"</li>
                            <li>List updates to show only matching patients</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archive Patient Section -->
        <div class="card" id="archive-patient">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-archive"></i> Archiving Patients</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                    Archiving removes patients from active lists but preserves all data for future reference.
                </div>

                <h5><i class="fas fa-step-forward text-danger"></i> When to Archive Patients</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Consider archiving when:</strong></p>
                        <ul>
                            <li>Patient has moved away permanently</li>
                            <li>Patient has switched to another provider</li>
                            <li>Patient has been inactive for extended period (2+ years)</li>
                            <li>Duplicate records need to be cleaned up</li>
                            <li>Patient has requested account closure</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-warning"></i> How to Archive a Patient</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find Patient:</strong> Locate patient in the patient list</li>
                            <li><strong>Click "Archive" Button:</strong> Usually next to Edit button</li>
                            <li><strong>Confirm Action:</strong> System will ask for confirmation</li>
                            <li><strong>Add Archive Reason:</strong> Required - explain why archiving</li>
                            <li><strong>Confirm Archive:</strong> Click "Yes, Archive Patient"</li>
                            <li><strong>Patient Moved:</strong> No longer appears in active patient list</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            Archived patients can be restored if needed later.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-undo text-success"></i> Restoring Archived Patients</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>View Archived Patients:</strong> Filter list to show "Archived" status</li>
                            <li><strong>Find Patient:</strong> Locate the archived patient</li>
                            <li><strong>Click "Restore":</strong> Button appears for archived patients</li>
                            <li><strong>Confirm Restore:</strong> Click "Yes" to confirm</li>
                            <li><strong>Patient Active:</strong> Returns to active patient list</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy and Security Section -->
        <div class="card" id="patient-privacy">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt"></i> Patient Privacy & Security</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-lock text-danger"></i> HIPAA Compliance Guidelines</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Always remember:</strong></p>
                        <ul>
                            <li><strong>Minimum Necessary:</strong> Only access patient information you need for your job</li>
                            <li><strong>Authorized Access:</strong> Only view patients you're treating or supporting</li>
                            <li><strong>Secure Communication:</strong> Never share patient info via unsecured email</li>
                            <li><strong>Screen Privacy:</strong> Ensure others cannot see patient information on your screen</li>
                            <li><strong>Log Out:</strong> Always log out when leaving your workstation</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-info"></i> Access Monitoring</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>The system automatically tracks:</strong></p>
                        <ul>
                            <li>Who accessed each patient record</li>
                            <li>When the access occurred</li>
                            <li>What information was viewed or changed</li>
                            <li>IP address and device used</li>
                        </ul>
                        
                        <p><strong>Audit Trail:</strong></p>
                        <ol>
                            <li>All patient data access is logged</li>
                            <li>Changes are tracked with timestamps</li>
                            <li>Administrators can review access logs</li>
                            <li>Suspicious activity triggers alerts</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Security Best Practices</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ul>
                            <li><strong>Strong Passwords:</strong> Use complex, unique passwords</li>
                            <li><strong>Regular Updates:</strong> Change passwords every 90 days</li>
                            <li><strong>Secure Workstation:</strong> Lock screen when away</li>
                            <li><strong>Report Issues:</strong> Immediately report suspected security breaches</li>
                            <li><strong>Training:</strong> Stay updated on privacy policies and procedures</li>
                            <li><strong>Physical Security:</strong> Keep printed patient information secure</li>
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
.text-secondary { color: #6c757d !important; }

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
