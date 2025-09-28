@extends('adminlte::page')

@section('title', 'Appointment Management - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-calendar-check"></i> Appointment Management
            <small class="text-muted">Complete step-by-step appointment scheduling guide</small>
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
                        <a class="nav-link" href="#schedule-appointment">Scheduling New Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#view-appointments">Viewing Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#manage-appointments">Managing Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#calendar-view">Using Calendar View</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#approval-workflow">Approval Workflow</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reschedule-cancel">Rescheduling & Cancellation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#notifications">Notifications & Reminders</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Schedule Appointment Section -->
        <div class="card" id="schedule-appointment">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Scheduling New Appointments - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Before You Start:</strong> 
                    Make sure the patient is already in the system. You can't schedule appointments for non-registered patients.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> Step 1: Access Appointment Creation</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Appointments:</strong> Click "Appointments" in the left sidebar</li>
                            <li><strong>Click "Create Appointment"</strong> button (usually green button in top-right)</li>
                            <li><strong>Form Opens:</strong> New appointment scheduling form appears</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-lightbulb"></i> <strong>Quick Tip:</strong> 
                            You can also create appointments directly from a patient's profile page!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 2: Select Patient</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Patient Dropdown:</strong> Click on the patient selection field</li>
                            <li><strong>Search for Patient:</strong>
                                <ul>
                                    <li>Start typing patient's name</li>
                                    <li>System will show matching results</li>
                                    <li>You can search by first name, last name, or patient ID</li>
                                </ul>
                            </li>
                            <li><strong>Select Patient:</strong> Click on the correct patient from the dropdown</li>
                            <li><strong>Verification:</strong> Patient's basic info appears to confirm selection</li>
                        </ol>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                            Double-check you've selected the correct patient - similar names can be confusing!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-info"></i> Step 3: Set Date and Time</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Appointment Date:</strong>
                                <ul>
                                    <li>Click on the date field</li>
                                    <li>Use calendar picker to select date</li>
                                    <li>Or type date in MM/DD/YYYY format</li>
                                </ul>
                            </li>
                            <li><strong>Appointment Time:</strong>
                                <ul>
                                    <li>Click on time field</li>
                                    <li>Select from available time slots</li>
                                    <li>Or use time picker (HH:MM format)</li>
                                </ul>
                            </li>
                            <li><strong>Duration (Optional):</strong>
                                <ul>
                                    <li>Select expected appointment length</li>
                                    <li>Default is usually 30 minutes</li>
                                    <li>Can be adjusted based on appointment type</li>
                                </ul>
                            </li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Tip:</strong> 
                            The system may show conflicts if the time slot is already booked.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-warning"></i> Step 4: Add Appointment Details</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Reason for Visit:</strong>
                                <ul>
                                    <li>Enter the purpose of the appointment</li>
                                    <li>Be specific (e.g., "Annual checkup", "Follow-up for blood pressure")</li>
                                    <li>This helps with preparation and scheduling</li>
                                </ul>
                            </li>
                            <li><strong>Appointment Type (if available):</strong>
                                <ul>
                                    <li>Regular consultation</li>
                                    <li>Follow-up visit</li>
                                    <li>Emergency/Urgent</li>
                                    <li>Procedure/Treatment</li>
                                </ul>
                            </li>
                            <li><strong>Additional Notes:</strong>
                                <ul>
                                    <li>Any special instructions</li>
                                    <li>Preparation required</li>
                                    <li>Equipment needed</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-secondary"></i> Step 5: Set Status and Priority</h5>
                <div class="card border-secondary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Appointment Status:</strong>
                                <ul>
                                    <li><strong>Pending:</strong> Needs approval (default for patient requests)</li>
                                    <li><strong>Confirmed:</strong> Approved and scheduled</li>
                                    <li><strong>Active:</strong> Ready for the appointment date</li>
                                </ul>
                            </li>
                            <li><strong>Priority Level:</strong>
                                <ul>
                                    <li><strong>Normal:</strong> Standard appointment</li>
                                    <li><strong>High:</strong> Important follow-up</li>
                                    <li><strong>Urgent:</strong> Needs immediate attention</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 6: Save and Confirm</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Review Information:</strong> Double-check all details</li>
                            <li><strong>Click "Schedule Appointment"</strong> button</li>
                            <li><strong>Success Message:</strong> System confirms appointment creation</li>
                            <li><strong>Appointment Appears:</strong> In appointment list and calendar</li>
                            <li><strong>Notifications Sent:</strong> Patient receives confirmation (if email configured)</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong> 
                            Appointment is now scheduled and patient has been notified.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Appointments Section -->
        <div class="card" id="view-appointments">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Viewing Appointments</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-table text-primary"></i> Understanding the Appointment List</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Accessing Appointment List:</strong></p>
                        <ol>
                            <li>Click "Appointments" in the left sidebar</li>
                            <li>Default view shows today's appointments</li>
                            <li>Use filters to view different date ranges</li>
                        </ol>
                        
                        <p><strong>Information Displayed:</strong></p>
                        <ul>
                            <li><strong>Date & Time:</strong> When the appointment is scheduled</li>
                            <li><strong>Patient Name:</strong> Who the appointment is for</li>
                            <li><strong>Reason:</strong> Purpose of the visit</li>
                            <li><strong>Status:</strong> Pending/Confirmed/Completed/Cancelled</li>
                            <li><strong>Actions:</strong> View/Edit/Cancel/Complete buttons</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-filter text-info"></i> Filtering and Searching Appointments</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Filter Options:</strong></p>
                        <ul>
                            <li><strong>By Date:</strong> Today, This Week, This Month, Custom Range</li>
                            <li><strong>By Status:</strong> All, Pending, Confirmed, Completed, Cancelled</li>
                            <li><strong>By Patient:</strong> Search for specific patient's appointments</li>
                        </ul>
                        
                        <p><strong>How to Apply Filters:</strong></p>
                        <ol>
                            <li>Click "Filters" button above appointment list</li>
                            <li>Select your criteria</li>
                            <li>Click "Apply" to update the list</li>
                            <li>Click "Clear" to reset filters</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Appointments Section -->
        <div class="card" id="manage-appointments">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cogs"></i> Managing Existing Appointments</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-edit text-warning"></i> Editing Appointment Details</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the Appointment:</strong> Use appointment list or calendar view</li>
                            <li><strong>Click "Edit" button</strong> (pencil icon)</li>
                            <li><strong>Modify Details:</strong> Change any necessary information</li>
                            <li><strong>Save Changes:</strong> Click "Update Appointment"</li>
                            <li><strong>Notification:</strong> Patient receives update notification if email configured</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            Changes are logged with timestamp and user information.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-check-circle text-success"></i> Completing Appointments</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>When the appointment is finished:</strong></p>
                        <ol>
                            <li><strong>Find the Appointment:</strong> In today's appointment list</li>
                            <li><strong>Click "Complete" button</strong></li>
                            <li><strong>Add Summary (Optional):</strong> Notes about the visit</li>
                            <li><strong>Confirm Completion:</strong> Click "Mark as Complete"</li>
                            <li><strong>Status Updates:</strong> Appointment moves to completed status</li>
                        </ol>
                        
                        <p><strong>Benefits of Completing Appointments:</strong></p>
                        <ul>
                            <li>Accurate record keeping</li>
                            <li>Better reporting and analytics</li>
                            <li>Patient history tracking</li>
                            <li>Billing and insurance purposes</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-times-circle text-danger"></i> Cancelling Appointments</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find the Appointment:</strong> Locate in appointment list</li>
                            <li><strong>Click "Cancel" button</strong></li>
                            <li><strong>Provide Reason:</strong> Required - explain why cancelling</li>
                            <li><strong>Confirm Cancellation:</strong> Click "Yes, Cancel Appointment"</li>
                            <li><strong>Notification:</strong> Patient is automatically notified</li>
                        </ol>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                            Cancelled appointments are preserved for record-keeping but free up the time slot.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar View Section -->
        <div class="card" id="calendar-view">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Using Calendar View</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-eye text-primary"></i> Accessing Calendar View</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Calendar:</strong> Click "Calendar" under Appointments menu</li>
                            <li><strong>Calendar Loads:</strong> Shows current month with appointments</li>
                            <li><strong>View Options:</strong> Month, Week, or Day view available</li>
                        </ol>
                        
                        <p><strong>Calendar Features:</strong></p>
                        <ul>
                            <li><strong>Color Coding:</strong> Different colors for appointment statuses</li>
                            <li><strong>Click to View:</strong> Click any appointment for details</li>
                            <li><strong>Navigation:</strong> Use arrows to move between months/weeks</li>
                            <li><strong>Today Button:</strong> Quick return to current date</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-arrows-alt text-success"></i> Drag and Drop Functionality</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Quick Rescheduling:</strong></p>
                        <ol>
                            <li><strong>Click and Hold:</strong> On the appointment you want to move</li>
                            <li><strong>Drag:</strong> To the new date/time slot</li>
                            <li><strong>Drop:</strong> Release to place in new slot</li>
                            <li><strong>Confirm:</strong> System asks for confirmation</li>
                            <li><strong>Update:</strong> Appointment is moved and notifications sent</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            This only works for confirmed appointments, not pending ones.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Workflow Section -->
        <div class="card" id="approval-workflow">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-double"></i> Appointment Approval Workflow</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About Approvals:</strong> 
                    When patients request appointments online, they need admin approval before being confirmed.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> Reviewing Pending Appointments</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Check Dashboard:</strong> Shows count of pending approvals</li>
                            <li><strong>Go to Appointments:</strong> Filter by "Pending" status</li>
                            <li><strong>Review Details:</strong> Check patient, date, time, and reason</li>
                            <li><strong>Verify Availability:</strong> Ensure no conflicts exist</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-thumbs-up text-success"></i> Approving Appointments</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Click "Approve" button</strong> next to pending appointment</li>
                            <li><strong>Confirm Approval:</strong> Click "Yes, Approve"</li>
                            <li><strong>Status Changes:</strong> From Pending to Confirmed</li>
                            <li><strong>Notification Sent:</strong> Patient receives approval email</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-thumbs-down text-danger"></i> Rejecting Appointments</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Click "Reject" button</strong></li>
                            <li><strong>Provide Reason:</strong> Required - explain why rejecting</li>
                            <li><strong>Suggest Alternative (Optional):</strong> Offer different time slots</li>
                            <li><strong>Confirm Rejection:</strong> Click "Yes, Reject"</li>
                            <li><strong>Patient Notified:</strong> Receives rejection with reason</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule and Cancel Section -->
        <div class="card" id="reschedule-cancel">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-times"></i> Rescheduling & Cancellation</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-calendar-plus text-warning"></i> Rescheduling Appointments</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Method 1: Using Edit Function</strong></p>
                        <ol>
                            <li>Click "Edit" on the appointment</li>
                            <li>Change date and/or time</li>
                            <li>Save changes</li>
                            <li>Patient receives update notification</li>
                        </ol>
                        
                        <p><strong>Method 2: Drag and Drop (Calendar View)</strong></p>
                        <ol>
                            <li>Open calendar view</li>
                            <li>Drag appointment to new slot</li>
                            <li>Confirm the change</li>
                            <li>Automatic notification sent</li>
                        </ol>
                        
                        <p><strong>Method 3: Reschedule Button</strong></p>
                        <ol>
                            <li>Click "Reschedule" button</li>
                            <li>Select new date and time</li>
                            <li>Add reason for change</li>
                            <li>Confirm reschedule</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-ban text-danger"></i> Handling Cancellations</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Patient-Initiated Cancellations:</strong></p>
                        <ol>
                            <li>Patient calls or messages to cancel</li>
                            <li>Find appointment in system</li>
                            <li>Click "Cancel" button</li>
                            <li>Enter cancellation reason</li>
                            <li>Confirm cancellation</li>
                        </ol>
                        
                        <p><strong>Emergency Cancellations:</strong></p>
                        <ol>
                            <li>Use "Emergency Cancel" if available</li>
                            <li>Provide detailed reason</li>
                            <li>Consider immediate rescheduling</li>
                            <li>Follow up with patient</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Best Practice:</strong> 
                            Always try to offer alternative time slots when cancelling appointments.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Section -->
        <div class="card" id="notifications">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell"></i> Notifications & Reminders</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-envelope text-primary"></i> Automatic Email Notifications</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>System automatically sends emails for:</strong></p>
                        <ul>
                            <li><strong>Appointment Confirmation:</strong> When appointment is approved</li>
                            <li><strong>Appointment Changes:</strong> Date, time, or details modified</li>
                            <li><strong>Appointment Reminders:</strong> 24 hours before appointment</li>
                            <li><strong>Appointment Cancellation:</strong> When cancelled by either party</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> 
                            Email notifications require proper SMTP configuration in system settings.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-clock text-success"></i> Setting Up Reminders</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Reminder Configuration:</strong></p>
                        <ol>
                            <li>Go to Settings → Email Configuration</li>
                            <li>Enable appointment reminders</li>
                            <li>Set reminder timing (24 hours, 2 hours, etc.)</li>
                            <li>Customize reminder message template</li>
                            <li>Save settings</li>
                        </ol>
                        
                        <p><strong>Manual Reminders:</strong></p>
                        <ul>
                            <li>View tomorrow's appointments</li>
                            <li>Click "Send Reminder" for specific patients</li>
                            <li>Use bulk reminder function for all appointments</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-sms text-info"></i> SMS Notifications (If Available)</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>SMS reminders can be sent for:</strong></p>
                        <ul>
                            <li>Appointment confirmations</li>
                            <li>Day-before reminders</li>
                            <li>Last-minute changes</li>
                            <li>Cancellation notices</li>
                        </ul>
                        
                        <p><strong>Requirements:</strong></p>
                        <ul>
                            <li>SMS service configured in settings</li>
                            <li>Patient phone numbers must be valid</li>
                            <li>Patient consent for SMS notifications</li>
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
