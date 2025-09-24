@extends('adminlte::page')

@section('title', 'Appointment Calendar | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Appointment Calendar</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">Calendar</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Calendar Actions Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt mr-2"></i>Calendar Controls
            </h3>
            <div class="card-tools">
                <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>New Appointment
                </a>
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list mr-1"></i>List View
                </a>
            </div>
        </div>
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-1">View:</label>
                        <select id="calendarView" class="form-control form-control-sm">
                            <option value="dayGridMonth">Month</option>
                            <option value="timeGridWeek">Week</option>
                            <option value="timeGridDay">Day</option>
                            <option value="listWeek">Agenda</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-1">Filter by Status:</label>
                        <select id="statusFilter" class="form-control form-control-sm">
                            <option value="">All Appointments</option>
                            <option value="approved">Approved Only</option>
                            <option value="pending">Pending Approval</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="mb-1">Legend:</label>
                        <div class="d-flex flex-wrap">
                            <span class="badge badge-success mr-2 mb-1">
                                <i class="fas fa-circle mr-1"></i>Approved
                            </span>
                            <span class="badge badge-warning mr-2 mb-1">
                                <i class="fas fa-circle mr-1"></i>Pending Approval
                            </span>
                            <span class="badge mr-2 mb-1" style="background-color: #17a2b8; color: white;">
                                <i class="fas fa-circle mr-1"></i>Completed 🔒
                            </span>
                            <span class="badge mr-2 mb-1" style="background-color: #6c757d; color: white;">
                                <i class="fas fa-circle mr-1"></i>Cancelled 🔒
                            </span>
                            <span class="badge mr-2 mb-1" style="background-color: #fd7e14; color: white;">
                                <i class="fas fa-circle mr-1"></i>Reschedule Request
                            </span>
                            <span class="badge badge-danger mr-2 mb-1">
                                <i class="fas fa-circle mr-1"></i>Overdue
                            </span>
                            <span class="badge" style="background-color: #f44336; color: white;">
                                🇵🇭 Philippine Holiday
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar mr-2"></i>Appointment Calendar
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="appointmentCalendar"></div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-calendar-check mr-2"></i>Appointment Details
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="appointmentDetails">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <p>Loading appointment details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="appointmentActions">
                        <!-- Dynamic action buttons will be loaded here -->
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Form Modal -->
    <div class="modal fade" id="appointmentFormModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-plus mr-2"></i>Quick Appointment
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quickAppointmentForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalPatientId">Patient <span class="text-danger">*</span></label>
                                    <select class="form-control" id="modalPatientId" name="patient_id" required>
                                        <option value="">Select Patient...</option>
                                        <!-- Options will be populated dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalReason">Reason <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="modalReason" name="reason" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalDate">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="modalDate" name="appointment_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalTime">Time <span class="text-danger">*</span></label>
                                    <select class="form-control" id="modalTime" name="appointment_time" required>
                                        <option value="">Select Time...</option>
                                        <!-- Morning Session: 8:00 AM - 12:00 PM -->
                                        <optgroup label="Morning Session (8:00 AM - 12:00 PM)">
                                            <option value="08:00">8:00 AM</option>
                                            <option value="08:15">8:15 AM</option>
                                            <option value="08:30">8:30 AM</option>
                                            <option value="08:45">8:45 AM</option>
                                            <option value="09:00">9:00 AM</option>
                                            <option value="09:15">9:15 AM</option>
                                            <option value="09:30">9:30 AM</option>
                                            <option value="09:45">9:45 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="10:15">10:15 AM</option>
                                            <option value="10:30">10:30 AM</option>
                                            <option value="10:45">10:45 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="11:15">11:15 AM</option>
                                            <option value="11:30">11:30 AM</option>
                                            <option value="11:45">11:45 AM</option>
                                        </optgroup>
                                        <!-- Afternoon Session: 1:00 PM - 5:00 PM -->
                                        <optgroup label="Afternoon Session (1:00 PM - 5:00 PM)">
                                            <option value="13:00">1:00 PM</option>
                                            <option value="13:15">1:15 PM</option>
                                            <option value="13:30">1:30 PM</option>
                                            <option value="13:45">1:45 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                            <option value="14:15">2:15 PM</option>
                                            <option value="14:30">2:30 PM</option>
                                            <option value="14:45">2:45 PM</option>
                                            <option value="15:00">3:00 PM</option>
                                            <option value="15:15">3:15 PM</option>
                                            <option value="15:30">3:30 PM</option>
                                            <option value="15:45">3:45 PM</option>
                                            <option value="16:00">4:00 PM</option>
                                            <option value="16:15">4:15 PM</option>
                                            <option value="16:30">4:30 PM</option>
                                            <option value="16:45">4:45 PM</option>
                                            <option value="17:00">5:00 PM</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>School Hours:</strong> 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM, Monday to Friday only.<br>
                            <strong>Note:</strong> No appointments on weekends or Philippine holidays.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveQuickAppointment">
                        <i class="fas fa-save mr-1"></i>Save Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Message Modal for confirmations -->
    @include('components.message-modal')

@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css" rel="stylesheet">
<style>
    #appointmentCalendar {
        min-height: 600px;
    }
    
    .fc-event {
        cursor: pointer;
        border-radius: 3px;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
    }
    
    .fc-event:hover {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
        transition: all 0.2s ease;
    }
    
    .fc-daygrid-event {
        white-space: normal !important;
        min-height: 22px !important;
        padding: 2px 4px !important;
    }
    
    .fc-event-title {
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        line-height: 1.2 !important;
    }
    
    .fc-event-time {
        font-weight: 700 !important;
        font-size: 0.75rem !important;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .fc-button {
        text-transform: capitalize;
    }
    
    .card-maximized .fc {
        height: calc(100vh - 200px) !important;
    }
    
    .appointment-status {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.7em;
    }
    
    /* Event status styling - Enhanced visibility */
    .appointment-pending {
        border-left: 4px solid #ffc107 !important;
        background-color: #fff3cd !important;
        color: #856404 !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 1px rgba(255,255,255,0.8) !important;
    }
    
    .appointment-reschedule {
        border-left: 4px solid #17a2b8 !important;
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 1px rgba(255,255,255,0.8) !important;
    }
    
    /* Additional appointment status styles */
    .fc-event[style*="background-color: rgb(40, 167, 69)"] {
        /* Approved appointments - Green */
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
        border: 1px solid #28a745 !important;
    }
    
    .fc-event[style*="background-color: rgb(255, 193, 7)"] {
        /* Pending appointments - Yellow */
        color: #212529 !important;
        font-weight: 700 !important;
        text-shadow: 0 1px 1px rgba(255,255,255,0.8) !important;
        border: 2px solid #ffc107 !important;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
    }
    
    .fc-event[style*="background-color: rgb(220, 53, 69)"] {
        /* Overdue appointments - Red */
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
        border: 1px solid #dc3545 !important;
    }
    
    .fc-event[style*="background-color: rgb(253, 126, 20)"] {
        /* Reschedule appointments - Orange */
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
        border: 1px solid #fd7e14 !important;
    }
    
    .fc-event[style*="background-color: rgb(23, 162, 184)"] {
        /* Completed appointments - Blue */
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
        border: 1px solid #17a2b8 !important;
    }
    
    .fc-event[style*="background-color: rgb(108, 117, 125)"] {
        /* Cancelled appointments - Gray */
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
        border: 1px solid #6c757d !important;
    }
    
    .appointment-hover {
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    
    /* Loading states */
    .fc-event.updating {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }
    
    .fc-event.updating::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.1);
        border-radius: 3px;
    }
    
    /* Drag feedback */
    .fc-event:hover {
        cursor: grab;
    }
    
    .fc-event.fc-event-dragging {
        cursor: grabbing;
        opacity: 0.8;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        z-index: 1000;
    }
    
    .fc-daygrid-day.fc-highlight {
        background: rgba(40, 167, 69, 0.1) !important;
        border: 2px dashed #28a745 !important;
    }
    
    .fc-daygrid-day.fc-highlight.invalid-drop {
        background: rgba(220, 53, 69, 0.1) !important;
        border: 2px dashed #dc3545 !important;
    }
    
    /* Drag helper message */
    .drag-helper {
        border-left: 4px solid #17a2b8;
        border-radius: 6px;
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        color: #0c5460;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Calendar alerts */
    .calendar-alert {
        position: relative;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    
    /* Modal enhancements */
    .modal-header {
        border-bottom: 1px solid #e9ecef;
    }
    
    .modal-footer {
        border-top: 1px solid #e9ecef;
    }
    
    /* Button loading states */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }
    
    .btn-loading .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    
    /* Tooltip styling */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        white-space: pre-line;
    }
    
    /* Simple, reliable CSS overrides for pending appointments */
    .fc-event.status-pending {
        background: #ffc107 !important;
        background-color: #ffc107 !important;
        color: #000000 !important;
        font-weight: 900 !important;
        border: 3px solid #e0a800 !important;
    }
    
    .fc-event.status-pending * {
        color: #000000 !important;
        font-weight: 900 !important;
    }
    
    /* Holiday styling */
    .fc-bg-event.holiday-event {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%) !important;
        border: 1px solid #f44336 !important;
        opacity: 0.3 !important;
    }
    
    .fc-daygrid-day.fc-day-today .holiday-event {
        opacity: 0.5 !important;
    }
    
    /* Holiday text overlay */
    .fc-daygrid-day-frame {
        position: relative;
    }
    
    .holiday-overlay {
        position: absolute;
        top: 2px;
        right: 2px;
        background: #f44336;
        color: white;
        padding: 1px 4px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: bold;
        z-index: 2;
        pointer-events: none;
    }
    
    /* Tooltip for holidays */
    .fc-daygrid-day.has-holiday {
        position: relative;
    }
    
    .fc-daygrid-day.has-holiday::before {
        content: '🇵🇭';
        position: absolute;
        top: 2px;
        right: 4px;
        font-size: 14px;
        z-index: 3;
        pointer-events: none;
    }
    
    .fc-event.status-approved {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
        border: 1px solid #28a745 !important;
    }
    
    .fc-event.status-overdue {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5) !important;
        border: 1px solid #dc3545 !important;
        animation: pulse-red 2s infinite;
    }
    
    .fc-event.status-reschedule {
        background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
        border: 1px solid #fd7e14 !important;
    }
    
    .fc-event.status-completed {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important;
        border: 1px solid #17a2b8 !important;
    }
    
    .fc-event.status-cancelled {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5) !important;
        border: 1px solid #6c757d !important;
        opacity: 0.7;
        cursor: not-allowed !important;
    }
    
    .fc-event.status-completed {
        cursor: not-allowed !important;
        opacity: 0.8 !important;
    }
    
    /* Override hover effects for non-draggable appointments */
    .fc-event.status-cancelled:hover,
    .fc-event.status-completed:hover {
        cursor: not-allowed !important;
        transform: none !important;
        box-shadow: none !important;
    }
    
    /* Visual indicator for non-draggable appointments */
    .fc-event.status-cancelled::after,
    .fc-event.status-completed::after {
        content: '🔒';
        position: absolute;
        top: 2px;
        right: 2px;
        font-size: 10px;
        opacity: 0.7;
    }
    
    /* Pulse animation for overdue appointments */
    @keyframes pulse-red {
        0% {
            box-shadow: 0 2px 4px rgba(220,53,69,0.3);
        }
        50% {
            box-shadow: 0 4px 8px rgba(220,53,69,0.6);
        }
        100% {
            box-shadow: 0 2px 4px rgba(220,53,69,0.3);
        }
    }
    
    /* High contrast mode for better accessibility */
    @media (prefers-contrast: high) {
        .fc-event {
            border-width: 2px !important;
            font-weight: 700 !important;
        }
        
        .fc-event.status-pending {
            background: #ffeb3b !important;
            color: #000000 !important;
            border-color: #f57f17 !important;
        }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
        }
        
        .fc-toolbar-chunk {
            margin: 0.2em 0;
        }
        
        .modal-dialog {
            margin: 10px;
            max-width: calc(100% - 20px);
        }
        
        #appointmentCalendar {
            min-height: 400px;
        }
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js"></script>
<script>
$(document).ready(function() {
    let calendar;
    let currentView = 'dayGridMonth';
    
    // Initialize calendar
    initializeCalendar();
    
    // Load patients for quick form
    loadPatients();
    
    // View selector
    $('#calendarView').change(function() {
        currentView = $(this).val();
        calendar.changeView(currentView);
    });
    
    // Status filter
    $('#statusFilter').change(function() {
        calendar.refetchEvents();
    });
    
    // Save quick appointment
    $('#saveQuickAppointment').click(function() {
        saveQuickAppointment();
    });
    
    function initializeCalendar() {
        const calendarEl = document.getElementById('appointmentCalendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: currentView,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '17:00:00',
            slotDuration: '00:15:00', // 15-minute slots
            snapDuration: '00:15:00',
            businessHours: [
                {
                    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday only
                    startTime: '08:00',
                    endTime: '12:00'
                },
                {
                    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday only
                    startTime: '13:00', // 1:00 PM
                    endTime: '17:00'  // 5:00 PM
                }
            ],
            hiddenDays: [0, 6], // Hide Sunday (0) and Saturday (6)
            selectConstraint: 'businessHours',
            eventConstraint: 'businessHours',
            nowIndicator: true,
            selectable: true,
            selectMirror: true,
            editable: true,
            eventResizableFromStart: false,
            eventDurationEditable: false,
            eventStartEditable: true,
            events: function(info, successCallback, failureCallback) {
                const statusFilter = $('#statusFilter').val();
                
                // Get appointments
                $.ajax({
                    url: '{{ route("appointments.calendar.data") }}',
                    type: 'GET',
                    data: {
                        start: info.startStr,
                        end: info.endStr,
                        status_filter: statusFilter
                    },
                    success: function(appointmentData) {
                        // Get holidays in the date range and combine with appointments
                        const holidays = getHolidaysInRange(info.startStr, info.endStr);
                        const allEvents = appointmentData.concat(holidays);
                        successCallback(allEvents);
                    },
                    error: function() {
                        failureCallback();
                        showAlert('error', 'Failed to load calendar events');
                    }
                });
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                
                // Check if this is a holiday event
                if (info.event.extendedProps.type === 'holiday') {
                    showAlert('info', `🇵🇭 ${info.event.extendedProps.holiday_name} - No appointments can be scheduled on this date.`);
                    return;
                }
                
                // Only show appointment details for actual appointments
                if (info.event.id && !info.event.id.startsWith('holiday-')) {
                    showAppointmentDetails(info.event.id);
                }
            },
            select: function(info) {
                // Check if selected date is a holiday first
                const selectedDateStr = info.startStr.split('T')[0]; // Get YYYY-MM-DD part
                const year = parseInt(selectedDateStr.substring(0, 4));
                let isHoliday = false;
                
                if (philippineHolidays[year]) {
                    isHoliday = philippineHolidays[year].includes(selectedDateStr);
                } else if (year > 2030) {
                    const generatedHolidays = generateHolidaysForYear(year);
                    isHoliday = generatedHolidays.includes(selectedDateStr);
                }
                
                if (isHoliday) {
                    const holidayName = getHolidayName(selectedDateStr);
                    showAlert('error', `🇵🇭 Cannot schedule appointments on ${holidayName}. Please select a different date.`);
                    calendar.unselect();
                    return;
                }
                
                const validation = isValidAppointmentDateTime(info.start);
                if (!validation.valid) {
                    showAlert('error', validation.message);
                    calendar.unselect();
                    return;
                }
                
                // Check if time is during valid school hours
                const timeStr = info.startStr.includes('T') ? info.startStr.split('T')[1].substring(0, 5) : '08:00';
                if (!isValidSchoolTime(timeStr)) {
                    showAlert('error', 'Appointments can only be scheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.');
                    calendar.unselect();
                    return;
                }
                
                openQuickAppointmentForm(info.startStr.split('T')[0], timeStr);
                calendar.unselect();
            },
            dateClick: function(info) {
                // Use the dateStr directly for holiday check to avoid timezone issues
                const dateStr = info.dateStr; // This is already in YYYY-MM-DD format
                
                // Check if this date string is in our holiday list
                const year = parseInt(dateStr.substring(0, 4));
                let isHoliday = false;
                
                if (philippineHolidays[year]) {
                    isHoliday = philippineHolidays[year].includes(dateStr);
                } else if (year > 2030) {
                    const generatedHolidays = generateHolidaysForYear(year);
                    isHoliday = generatedHolidays.includes(dateStr);
                }
                
                if (isHoliday) {
                    const holidayName = getHolidayName(dateStr);
                    showAlert('error', `🇵🇭 Cannot schedule appointments on ${holidayName}. Please select a different date.`);
                    return;
                }
                
                // Use direct validation with dateStr to avoid timezone issues
                const directValidation = isValidAppointmentDateTimeStr(dateStr);
                
                if (!directValidation.valid) {
                    showAlert('error', directValidation.message);
                    return;
                }
                
                openQuickAppointmentForm(info.dateStr, '08:00');
            },
            eventDrop: function(info) {
                handleEventDrop(info);
            },
            eventDragStart: function(info) {
                // Check if appointment can be dragged
                const props = info.event.extendedProps;
                
                // Prevent dragging completed appointments
                if (props.status === 'completed') {
                    MessageModal.error('Completed appointments cannot be rescheduled.', {
                        title: 'Cannot Reschedule - Completed'
                    });
                    return false; // Prevent drag
                }
                
                // Prevent dragging cancelled appointments
                if (props.status === 'cancelled' || props.approval_status === 'rejected') {
                    MessageModal.error('Cancelled or rejected appointments cannot be rescheduled.', {
                        title: 'Cannot Reschedule - Cancelled'
                    });
                    return false; // Prevent drag
                }
                
                // Add visual feedback when drag starts
                $(info.el).addClass('fc-event-dragging');
                
                // Show helper message
                if ($('.drag-helper').length === 0) {
                    $('.fc-toolbar').after(
                        '<div class="drag-helper alert alert-info mt-2">' +
                        '<i class="fas fa-hand-rock mr-2"></i>' +
                        'Drag the appointment to a new date/time to reschedule. ' +
                        'Valid times: Mon-Fri, 8AM-12PM & 1PM-5PM' +
                        '</div>'
                    );
                }
            },
            eventDragStop: function(info) {
                // Remove visual feedback when drag stops
                $(info.el).removeClass('fc-event-dragging');
                
                // Remove helper message
                $('.drag-helper').fadeOut(300, function() {
                    $(this).remove();
                });
            },
            eventDidMount: function(info) {
                // Add custom styling and tooltips
                const event = info.event;
                const props = event.extendedProps;
                
                // Handle holiday events
                if (props.type === 'holiday') {
                    // Add holiday tooltip
                    $(info.el).tooltip({
                        title: `🇵🇭 Philippine Holiday\n${props.holiday_name}\n\nNo appointments can be scheduled on this date.`,
                        placement: 'auto',
                        trigger: 'hover',
                        container: 'body'
                    });
                    
                    // Mark the day as a holiday
                    const dateStr = event.startStr;
                    const dayCell = $('.fc-daygrid-day[data-date="' + dateStr + '"]');
                    dayCell.addClass('has-holiday');
                    
                    // Add holiday indicator if not already present
                    if (dayCell.find('.holiday-overlay').length === 0) {
                        dayCell.find('.fc-daygrid-day-frame').append(
                            '<div class="holiday-overlay">Holiday</div>'
                        );
                    }
                    
                    return; // Exit early for holiday events
                }
                
                // Handle appointment events
                if (props.patient_name) {
                    // Add tooltip with detailed info
                    $(info.el).tooltip({
                        title: `${props.patient_name}\n${props.appointment_time}\nReason: ${props.reason}\nStatus: ${props.approval_status}`,
                        placement: 'auto',
                        trigger: 'hover',
                        container: 'body'
                    });
                }
                
                // Add status-specific classes for enhanced visibility
                $(info.el).removeClass('status-pending status-approved status-overdue status-reschedule status-completed status-cancelled');
                
                // Apply status classes based on appointment status
                if (props.approval_status === 'pending') {
                    $(info.el).addClass('status-pending');
                } else if (props.approval_status === 'approved') {
                    // Check if overdue
                    const now = new Date();
                    const appointmentDate = new Date(info.event.start);
                    if (appointmentDate < now && props.status !== 'completed') {
                        $(info.el).addClass('status-overdue');
                    } else {
                        $(info.el).addClass('status-approved');
                    }
                } else if (props.approval_status === 'rejected' || props.status === 'cancelled') {
                    $(info.el).addClass('status-cancelled');
                }
                
                // Handle reschedule requests
                if (props.reschedule_status === 'pending') {
                    $(info.el).addClass('status-reschedule');
                }
                
                // Handle completed appointments
                if (props.status === 'completed') {
                    $(info.el).addClass('status-completed');
                    // Make completed appointments non-draggable
                    $(info.el).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.8'
                    });
                    // Disable dragging
                    info.event.setProp('editable', false);
                }
                
                // Handle cancelled appointments - make them non-draggable
                if (props.status === 'cancelled' || props.approval_status === 'rejected') {
                    $(info.el).css({
                        'cursor': 'not-allowed',
                        'opacity': '0.7',
                        'pointer-events': 'auto' // Still allow clicking for details
                    });
                    // Disable dragging
                    info.event.setProp('editable', false);
                }
                
                // Enhance text visibility with additional attributes
                $(info.el).attr('data-status', props.approval_status);
                $(info.el).attr('data-appointment-status', props.status);
                
                // Add ARIA labels for accessibility
                $(info.el).attr('aria-label', `Appointment with ${props.patient_name} at ${props.appointment_time}. Status: ${props.approval_status}`);
                
                // FORCE style changes with JavaScript (override FullCalendar's inline styles)
                if (props.approval_status === 'pending') {
                    setTimeout(function() {
                        // Apply styles directly to the element
                        $(info.el).css({
                            'background': '#ffc107',
                            'background-color': '#ffc107',
                            'color': '#000000',
                            'font-weight': '900',
                            'border': '3px solid #e0a800'
                        });
                        
                        // Force text color on all child elements
                        $(info.el).find('*').css({
                            'color': '#000000',
                            'background': 'transparent',
                            'font-weight': '900'
                        });
                    }, 50);
                }
            },
            eventMouseEnter: function(info) {
                $(info.el).addClass('appointment-hover');
            },
            eventMouseLeave: function(info) {
                $(info.el).removeClass('appointment-hover');
            },
            height: 'auto',
            eventDisplay: 'block',
            displayEventTime: true,
            allDaySlot: false,
            dayMaxEventRows: 3,
            moreLinkClick: 'popover',
            weekNumbers: false,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: false,
                hour12: true
            }
        });
        
        calendar.render();
        
        // Handle view changes from toolbar
        calendar.on('viewChange', function(view) {
            $('#calendarView').val(view.type);
        });
        
        // Force text visibility after events are rendered
        calendar.on('eventsSet', function() {
            fixPendingAppointmentVisibility();
            // Add slight delay to ensure DOM is updated
            setTimeout(markHolidayDays, 50);
        });
        
        // Mark holiday days when view changes
        calendar.on('datesSet', function() {
            setTimeout(markHolidayDays, 100);
        });
    }
    
    // Philippine Holidays (Extended with dynamic calculation)
    const philippineHolidays = {
        2024: [
            '2024-01-01', // New Year's Day
            '2024-02-10', // Chinese New Year
            '2024-02-25', // EDSA People Power Revolution Anniversary
            '2024-03-28', // Maundy Thursday
            '2024-03-29', // Good Friday
            '2024-04-09', // Araw ng Kagitingan (Day of Valor)
            '2024-05-01', // Labor Day
            '2024-06-12', // Independence Day
            '2024-08-21', // Ninoy Aquino Day
            '2024-08-26', // National Heroes Day
            '2024-11-01', // All Saints' Day
            '2024-11-30', // Bonifacio Day
            '2024-12-25', // Christmas Day
            '2024-12-30', // Rizal Day
            '2024-12-31'  // New Year's Eve
        ],
        2025: [
            '2025-01-01', // New Year's Day
            '2025-01-29', // Chinese New Year
            '2025-02-25', // EDSA People Power Revolution Anniversary
            '2025-04-17', // Maundy Thursday
            '2025-04-18', // Good Friday
            '2025-04-09', // Araw ng Kagitingan (Day of Valor)
            '2025-05-01', // Labor Day
            '2025-06-12', // Independence Day
            '2025-08-21', // Ninoy Aquino Day
            '2025-08-25', // National Heroes Day
            '2025-11-01', // All Saints' Day
            '2025-11-30', // Bonifacio Day
            '2025-12-25', // Christmas Day
            '2025-12-30', // Rizal Day
            '2025-12-31'  // New Year's Eve
        ],
        2026: [
            '2026-01-01', '2026-02-17', '2026-02-25', '2026-04-02', '2026-04-03',
            '2026-04-09', '2026-05-01', '2026-06-12', '2026-08-21', '2026-08-31',
            '2026-11-01', '2026-11-30', '2026-12-25', '2026-12-30', '2026-12-31'
        ],
        2027: [
            '2027-01-01', '2027-02-06', '2027-02-25', '2027-03-25', '2027-03-26',
            '2027-04-09', '2027-05-01', '2027-06-12', '2027-08-21', '2027-08-30',
            '2027-11-01', '2027-11-30', '2027-12-25', '2027-12-30', '2027-12-31'
        ],
        2028: [
            '2028-01-01', '2028-01-26', '2028-02-25', '2028-04-13', '2028-04-14',
            '2028-04-09', '2028-05-01', '2028-06-12', '2028-08-21', '2028-08-28',
            '2028-11-01', '2028-11-30', '2028-12-25', '2028-12-30', '2028-12-31'
        ],
        2029: [
            '2029-01-01', '2029-02-13', '2029-02-25', '2029-03-29', '2029-03-30',
            '2029-04-09', '2029-05-01', '2029-06-12', '2029-08-21', '2029-08-27',
            '2029-11-01', '2029-11-30', '2029-12-25', '2029-12-30', '2029-12-31'
        ],
        2030: [
            '2030-01-01', '2030-02-03', '2030-02-25', '2030-04-18', '2030-04-19',
            '2030-04-09', '2030-05-01', '2030-06-12', '2030-08-21', '2030-08-26',
            '2030-11-01', '2030-11-30', '2030-12-25', '2030-12-30', '2030-12-31'
        ]
    };
    
    // Function to generate holidays for years not in the static list
    function generateHolidaysForYear(year) {
        return [
            `${year}-01-01`, // New Year's Day
            `${year}-02-25`, // EDSA People Power Revolution Anniversary
            `${year}-04-09`, // Araw ng Kagitingan (Day of Valor)
            `${year}-05-01`, // Labor Day
            `${year}-06-12`, // Independence Day
            `${year}-08-21`, // Ninoy Aquino Day
            `${year}-11-01`, // All Saints' Day
            `${year}-11-30`, // Bonifacio Day
            `${year}-12-25`, // Christmas Day
            `${year}-12-30`, // Rizal Day
            `${year}-12-31`  // New Year's Eve
            // Note: Variable holidays like Chinese New Year, Maundy Thursday, Good Friday, National Heroes Day
            // would need more complex calculation or manual updates
        ];
    }
    
    // Function to check if a date is a Philippine holiday
    function isPhilippineHoliday(date) {
        const year = date.getFullYear();
        const dateStr = date.toISOString().split('T')[0];
        
        // Check if we have specific holidays for this year
        if (philippineHolidays[year]) {
            return philippineHolidays[year].includes(dateStr);
        }
        
        // For years not in our list, use generated holidays (fixed dates only)
        if (year > 2030) {
            const generatedHolidays = generateHolidaysForYear(year);
            return generatedHolidays.includes(dateStr);
        }
        
        return false;
    }
    
    // Function to get holiday name from date
    function getHolidayName(dateStr) {
        const holidayNames = {
            '01-01': 'New Year\'s Day',
            '02-10': 'Chinese New Year', '01-29': 'Chinese New Year',
            '02-25': 'EDSA People Power Revolution',
            '03-28': 'Maundy Thursday', '04-17': 'Maundy Thursday',
            '03-29': 'Good Friday', '04-18': 'Good Friday',
            '04-09': 'Araw ng Kagitingan',
            '05-01': 'Labor Day',
            '06-12': 'Independence Day',
            '08-21': 'Ninoy Aquino Day',
            '08-26': 'National Heroes Day', '08-25': 'National Heroes Day',
            '11-01': 'All Saints\' Day',
            '11-30': 'Bonifacio Day',
            '12-25': 'Christmas Day',
            '12-30': 'Rizal Day',
            '12-31': 'New Year\'s Eve'
        };
        
        const monthDay = dateStr.substring(5); // Get MM-DD from YYYY-MM-DD
        return holidayNames[monthDay] || 'Holiday';
    }
    
    // Function to get holidays in a date range for calendar display
    function getHolidaysInRange(startStr, endStr) {
        const holidays = [];
        const startDate = new Date(startStr);
        const endDate = new Date(endStr);
        
        // Check each year in the range
        for (let year = startDate.getFullYear(); year <= endDate.getFullYear(); year++) {
            let yearHolidays = [];
            
            if (philippineHolidays[year]) {
                yearHolidays = philippineHolidays[year];
            } else if (year > 2030) {
                // Generate holidays for future years
                yearHolidays = generateHolidaysForYear(year);
            }
            
            yearHolidays.forEach(holidayDate => {
                const date = new Date(holidayDate);
                if (date >= startDate && date <= endDate) {
                    holidays.push({
                        id: 'holiday-' + holidayDate,
                        title: '🇵🇭 ' + getHolidayName(holidayDate),
                        date: holidayDate,
                        allDay: true,
                        display: 'background',
                        backgroundColor: '#ffebee',
                        borderColor: '#f44336',
                        textColor: '#c62828',
                        classNames: ['holiday-event'],
                        editable: false,
                        startEditable: false,
                        durationEditable: false,
                        extendedProps: {
                            type: 'holiday',
                            holiday_name: getHolidayName(holidayDate)
                        }
                    });
                }
            });
        }
        
        return holidays;
    }
    
    // Function to mark holiday days visually
    function markHolidayDays() {
        // Get current calendar view dates
        const calendarApi = calendar;
        if (!calendarApi) return;
        
        const currentStart = calendarApi.view.activeStart;
        const currentEnd = calendarApi.view.activeEnd;
        
        // Check each day in the current view
        for (let year = currentStart.getFullYear(); year <= currentEnd.getFullYear(); year++) {
            if (philippineHolidays[year]) {
                philippineHolidays[year].forEach(holidayDateStr => {
                    const holidayDate = new Date(holidayDateStr);
                    if (holidayDate >= currentStart && holidayDate < currentEnd) {
                        const dateStr = holidayDateStr;
                        const dayCell = $('.fc-daygrid-day[data-date="' + dateStr + '"]');
                        
                        if (dayCell.length > 0) {
                            dayCell.addClass('has-holiday');
                            
                            // Add holiday overlay if not present
                            if (dayCell.find('.holiday-overlay').length === 0) {
                                const holidayName = getHolidayName(dateStr);
                                dayCell.find('.fc-daygrid-day-frame').append(
                                    `<div class="holiday-overlay" title="${holidayName}">Holiday</div>`
                                );
                            }
                            
                            // Add tooltip to the entire day cell
                            const holidayName = getHolidayName(dateStr);
                            dayCell.attr('title', `🇵🇭 ${holidayName} - No appointments can be scheduled`);
                        }
                    }
                });
            }
        }
    }
    
    // Function to check if time is during valid school hours
    function isValidSchoolTime(timeStr) {
        const time = timeStr.split(':');
        const hours = parseInt(time[0]);
        const minutes = parseInt(time[1]);
        const totalMinutes = hours * 60 + minutes;
        
        // Morning session: 8:00 AM - 12:00 PM (480-720 minutes)
        const morningStart = 8 * 60; // 480
        const morningEnd = 12 * 60;  // 720
        
        // Afternoon session: 1:00 PM - 5:00 PM (780-1020 minutes)
        const afternoonStart = 13 * 60; // 780
        const afternoonEnd = 17 * 60;   // 1020
        
        return (totalMinutes >= morningStart && totalMinutes < morningEnd) ||
               (totalMinutes >= afternoonStart && totalMinutes < afternoonEnd);
    }
    
    // Function to check if date/time is valid for appointments (using Date objects)
    function isValidAppointmentDateTime(dateTime) {
        const date = new Date(dateTime);
        const dayOfWeek = date.getDay();
        
        // Check if it's Monday-Friday (1-5)
        if (dayOfWeek < 1 || dayOfWeek > 5) {
            return { valid: false, message: 'Appointments can only be scheduled Monday through Friday.' };
        }
        
        // Check if it's a holiday
        if (isPhilippineHoliday(date)) {
            return { valid: false, message: 'Appointments cannot be scheduled on Philippine holidays.' };
        }
        
        // Check if it's in the past
        if (date < new Date()) {
            return { valid: false, message: 'Cannot schedule appointments in the past.' };
        }
        
        return { valid: true, message: '' };
    }
    
    // Function to check if date string is valid for appointments (no timezone conversion)
    function isValidAppointmentDateTimeStr(dateStr) {
        // Parse date components directly from string to avoid timezone issues
        const [year, month, day] = dateStr.split('-').map(Number);
        const date = new Date(year, month - 1, day); // month is 0-indexed in Date constructor
        const dayOfWeek = date.getDay();
        
        // Check if it's Monday-Friday (1-5)
        if (dayOfWeek < 1 || dayOfWeek > 5) {
            return { valid: false, message: 'Appointments can only be scheduled Monday through Friday.' };
        }
        
        // Check if it's a holiday using direct string comparison
        const yearNum = parseInt(dateStr.substring(0, 4));
        let isHoliday = false;
        
        if (philippineHolidays[yearNum]) {
            isHoliday = philippineHolidays[yearNum].includes(dateStr);
        } else if (yearNum > 2030) {
            const generatedHolidays = generateHolidaysForYear(yearNum);
            isHoliday = generatedHolidays.includes(dateStr);
        }
        
        if (isHoliday) {
            return { valid: false, message: 'Appointments cannot be scheduled on Philippine holidays.' };
        }
        
        // Check if it's in the past
        const now = new Date();
        const nowStr = now.toISOString().split('T')[0];
        if (dateStr < nowStr) {
            return { valid: false, message: 'Cannot schedule appointments in the past.' };
        }
        
        return { valid: true, message: '' };
    }
    
    // Function to force text visibility on pending appointments
    function fixPendingAppointmentVisibility() {
        setTimeout(function() {
            // Find all pending appointments and force their styling
            $('.fc-event[data-status="pending"], .status-pending').each(function() {
                $(this).css({
                    'background': '#ffc107',
                    'background-color': '#ffc107',
                    'color': '#000000',
                    'font-weight': '900',
                    'border': '3px solid #e0a800'
                });
                
                // Force text color on all children
                $(this).find('*').css({
                    'color': '#000000',
                    'background': 'transparent',
                    'font-weight': '900'
                });
            });
        }, 100);
    }
    
    function showAppointmentDetails(appointmentId) {
        $('#appointmentModal').modal('show');
        
        // Show loading state
        $('#appointmentDetails').html(`
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                <p>Loading appointment details...</p>
            </div>
        `);
        $('#appointmentActions').html('');
        
        $.ajax({
            url: '{{ route("appointments.details", ":id") }}'.replace(':id', appointmentId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#appointmentDetails').html(response.html);
                    $('#appointmentActions').html(response.actions);
                } else {
                    $('#appointmentDetails').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ${response.message || 'Failed to load appointment details'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to load appointment details';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#appointmentDetails').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${errorMessage}
                    </div>
                `);
            }
        });
    }
    
    function openQuickAppointmentForm(dateStr, timeStr = '08:00') {
        $('#modalDate').val(dateStr);
        $('#modalTime').val(timeStr);
        $('#modalReason').val('');
        $('#modalPatientId').val('');
        
        // Update the modal info text
        $('.alert-info').html(`
            <i class="fas fa-info-circle mr-2"></i>
            <strong>School Hours:</strong> 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM, Monday to Friday only.<br>
            <strong>Note:</strong> No appointments on weekends or Philippine holidays.
        `);
        
        $('#appointmentFormModal').modal('show');
    }
    
    function loadPatients() {
        console.log('Loading patients...');
        
        // Show loading state in dropdown
        $('#modalPatientId').html('<option value="">Loading patients...</option>');
        
        $.ajax({
            url: '{{ route("patients.index") }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: { 
                api: true,
                status: 'active' 
            },
            success: function(response) {
                
                if (response.success && response.data) {
                    let options = '<option value="">Select Patient...</option>';
                    if (response.data.length > 0) {
                        response.data.forEach(function(patient) {
                            options += `<option value="${patient.id}">${patient.patient_name} - ${patient.email}</option>`;
                        });
                    } else {
                        options += '<option value="">No active patients found</option>';
                    }
                    $('#modalPatientId').html(options);
                } else {
                    $('#modalPatientId').html('<option value="">Error loading patients</option>');
                    console.error('Invalid response format:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load patients:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                $('#modalPatientId').html('<option value="">Failed to load patients</option>');
            }
        });
    }
    
    function saveQuickAppointment() {
        const form = $('#quickAppointmentForm');
        const formData = new FormData(form[0]);
        
        // Basic validation
        if (!$('#modalPatientId').val() || !$('#modalDate').val() || 
            !$('#modalTime').val() || !$('#modalReason').val()) {
            MessageModal.error('Please fill in all required fields', {
                title: 'Missing Information'
            });
            return;
        }
        
        // Date and holiday validation
        const appointmentDate = $('#modalDate').val();
        const appointmentTime = $('#modalTime').val();
        const appointmentDateTime = new Date(appointmentDate + 'T' + appointmentTime);
        
        const dateValidation = isValidAppointmentDateTime(appointmentDateTime);
        if (!dateValidation.valid) {
            MessageModal.error(dateValidation.message, {
                title: 'Invalid Date/Time'
            });
            return;
        }
        
        // School hours validation
        if (!isValidSchoolTime(appointmentTime)) {
            MessageModal.error('Appointments can only be scheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.', {
                title: 'Invalid Time'
            });
            return;
        }
        
        // Lunch break validation (12:00 PM - 1:00 PM)
        const time24 = appointmentTime;
        if (time24 >= '12:00' && time24 < '13:00') {
            MessageModal.error('No appointments during lunch break (12:00 PM - 1:00 PM).', {
                title: 'Invalid Time - Lunch Break'
            });
            return;
        }
        
        // Show loading state on save button
        const saveBtn = $('#saveQuickAppointment');
        const originalHtml = saveBtn.html();
        saveBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("appointments.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#appointmentFormModal').modal('hide');
                    
                    MessageModal.success('Appointment created successfully!', {
                        title: 'Appointment Created',
                        autoDismiss: 3000
                    });
                    
                    // Clear form for next use
                    form[0].reset();
                    $('#modalPatientId').val('');
                    
                    // Refresh calendar to show new appointment
                    setTimeout(() => {
                        calendar.refetchEvents();
                    }, 500);
                } else {
                    MessageModal.error(response.message || 'Failed to create appointment', {
                        title: 'Creation Failed'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to create appointment';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                MessageModal.error(errorMessage, {
                    title: 'Creation Failed'
                });
            },
            complete: function() {
                // Restore button state
                saveBtn.html(originalHtml).prop('disabled', false);
            }
        });
    }
    
    function showAlert(type, message, autoHide = true) {
        const alertConfig = {
            'success': { class: 'alert-success', icon: 'fa-check-circle' },
            'error': { class: 'alert-danger', icon: 'fa-exclamation-triangle' },
            'info': { class: 'alert-info', icon: 'fa-info-circle' },
            'warning': { class: 'alert-warning', icon: 'fa-exclamation-triangle' }
        };
        
        const config = alertConfig[type] || alertConfig['info'];
        
        const alertHtml = `
            <div class="alert ${config.class} alert-dismissible calendar-alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas ${config.icon}"></i> ${message}
            </div>
        `;
        
        // Remove any existing calendar alerts
        $('.calendar-alert').remove();
        
        $('.content-header').after(alertHtml);
        
        if (autoHide) {
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $('.calendar-alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    }
    
    function handleEventDrop(info) {
        // Get appointment details for better messaging
        const appointmentTitle = info.event.title;
        const patientName = info.event.extendedProps.patient_name || 'Patient';
        const appointmentStatus = info.event.extendedProps.status;
        const approvalStatus = info.event.extendedProps.approval_status;
        
        // Check if appointment can be rescheduled based on status
        if (appointmentStatus === 'completed') {
            MessageModal.error('Cannot reschedule a completed appointment. Completed appointments are final and cannot be modified.', {
                title: 'Cannot Reschedule - Completed Appointment'
            });
            info.revert();
            return;
        }
        
        if (appointmentStatus === 'cancelled') {
            MessageModal.error('Cannot reschedule a cancelled appointment. Please create a new appointment instead.', {
                title: 'Cannot Reschedule - Cancelled Appointment'
            });
            info.revert();
            return;
        }
        
        // Check if appointment is rejected
        if (approvalStatus === 'rejected') {
            MessageModal.error('Cannot reschedule a rejected appointment. Please create a new appointment instead.', {
                title: 'Cannot Reschedule - Rejected Appointment'
            });
            info.revert();
            return;
        }
        
        // Check if dropped on a holiday
        const droppedDateStr = info.event.startStr.split('T')[0]; // Get YYYY-MM-DD part
        const year = parseInt(droppedDateStr.substring(0, 4));
        let isHoliday = false;
        
        if (philippineHolidays[year]) {
            isHoliday = philippineHolidays[year].includes(droppedDateStr);
        } else if (year > 2030) {
            const generatedHolidays = generateHolidaysForYear(year);
            isHoliday = generatedHolidays.includes(droppedDateStr);
        }
        
        if (isHoliday) {
            const holidayName = getHolidayName(droppedDateStr);
            MessageModal.error(`🇵🇭 Cannot reschedule appointment to ${holidayName}. Please select a different date.`, {
                title: 'Invalid Date - Philippine Holiday'
            });
            info.revert();
            return;
        }
        
        // Check if dropped on weekend
        const dayOfWeek = info.event.start.getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            MessageModal.error('Cannot reschedule appointment to weekend. Please select Monday through Friday.', {
                title: 'Invalid Date - Weekend'
            });
            info.revert();
            return;
        }
        
        // Check if dropped during valid school hours
        const timeStr = info.event.start.toTimeString().substring(0, 5);
        if (!isValidSchoolTime(timeStr)) {
            MessageModal.error('Appointments can only be scheduled during school hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM.', {
                title: 'Invalid Time - Outside School Hours'
            });
            info.revert();
            return;
        }
        
        // Check if dropped on a past date
        const now = new Date();
        const droppedDateTime = new Date(info.event.start);
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // Today at 00:00:00
        const droppedDate = new Date(droppedDateTime.getFullYear(), droppedDateTime.getMonth(), droppedDateTime.getDate()); // Dropped date at 00:00:00
        
        if (droppedDate < today) {
            MessageModal.error('Cannot reschedule appointment to a past date. Please select today or a future date.', {
                title: 'Invalid Date - Past Date'
            });
            info.revert();
            return;
        }
        
        // Check if dropped on today but past time
        if (droppedDate.getTime() === today.getTime()) {
            // Same day, check if time is in the past
            if (droppedDateTime <= now) {
                MessageModal.error('Cannot reschedule appointment to a past time. Please select a future time.', {
                    title: 'Invalid Time - Past Time'
                });
                info.revert();
                return;
            }
        }
        
        // Format the new date and time for display
        const newDate = info.event.start.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const newTime = info.event.start.toLocaleTimeString([], {
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true
        });
        
        // Show enhanced confirmation dialog
        const confirmMessage = `Do you want to reschedule the appointment for <strong>${patientName}</strong>?<br><br>` +
                             `<strong>New Date:</strong> ${newDate}<br>` +
                             `<strong>New Time:</strong> ${newTime}<br><br>` +
                             `The patient will be notified via email about this change.`;
        
        MessageModal.confirm(confirmMessage, 
            function() {
                // Proceed with rescheduling
                proceedWithReschedule(info);
            },
            function() {
                // Cancel - revert the change
                info.revert();
            },
            {
                title: '📅 Reschedule Appointment',
                confirmText: 'Yes, Reschedule',
                confirmClass: 'btn-warning',
                cancelText: 'Cancel',
                icon: 'fas fa-calendar-alt'
            }
        );
        
        return; // Exit here, actual rescheduling happens in proceedWithReschedule
    }
    
    function proceedWithReschedule(info) {
        const patientName = info.event.extendedProps.patient_name || 'Patient';
        
        // Show loading state with better UI
        MessageModal.info(`Rescheduling appointment for ${patientName}...<br><br><i class="fas fa-spinner fa-spin"></i> Please wait while we update the appointment.`, {
            title: 'Processing Reschedule'
        });
        
        // Add visual indication that the event is being processed
        $(info.el).addClass('updating').css('opacity', '0.6');

        $.ajax({
            url: '{{ route("appointments.updateTime", ":id") }}'.replace(':id', info.event.id),
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                appointment_date: info.event.start.toISOString().split('T')[0],
                appointment_time: info.event.start.toTimeString().substring(0, 5)
            }),
            success: function(response) {
                // Hide the processing modal
                $('#messageModal').modal('hide');
                
                if (response.success) {
                    // Show success message with email notification info
                    MessageModal.success(
                        `Appointment successfully rescheduled for <strong>${patientName}</strong>!<br><br>` +
                        `📧 Email notification has been sent to the patient.`, 
                        {
                            title: '✓ Reschedule Complete',
                            autoDismiss: 4000
                        }
                    );
                    
                    // Refresh calendar to get updated event styling
                    calendar.refetchEvents();
                } else {
                    MessageModal.error(response.message || 'Failed to reschedule appointment. Please try again.', {
                        title: 'Reschedule Failed'
                    });
                    info.revert();
                }
            },
            error: function(xhr) {
                // Hide the processing modal
                $('#messageModal').modal('hide');
                
                let errorMessage = 'Failed to reschedule appointment. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
                
                MessageModal.error(errorMessage, {
                    title: 'Reschedule Failed'
                });
                info.revert();
            },
            complete: function() {
                // Remove loading state
                $(info.el).removeClass('updating').css('opacity', '');
            }
        });
    }
    
    // Initialize tooltips and other UI elements
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        html: true
    });
    
    // Handle modal shown events
    $('#appointmentModal').on('shown.bs.modal', function() {
        // Re-initialize tooltips for dynamically loaded content
        $(this).find('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            html: true
        });
    });
    
    $('#appointmentFormModal').on('shown.bs.modal', function() {
        // Focus on first input
        $(this).find('input:visible:first').focus();
    });
    
    // Handle appointment actions from modal
    $(document).on('click', '.approve-appointment', function() {
        const appointmentId = $(this).data('id');
        updateAppointmentStatus(appointmentId, 'approve');
    });
    
    $(document).on('click', '.reject-appointment', function() {
        const appointmentId = $(this).data('id');
        MessageModal.confirm(
            'Are you sure you want to reject this appointment?',
            function() {
                updateAppointmentStatus(appointmentId, 'reject');
            },
            null,
            {
                title: 'Reject Appointment',
                confirmText: 'Reject',
                confirmClass: 'btn-danger'
            }
        );
    });
    
    $(document).on('click', '.complete-appointment', function() {
        const appointmentId = $(this).data('id');
        MessageModal.confirm(
            'Are you sure you want to mark this appointment as completed?',
            function() {
                updateAppointmentStatus(appointmentId, 'complete');
            },
            null,
            {
                title: 'Complete Appointment',
                confirmText: 'Mark as Complete',
                confirmClass: 'btn-success'
            }
        );
    });
    
    $(document).on('click', '.cancel-appointment', function() {
        const appointmentId = $(this).data('id');
        MessageModal.confirm(
            'Are you sure you want to cancel this appointment?',
            function() {
                updateAppointmentStatus(appointmentId, 'cancel');
            },
            null,
            {
                title: 'Cancel Appointment',
                confirmText: 'Cancel Appointment',
                confirmClass: 'btn-danger'
            }
        );
    });
    
    $(document).on('click', '.reschedule-appointment', function() {
        const appointmentId = $(this).data('id');
        
        // Create a better reschedule modal
        const rescheduleModal = `
            <div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-calendar-alt mr-2"></i>Reschedule Appointment
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="rescheduleForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rescheduleDate">New Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="rescheduleDate" name="requested_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rescheduleTime">New Time <span class="text-danger">*</span></label>
                                            <select class="form-control" id="rescheduleTime" name="requested_time" required>
                                                <option value="">Select Time...</option>
                                                <optgroup label="Morning Session (8:00 AM - 12:00 PM)">
                                                    <option value="08:00">8:00 AM</option>
                                                    <option value="08:15">8:15 AM</option>
                                                    <option value="08:30">8:30 AM</option>
                                                    <option value="08:45">8:45 AM</option>
                                                    <option value="09:00">9:00 AM</option>
                                                    <option value="09:15">9:15 AM</option>
                                                    <option value="09:30">9:30 AM</option>
                                                    <option value="09:45">9:45 AM</option>
                                                    <option value="10:00">10:00 AM</option>
                                                    <option value="10:15">10:15 AM</option>
                                                    <option value="10:30">10:30 AM</option>
                                                    <option value="10:45">10:45 AM</option>
                                                    <option value="11:00">11:00 AM</option>
                                                    <option value="11:15">11:15 AM</option>
                                                    <option value="11:30">11:30 AM</option>
                                                    <option value="11:45">11:45 AM</option>
                                                </optgroup>
                                                <optgroup label="Afternoon Session (1:00 PM - 5:00 PM)">
                                                    <option value="13:00">1:00 PM</option>
                                                    <option value="13:15">1:15 PM</option>
                                                    <option value="13:30">1:30 PM</option>
                                                    <option value="13:45">1:45 PM</option>
                                                    <option value="14:00">2:00 PM</option>
                                                    <option value="14:15">2:15 PM</option>
                                                    <option value="14:30">2:30 PM</option>
                                                    <option value="14:45">2:45 PM</option>
                                                    <option value="15:00">3:00 PM</option>
                                                    <option value="15:15">3:15 PM</option>
                                                    <option value="15:30">3:30 PM</option>
                                                    <option value="15:45">3:45 PM</option>
                                                    <option value="16:00">4:00 PM</option>
                                                    <option value="16:15">4:15 PM</option>
                                                    <option value="16:30">4:30 PM</option>
                                                    <option value="16:45">4:45 PM</option>
                                                    <option value="17:00">5:00 PM</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rescheduleReason">Reason (Optional)</label>
                                    <textarea class="form-control" id="rescheduleReason" name="reschedule_reason" rows="3" placeholder="Please provide a reason for rescheduling..."></textarea>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>School Hours:</strong> 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM, Monday to Friday only.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning" id="submitReschedule">
                                <i class="fas fa-calendar-check mr-1"></i>Submit Reschedule Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal and add new one
        $('#rescheduleModal').remove();
        $('body').append(rescheduleModal);
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#rescheduleDate').attr('min', today);
        
        // Show modal
        $('#rescheduleModal').modal('show');
        
        // Handle form submission
        $('#submitReschedule').off('click').on('click', function() {
            const formData = {
                requested_date: $('#rescheduleDate').val(),
                requested_time: $('#rescheduleTime').val(),
                reschedule_reason: $('#rescheduleReason').val()
            };
            
            if (!formData.requested_date || !formData.requested_time) {
                MessageModal.error('Please select both date and time for the reschedule.', {
                    title: 'Missing Information'
                });
                return;
            }
            
            // Show loading state
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Submitting...').prop('disabled', true);
            
            $.ajax({
                url: `/appointments/${appointmentId}/reschedule`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        $('#rescheduleModal').modal('hide');
                        $('#appointmentModal').modal('hide');
                        
                        MessageModal.success(response.message || 'Reschedule request submitted successfully!', {
                            title: 'Reschedule Submitted',
                            autoDismiss: 3000
                        });
                        
                        // Refresh calendar
                        setTimeout(() => {
                            calendar.refetchEvents();
                        }, 500);
                    } else {
                        MessageModal.error(response.message || 'Failed to request reschedule', {
                            title: 'Reschedule Failed'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to request reschedule';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    MessageModal.error(errorMessage, {
                        title: 'Reschedule Failed'
                    });
                },
                complete: function() {
                    btn.html(originalHtml).prop('disabled', false);
                }
            });
        });
    });
    
    $(document).on('click', '.delete-appointment', function() {
        const appointmentId = $(this).data('id');
        MessageModal.confirm(
            'Are you sure you want to permanently delete this appointment? This action cannot be undone.',
            function() {
                // Show loading in the confirm modal
                const confirmBtn = $('.btn-danger:contains("Delete Permanently")');
                const originalText = confirmBtn.text();
                confirmBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Deleting...').prop('disabled', true);
                
                // Proceed with deletion
                $.ajax({
                    url: `/appointments/${appointmentId}/delete`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#appointmentModal').modal('hide');
                            $('#messageModal').modal('hide');
                            
                            MessageModal.success(response.message || 'Appointment deleted successfully!', {
                                title: 'Appointment Deleted',
                                autoDismiss: 3000
                            });
                            
                            // Refresh calendar
                            setTimeout(() => {
                                calendar.refetchEvents();
                            }, 500);
                        } else {
                            MessageModal.error(response.message || 'Failed to delete appointment', {
                                title: 'Delete Failed'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete appointment';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        MessageModal.error(errorMessage, {
                            title: 'Delete Failed'
                        });
                    },
                    complete: function() {
                        confirmBtn.html(originalText).prop('disabled', false);
                    }
                });
            },
            null,
            {
                title: 'Delete Appointment',
                confirmText: 'Delete Permanently',
                confirmClass: 'btn-danger'
            }
        );
    });
    
    // Handle form validation
    $('#quickAppointmentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Basic client-side validation
        const patientId = $('#modalPatientId').val();
        const date = $('#modalDate').val();
        const time = $('#modalTime').val();
        const reason = $('#modalReason').val();
        
        if (!patientId || !date || !time || !reason) {
            showAlert('error', 'Please fill in all required fields.');
            return false;
        }
        
        // Check if date is not in the past
        const selectedDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (selectedDateTime <= now) {
            showAlert('error', 'Please select a future date and time.');
        return false;
        }
        
        saveQuickAppointment();
    });
    
    // Add enhanced logging and error handling
    console.log('Calendar JavaScript loaded successfully!');
    
    // Add global error handler for AJAX requests
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:', {
            url: settings.url,
            type: settings.type,
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText,
            error: thrownError
        });
    });
    
    // Add success handler for debugging
    $(document).ajaxSuccess(function(event, xhr, settings) {
        console.log('AJAX Success:', {
            url: settings.url,
            type: settings.type,
            status: xhr.status
        });
    });
    
    function updateAppointmentStatus(appointmentId, action) {
        // Show loading state
        const actionBtn = $(`.${action}-appointment[data-id="${appointmentId}"]`);
        const originalHtml = actionBtn.html();
        actionBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Processing...').prop('disabled', true);
        
        $.ajax({
            url: `/appointments/${appointmentId}/${action}`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Close modal and refresh calendar
                    $('#appointmentModal').modal('hide');
                    
                    // Show success message with auto-dismiss
                    MessageModal.success(response.message || 'Appointment updated successfully!', {
                        title: 'Action Completed',
                        autoDismiss: 3000
                    });
                    
                    // Refresh calendar to show updated status
                    setTimeout(() => {
                        calendar.refetchEvents();
                    }, 500);
                } else {
                    MessageModal.error(response.message || 'Failed to update appointment', {
                        title: 'Update Failed'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update appointment';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                MessageModal.error(errorMessage, {
                    title: 'Update Failed'
                });
            },
            complete: function() {
                // Restore button state
                actionBtn.html(originalHtml).prop('disabled', false);
            }
        });
    }
});
</script>
@endsection