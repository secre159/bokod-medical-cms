@extends('adminlte::page')

@section('title', 'Schedule New Appointment | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Schedule New Appointment</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">Schedule New</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-plus mr-2"></i>Appointment Details
            </h3>
            <div class="card-tools">
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to List
                </a>
            </div>
        </div>
        
        <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
            @csrf
            <div class="card-body">
                <div class="row">
                    <!-- Patient Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="patient_id" class="required">Patient</label>
                            <select name="patient_id" id="patient_id" class="form-control select2" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}
                                            data-email="{{ $patient->email }}" 
                                            data-phone="{{ $patient->phone_number }}"
                                            data-address="{{ $patient->address }}">
                                        {{ $patient->patient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Select the patient for this appointment
                            </small>
                        </div>
                    </div>

                    <!-- Appointment Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_date" class="required">Appointment Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" 
                                   class="form-control" value="{{ old('appointment_date') }}" required
                                   min="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Select a future date for the appointment
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Appointment Time -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_time" class="required">Appointment Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-control" required>
                                <option value="">Select Time...</option>
                                <!-- Morning Session: 8:00 AM - 12:00 PM -->
                                <optgroup label="Morning Session (8:00 AM - 12:00 PM)">
                                    <option value="08:00" {{ old('appointment_time') == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                    <option value="08:15" {{ old('appointment_time') == '08:15' ? 'selected' : '' }}>8:15 AM</option>
                                    <option value="08:30" {{ old('appointment_time') == '08:30' ? 'selected' : '' }}>8:30 AM</option>
                                    <option value="08:45" {{ old('appointment_time') == '08:45' ? 'selected' : '' }}>8:45 AM</option>
                                    <option value="09:00" {{ old('appointment_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="09:15" {{ old('appointment_time') == '09:15' ? 'selected' : '' }}>9:15 AM</option>
                                    <option value="09:30" {{ old('appointment_time') == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                                    <option value="09:45" {{ old('appointment_time') == '09:45' ? 'selected' : '' }}>9:45 AM</option>
                                    <option value="10:00" {{ old('appointment_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="10:15" {{ old('appointment_time') == '10:15' ? 'selected' : '' }}>10:15 AM</option>
                                    <option value="10:30" {{ old('appointment_time') == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                                    <option value="10:45" {{ old('appointment_time') == '10:45' ? 'selected' : '' }}>10:45 AM</option>
                                    <option value="11:00" {{ old('appointment_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="11:15" {{ old('appointment_time') == '11:15' ? 'selected' : '' }}>11:15 AM</option>
                                    <option value="11:30" {{ old('appointment_time') == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                                    <option value="11:45" {{ old('appointment_time') == '11:45' ? 'selected' : '' }}>11:45 AM</option>
                                </optgroup>
                                <!-- Afternoon Session: 1:00 PM - 5:00 PM -->
                                <optgroup label="Afternoon Session (1:00 PM - 5:00 PM)">
                                    <option value="13:00" {{ old('appointment_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="13:15" {{ old('appointment_time') == '13:15' ? 'selected' : '' }}>1:15 PM</option>
                                    <option value="13:30" {{ old('appointment_time') == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                                    <option value="13:45" {{ old('appointment_time') == '13:45' ? 'selected' : '' }}>1:45 PM</option>
                                    <option value="14:00" {{ old('appointment_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="14:15" {{ old('appointment_time') == '14:15' ? 'selected' : '' }}>2:15 PM</option>
                                    <option value="14:30" {{ old('appointment_time') == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                                    <option value="14:45" {{ old('appointment_time') == '14:45' ? 'selected' : '' }}>2:45 PM</option>
                                    <option value="15:00" {{ old('appointment_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="15:15" {{ old('appointment_time') == '15:15' ? 'selected' : '' }}>3:15 PM</option>
                                    <option value="15:30" {{ old('appointment_time') == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                                    <option value="15:45" {{ old('appointment_time') == '15:45' ? 'selected' : '' }}>3:45 PM</option>
                                    <option value="16:00" {{ old('appointment_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                    <option value="16:15" {{ old('appointment_time') == '16:15' ? 'selected' : '' }}>4:15 PM</option>
                                    <option value="16:30" {{ old('appointment_time') == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                                    <option value="16:45" {{ old('appointment_time') == '16:45' ? 'selected' : '' }}>4:45 PM</option>
                                    <option value="17:00" {{ old('appointment_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                </optgroup>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-clock mr-1"></i>School hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM (Monday to Friday)
                            </small>
                        </div>
                    </div>

                    <!-- Duration (Optional - for display purposes) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration">Estimated Duration</label>
                            <select id="duration" class="form-control">
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60" selected>1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>For scheduling reference only
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Reason for Appointment -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="reason" class="required">Reason for Appointment</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required
                                      maxlength="500" placeholder="Describe the reason for this appointment...">{{ old('reason') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="reasonCounter">0/500</span> characters used
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Patient Information Display -->
                <div id="patientInfo" class="row" style="display: none;">
                    <div class="col-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Patient Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Email:</strong><br>
                                        <span id="patientEmail" class="text-muted">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Phone:</strong><br>
                                        <span id="patientPhone" class="text-muted">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Address:</strong><br>
                                        <span id="patientAddress" class="text-muted">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conflict Checker Results -->
                <div id="conflictAlert" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Time Conflict!</strong>
                    <span id="conflictMessage"></span>
                </div>

                <!-- Available Time Suggestions -->
                <div id="suggestedTimes" class="card card-outline card-success" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Suggested Available Times</h3>
                    </div>
                    <div class="card-body">
                        <div id="timeSlots" class="row">
                            <!-- Time slots will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Schedule Appointment
                        </button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-info" id="checkAvailability" style="display: none;">
                            <i class="fas fa-search mr-2"></i>Check Availability
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('adminlte_css_pre')
<!-- Ensure jQuery is loaded first -->
<script>
    // Force jQuery loading if not available
    if (typeof window.jQuery === 'undefined' && typeof window.$ === 'undefined') {
        document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        console.log('Force loading jQuery from CDN');
    }
</script>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .required::after {
        content: " *";
        color: red;
        font-weight: bold;
    }
    
    .time-slot {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .time-slot:hover {
        background-color: #e9ecef;
        border-color: #007bff;
    }
    
    .time-slot.selected {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: 26px;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Backup jQuery Loading -->
<script>
// Comprehensive jQuery fallback and error handling
if (typeof window.$ === 'undefined' && typeof window.jQuery === 'undefined') {
    console.warn('jQuery not found, loading from CDN...');
    var jqueryScript = document.createElement('script');
    jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    jqueryScript.onload = function() {
        console.log('jQuery loaded from CDN');
        if (typeof window.initAppointmentCreate === 'function') {
            window.initAppointmentCreate();
        }
    };
    document.head.appendChild(jqueryScript);
}

// Global error handler for jQuery and JSON errors
window.addEventListener('error', function(e) {
    if (e.message.includes('$ is not defined')) {
        console.error('jQuery error caught:', e.message, 'at line', e.lineno);
        e.preventDefault();
        return true;
    }
    
    if (e.message.includes('Unexpected token') && e.message.includes('not valid JSON')) {
        console.error('JSON parse error caught:', e.message, 'at line', e.lineno);
        console.log('This is likely a data attribute trying to parse non-JSON content');
        e.preventDefault();
        return true;
    }
});

// Alternative function definitions for when jQuery fails
if (typeof window.$ === 'undefined') {
    // Mock jQuery functions to prevent errors
    window.$ = function(selector) {
        console.warn('jQuery not available, mocking selector:', selector);
        return {
            ready: function(callback) {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', callback);
                } else {
                    callback();
                }
                return this;
            },
            select2: function() { console.warn('Select2 not available'); return this; },
            on: function() { console.warn('jQuery event handler not available'); return this; },
            attr: function() { console.warn('jQuery attr not available'); return this; },
            val: function() { console.warn('jQuery val not available'); return ''; },
            text: function() { console.warn('jQuery text not available'); return this; },
            show: function() { console.warn('jQuery show not available'); return this; },
            hide: function() { console.warn('jQuery hide not available'); return this; },
            find: function() { return this; },
            trigger: function() { return this; },
            addClass: function() { return this; },
            removeClass: function() { return this; }
        };
    };
    window.$.fn = window.$().constructor.prototype;
}
</script>
<script>
// Immediate jQuery safety check
if (typeof window.$ === 'undefined') {
    console.error('jQuery not available at script execution time');
    // Create a minimal jQuery mock to prevent errors
    window.$ = function() { 
        return { 
            ready: function(fn) { 
                console.log('Mock jQuery ready called');
                setTimeout(fn, 1000); 
            }
        }; 
    };
}

// Wait for jQuery to be available
function initAppointmentCreate() {
    if (typeof $ === 'undefined') {
        console.log('jQuery not loaded, waiting...');
        setTimeout(initAppointmentCreate, 100);
        return;
    }
    
    // Comprehensive error handling wrapper
    try {
        if (typeof $ === 'undefined') {
            throw new Error('jQuery is not available');
        }
        
        $(document).ready(function() {
            console.log('Appointments create: jQuery ready executed successfully');
            
            // Safe JSON parsing helper
            window.safeJSONParse = function(str, defaultValue) {
                try {
                    return JSON.parse(str);
                } catch (e) {
                    console.warn('JSON parse error:', e.message, 'Data:', str);
                    return defaultValue || {};
                }
            };
            
            // Replace any existing JSON.parse calls with safe version
            $('[data-json]').each(function() {
                try {
                    var data = $(this).attr('data-json');
                    if (data && data.trim()) {
                        var parsed = safeJSONParse(data, null);
                        if (parsed === null) {
                            console.warn('Invalid JSON in data-json attribute:', data);
                            $(this).removeAttr('data-json');
                        }
                    }
                } catch (e) {
                    console.error('Error processing data-json attribute:', e);
                    $(this).removeAttr('data-json');
            });
            
            // Temporarily override JSON.parse for debugging
            var originalJSONParse = JSON.parse;
            JSON.parse = function(text) {
                try {
                    return originalJSONParse.call(this, text);
                } catch (e) {
                    console.error('JSON.parse failed with text:', text);
                    console.error('Error:', e.message);
                    
                    // If it looks like HTML content being parsed as JSON, return empty object
                    if (typeof text === 'string' && 
                        (text.includes('<') || text.includes('Appointment') || text.includes('html'))) {
                        console.log('Detected HTML/text content, returning empty object');
                        return {};
                    }
                    
                    // Re-throw the error if it's legitimate JSON that's malformed
                    throw e;
                }
            };
            
            // Initialize Select2
    $('#patient_id').select2({
        theme: 'bootstrap4',
        placeholder: '-- Select Patient --',
        allowClear: true
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#appointment_date').attr('min', today);

    // Character counter for reason
    $('#reason').on('input', function() {
        const length = $(this).val().length;
        $('#reasonCounter').text(length + '/500');
        
        if (length > 450) {
            $('#reasonCounter').addClass('text-warning');
        } else {
            $('#reasonCounter').removeClass('text-warning');
        }
    });

    // Patient selection change
    $('#patient_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        
        if ($(this).val()) {
            $('#patientEmail').text(selectedOption.data('email') || '-');
            $('#patientPhone').text(selectedOption.data('phone') || '-');
            $('#patientAddress').text(selectedOption.data('address') || '-');
            $('#patientInfo').show();
            $('#checkAvailability').show();
        } else {
            $('#patientInfo').hide();
            $('#checkAvailability').hide();
            $('#conflictAlert').hide();
            $('#suggestedTimes').hide();
        }
    });

    // Date/Time change - check for conflicts
    $('#appointment_date, #appointment_time').on('change', function() {
        if ($('#patient_id').val() && $('#appointment_date').val() && $('#appointment_time').val()) {
            checkAppointmentConflict();
        }
    });

    // Check availability button
    $('#checkAvailability').on('click', function() {
        if ($('#appointment_date').val()) {
            generateTimeSlots();
        } else {
            modalWarning('Please select a date first.', 'Date Required');
        }
    });

    // Time slot selection
    $(document).on('click', '.time-slot', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');
        const time = $(this).data('time');
        $('#appointment_time').val(time);
        $('#conflictAlert').hide();
    });

    // Form validation
    $('#appointmentForm').on('submit', function(e) {
        const appointmentTime = $('#appointment_time').val();
        
        if (appointmentTime) {
            const [hours, minutes] = appointmentTime.split(':');
            const hour = parseInt(hours);
            const minute = parseInt(minutes);
            const totalMinutes = hour * 60 + minute;
            
            // Morning session: 8:00 AM - 12:00 PM (480-720 minutes)
            const morningStart = 8 * 60; // 480
            const morningEnd = 12 * 60;  // 720
            
            // Afternoon session: 1:00 PM - 5:00 PM (780-1020 minutes)
            const afternoonStart = 13 * 60; // 780
            const afternoonEnd = 17 * 60 + 1;   // 1021 (to include 5:00 PM)
            
            const isValidTime = (totalMinutes >= morningStart && totalMinutes < morningEnd) ||
                               (totalMinutes >= afternoonStart && totalMinutes <= afternoonEnd);
            
            if (!isValidTime) {
                e.preventDefault();
                modalError('Please select a time during school hours: 8:00 AM - 12:00 PM or 1:00 PM - 5:00 PM.', 'Invalid Time Selection');
                return false;
            }
        }
    });

    // Initialize character counter
    $('#reason').trigger('input');

    // Check for conflicts on page load if values exist
    if ($('#patient_id').val() && $('#appointment_date').val() && $('#appointment_time').val()) {
        checkAppointmentConflict();
    }

    function checkAppointmentConflict() {
        const date = $('#appointment_date').val();
        const time = $('#appointment_time').val();
        
        if (!date || !time) return;
        
        // Simulate conflict checking (in real implementation, this would be an AJAX call)
        // For now, we'll show the conflict checker is working
        $('#conflictAlert').hide();
        
        // You would implement actual AJAX conflict checking here
        // $.ajax({
        //     url: '/appointments/check-conflict',
        //     method: 'POST',
        //     data: { date, time },
        //     success: function(response) {
        //         if (response.conflict) {
        //             showConflict(response.message);
        //         }
        //     }
        // });
    }

    function showConflict(message) {
        $('#conflictMessage').text(message);
        $('#conflictAlert').show();
        generateTimeSlots();
    }

    function generateTimeSlots() {
        const date = $('#appointment_date').val();
        if (!date) return;
        
        $('#timeSlots').empty();
        
        // Generate time slots for school hours (15-minute intervals)
        const slots = [];
        
        // Morning session: 8:00 AM - 12:00 PM
        for (let hour = 8; hour < 12; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                const timeString = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                const displayTime = formatTime(timeString);
                slots.push({ time: timeString, display: displayTime });
            }
        }
        
        // Afternoon session: 1:00 PM - 5:00 PM
        for (let hour = 13; hour <= 17; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                if (hour === 17 && minute > 0) break; // Stop after 5:00 PM
                const timeString = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                const displayTime = formatTime(timeString);
                slots.push({ time: timeString, display: displayTime });
            }
        }
        
        slots.forEach(function(slot) {
            const slotHtml = `
                <div class="col-md-2 col-sm-3 col-4 mb-2">
                    <div class="card card-outline time-slot" data-time="${slot.time}">
                        <div class="card-body text-center py-2">
                            <small>${slot.display}</small>
                        </div>
                    </div>
                </div>
            `;
            $('#timeSlots').append(slotHtml);
        });
        
        $('#suggestedTimes').show();
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour > 12 ? hour - 12 : hour;
        return displayHour + ':' + minutes + ' ' + ampm;
    }
        }); // End of $(document).ready
        
    } catch (error) {
        console.error('jQuery initialization error in appointments/create:', error);
        console.log('Falling back to basic form functionality');
        
        // Basic form fallback without jQuery
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Basic DOM ready fallback executed');
            
            // Set minimum date
            var dateInput = document.getElementById('appointment_date');
            if (dateInput) {
                var today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
            }
            
            // Basic form validation
            var form = document.getElementById('appointmentForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    var timeInput = document.getElementById('appointment_time');
                    if (timeInput && timeInput.value) {
                        var time = timeInput.value.split(':');
                        var hour = parseInt(time[0]);
                        var minute = parseInt(time[1]);
                        var totalMinutes = hour * 60 + minute;
                        
                        var validTime = (totalMinutes >= 480 && totalMinutes < 720) || 
                                       (totalMinutes >= 780 && totalMinutes <= 1021);
                        
                        if (!validTime) {
                            e.preventDefault();
                            alert('Please select a time during school hours: 8:00 AM - 12:00 PM or 1:00 PM - 5:00 PM.');
                            return false;
                        }
                    }
                });
            }
        });
    }
} // End of initAppointmentCreate function

// Initialize when page loads
initAppointmentCreate();
</script>
@endsection