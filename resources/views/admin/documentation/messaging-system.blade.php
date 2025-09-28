@extends('adminlte::page')

@section('title', 'Messaging System - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-comments"></i> Messaging System
            <small class="text-muted">Complete step-by-step communication guide</small>
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
                        <a class="nav-link" href="#accessing-messages">Accessing Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sending-messages">Sending Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#managing-conversations">Managing Conversations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#message-status">Message Status & Reading</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#archiving-messages">Archiving Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#message-security">Security & Privacy</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Accessing Messages Section -->
        <div class="card" id="accessing-messages">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-inbox"></i> Accessing Messages - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About Messaging:</strong> 
                    The messaging system allows secure communication between administrators and patients within the CMS.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> How to Access Messages</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Messages:</strong> Click "Messages" in the left sidebar</li>
                            <li><strong>Message Inbox Opens:</strong> Shows all conversations</li>
                            <li><strong>Quick Stats:</strong> See unread message count on dashboard</li>
                        </ol>
                        
                        <p><strong>Message Interface Elements:</strong></p>
                        <ul>
                            <li><strong>Conversation List:</strong> Left panel shows all patient conversations</li>
                            <li><strong>Message Thread:</strong> Right panel shows selected conversation</li>
                            <li><strong>Unread Indicator:</strong> Bold text or badge for unread messages</li>
                            <li><strong>Search Bar:</strong> Find specific conversations or messages</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-success"></i> Reading Messages</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Select Conversation:</strong> Click on patient conversation in left panel</li>
                            <li><strong>Message Thread Loads:</strong> All messages with that patient appear</li>
                            <li><strong>Automatic Read Status:</strong> Messages marked as read when viewed</li>
                            <li><strong>Scroll Through History:</strong> See chronological message history</li>
                        </ol>
                        
                        <p><strong>Message Information Displayed:</strong></p>
                        <ul>
                            <li>Patient name and profile picture (if available)</li>
                            <li>Date and time of each message</li>
                            <li>Message content</li>
                            <li>Read/unread status</li>
                            <li>Sender identification (admin or patient)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sending Messages Section -->
        <div class="card" id="sending-messages">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane"></i> Sending Messages</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Starting a New Conversation</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Messages:</strong> Access messaging interface</li>
                            <li><strong>Click "New Message"</strong> or "Compose" button</li>
                            <li><strong>Select Patient:</strong>
                                <ul>
                                    <li>Search for patient by name</li>
                                    <li>Choose from dropdown list</li>
                                    <li>Verify correct patient selected</li>
                                </ul>
                            </li>
                            <li><strong>Compose Message:</strong> Type your message in the text area</li>
                            <li><strong>Send:</strong> Click "Send Message" button</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> 
                            You can also start conversations directly from a patient's profile page!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-reply text-success"></i> Replying to Messages</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Open Conversation:</strong> Click on existing conversation</li>
                            <li><strong>Read Patient Message:</strong> Review what patient has sent</li>
                            <li><strong>Type Reply:</strong> Use message input box at bottom</li>
                            <li><strong>Review Message:</strong> Check for clarity and accuracy</li>
                            <li><strong>Send Reply:</strong> Click "Send" or press Enter</li>
                        </ol>
                        
                        <p><strong>Best Practices for Replies:</strong></p>
                        <ul>
                            <li>Respond promptly to patient inquiries</li>
                            <li>Be clear and professional in communication</li>
                            <li>Answer all questions in the patient's message</li>
                            <li>Provide helpful and accurate information</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-edit text-info"></i> Message Composition Tips</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Writing Effective Messages:</strong></p>
                        <ul>
                            <li><strong>Be Clear:</strong> Use simple, understandable language</li>
                            <li><strong>Be Professional:</strong> Maintain medical professionalism</li>
                            <li><strong>Be Complete:</strong> Include all necessary information</li>
                            <li><strong>Be Helpful:</strong> Provide actionable guidance when possible</li>
                        </ul>
                        
                        <p><strong>Message Length Guidelines:</strong></p>
                        <ul>
                            <li>Keep messages concise but informative</li>
                            <li>Break long information into multiple messages if needed</li>
                            <li>Use bullet points for multiple items</li>
                            <li>Include specific details (dates, times, dosages, etc.)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managing Conversations Section -->
        <div class="card" id="managing-conversations">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks"></i> Managing Conversations</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-search text-primary"></i> Finding Conversations</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Search Methods:</strong></p>
                        <ol>
                            <li><strong>Patient Name Search:</strong> Type patient name in search box</li>
                            <li><strong>Browse List:</strong> Scroll through conversation list</li>
                            <li><strong>Filter by Status:</strong> View unread, archived, or all messages</li>
                            <li><strong>Date Filtering:</strong> Find conversations from specific time periods</li>
                        </ol>
                        
                        <p><strong>Conversation Sorting Options:</strong></p>
                        <ul>
                            <li><strong>Most Recent:</strong> Latest activity first (default)</li>
                            <li><strong>Alphabetical:</strong> Sort by patient name</li>
                            <li><strong>Unread First:</strong> Prioritize unread messages</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-star text-warning"></i> Prioritizing Important Conversations</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Marking Priority Messages:</strong></p>
                        <ol>
                            <li><strong>Open Conversation:</strong> Select patient conversation</li>
                            <li><strong>Mark as Important:</strong> Click star icon or priority flag</li>
                            <li><strong>Add Notes:</strong> Optional - reason for priority status</li>
                            <li><strong>Priority View:</strong> Filter to show only priority conversations</li>
                        </ol>
                        
                        <p><strong>Use Priority for:</strong></p>
                        <ul>
                            <li>Urgent medical inquiries</li>
                            <li>Follow-up required conversations</li>
                            <li>Prescription clarifications</li>
                            <li>Appointment-related urgent matters</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-tags text-success"></i> Organizing Conversations</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Conversation Organization:</strong></p>
                        <ul>
                            <li><strong>Active Conversations:</strong> Ongoing communication</li>
                            <li><strong>Resolved Conversations:</strong> Issues addressed</li>
                            <li><strong>Follow-up Needed:</strong> Requires admin action</li>
                            <li><strong>Information Only:</strong> Patient notifications/updates</li>
                        </ul>
                        
                        <p><strong>Managing Multiple Conversations:</strong></p>
                        <ol>
                            <li>Use tabs or panels to keep multiple conversations open</li>
                            <li>Respond to urgent messages first</li>
                            <li>Set aside time daily for message management</li>
                            <li>Use templates for common responses</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Status Section -->
        <div class="card" id="message-status">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle"></i> Message Status & Reading</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-eye text-info"></i> Understanding Message Status</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Message Status Indicators:</strong></p>
                        <ul>
                            <li><strong>Unread (Bold):</strong> New messages you haven't viewed</li>
                            <li><strong>Read:</strong> Messages you have opened and viewed</li>
                            <li><strong>Sent:</strong> Messages you have sent to patients</li>
                            <li><strong>Delivered:</strong> Patient has received your message</li>
                            <li><strong>Read by Patient:</strong> Patient has viewed your message</li>
                        </ul>
                        
                        <p><strong>Visual Indicators:</strong></p>
                        <ul>
                            <li>Bold text for unread messages</li>
                            <li>Blue dot or badge for unread count</li>
                            <li>Checkmarks for delivery/read status</li>
                            <li>Timestamps for message timing</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-bell text-success"></i> Message Notifications</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Notification Types:</strong></p>
                        <ul>
                            <li><strong>Dashboard Alert:</strong> Unread message count on main dashboard</li>
                            <li><strong>Email Notifications:</strong> New message alerts to admin email</li>
                            <li><strong>System Notifications:</strong> Pop-up or banner alerts</li>
                        </ul>
                        
                        <p><strong>Configuring Notifications:</strong></p>
                        <ol>
                            <li>Go to Settings → Notification Preferences</li>
                            <li>Enable/disable email notifications</li>
                            <li>Set notification timing preferences</li>
                            <li>Choose notification types to receive</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-clock text-warning"></i> Response Time Management</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Recommended Response Times:</strong></p>
                        <ul>
                            <li><strong>Urgent Medical:</strong> Within 1 hour</li>
                            <li><strong>Appointment Related:</strong> Within 4 hours</li>
                            <li><strong>General Inquiries:</strong> Within 24 hours</li>
                            <li><strong>Non-urgent:</strong> Within 2-3 business days</li>
                        </ul>
                        
                        <p><strong>Managing Response Expectations:</strong></p>
                        <ol>
                            <li>Set up auto-reply for after-hours messages</li>
                            <li>Include response time expectations in auto-replies</li>
                            <li>Prioritize messages based on urgency</li>
                            <li>Use templates for faster common responses</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archiving Messages Section -->
        <div class="card" id="archiving-messages">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-archive"></i> Archiving Messages</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> When to Archive Conversations</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Archive conversations when:</strong></p>
                        <ul>
                            <li>Issue has been completely resolved</li>
                            <li>Patient inquiry has been fully addressed</li>
                            <li>No further action is needed</li>
                            <li>Conversation is complete and inactive</li>
                            <li>Patient has switched to phone/in-person communication</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> How to Archive Conversations</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Select Conversation:</strong> Choose conversation to archive</li>
                            <li><strong>Click Archive Button:</strong> Usually in conversation toolbar</li>
                            <li><strong>Add Archive Reason (Optional):</strong> Note why archiving</li>
                            <li><strong>Confirm Archive:</strong> Click "Yes, Archive"</li>
                            <li><strong>Conversation Moves:</strong> No longer in active list</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            Archived conversations can be restored if needed later.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-undo text-warning"></i> Restoring Archived Conversations</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>View Archived:</strong> Filter messages to show "Archived" status</li>
                            <li><strong>Find Conversation:</strong> Locate the archived conversation</li>
                            <li><strong>Click "Restore":</strong> Button appears for archived items</li>
                            <li><strong>Confirm Restore:</strong> Click "Yes" to confirm</li>
                            <li><strong>Conversation Active:</strong> Returns to active message list</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Security Section -->
        <div class="card" id="message-security">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt"></i> Security & Privacy</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-lock text-danger"></i> HIPAA Compliance in Messaging</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Security Requirements:</strong></p>
                        <ul>
                            <li><strong>Secure Platform:</strong> All messages encrypted in transit and storage</li>
                            <li><strong>Access Control:</strong> Only authorized staff can access messages</li>
                            <li><strong>Audit Logs:</strong> All message activity is logged and monitored</li>
                            <li><strong>Data Retention:</strong> Messages retained per policy requirements</li>
                        </ul>
                        
                        <p><strong>Staff Responsibilities:</strong></p>
                        <ul>
                            <li>Never share patient messages with unauthorized personnel</li>
                            <li>Log out of messaging system when away from workstation</li>
                            <li>Report any suspected security breaches immediately</li>
                            <li>Use secure communication methods only</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-info"></i> Message Monitoring & Auditing</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>System Monitoring:</strong></p>
                        <ul>
                            <li><strong>Access Tracking:</strong> Who accessed which conversations</li>
                            <li><strong>Message Logs:</strong> Complete record of all messages</li>
                            <li><strong>Activity Timestamps:</strong> When messages were sent/read</li>
                            <li><strong>User Authentication:</strong> Verification of user identity</li>
                        </ul>
                        
                        <p><strong>Audit Trail Features:</strong></p>
                        <ul>
                            <li>Read receipts and delivery confirmations</li>
                            <li>Message deletion tracking</li>
                            <li>Login/logout activity</li>
                            <li>Export capabilities for compliance reporting</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Best Practices for Secure Messaging</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Security Best Practices:</strong></p>
                        <ul>
                            <li><strong>Professional Communication:</strong> Maintain medical professionalism</li>
                            <li><strong>Accurate Information:</strong> Verify medical information before sending</li>
                            <li><strong>Appropriate Content:</strong> Only discuss relevant medical matters</li>
                            <li><strong>Emergency Procedures:</strong> Direct patients to emergency services when appropriate</li>
                        </ul>
                        
                        <p><strong>What NOT to include in messages:</strong></p>
                        <ul>
                            <li>Highly sensitive medical details (use secure methods instead)</li>
                            <li>Personal opinions about treatment</li>
                            <li>Information about other patients</li>
                            <li>Non-medical personal communications</li>
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
