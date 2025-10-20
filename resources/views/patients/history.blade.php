@extends('adminlte::page')

@section('title', 'Patient History | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-history mr-2"></i>Patient Medical History
                @if($selectedPatient)
                    - {{ $selectedPatient->patient_name }}
                @endif
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                <li class="breadcrumb-item active">History</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters Card -->
    <div class="card {{ ($selectedPatient || $filters['date_from'] || $filters['date_to'] || $filters['search'] || $filters['record_type'] != 'all') ? '' : 'collapsed-card' }}" id="filtersCard">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>Filters & Search
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-card-widget="collapse" id="filterToggle" title="Toggle Filters">
                    <i class="fas {{ ($selectedPatient || $filters['date_from'] || $filters['date_to'] || $filters['search'] || $filters['record_type'] != 'all') ? 'fa-minus' : 'fa-plus' }}" id="toggleIcon"></i>
                    <span class="ml-1" id="toggleText">{{ ($selectedPatient || $filters['date_from'] || $filters['date_to'] || $filters['search'] || $filters['record_type'] != 'all') ? 'Hide' : 'Show' }}</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('patients.history') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="patient_id">Select Patient</label>
                            <select name="patient_id" id="patient_id" class="form-control" style="width: 100%;">
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ $filters['patient_id'] == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->patient_name }} ({{ $patient->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select a patient and click the search button, or it will auto-submit</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="record_type">Record Type</label>
                            <select name="record_type" id="record_type" class="form-control">
                                <option value="all" {{ $filters['record_type'] == 'all' ? 'selected' : '' }}>All Records</option>
                                <option value="visits" {{ $filters['record_type'] == 'visits' ? 'selected' : '' }}>Visits Only</option>
                                <option value="appointments" {{ $filters['record_type'] == 'appointments' ? 'selected' : '' }}>Appointments Only</option>
                                <option value="prescriptions" {{ $filters['record_type'] == 'prescriptions' ? 'selected' : '' }}>Prescriptions Only</option>
                                <option value="notes" {{ $filters['record_type'] == 'notes' ? 'selected' : '' }}>Medical Notes Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search records..." value="{{ $filters['search'] }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block" title="Search / Filter">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!$selectedPatient)
        <!-- No Patient Selected -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-search fa-4x text-muted mb-4"></i>
                <h3 class="text-muted">Select a Patient</h3>
                <p class="lead text-muted">
                    Choose a patient from the dropdown above to view their complete medical history.
                </p>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary" id="selectPatientBtn">
                        <i class="fas fa-search mr-2"></i>Choose Patient
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">Click to open patient selection dropdown above</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Patient Information Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Patient Information
                </h3>
                <div class="card-tools">
                    <a href="{{ route('patients.show', $selectedPatient) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye mr-1"></i>View Full Profile
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Name:</strong> {{ $selectedPatient->patient_name }}<br>
                        <strong>Email:</strong> {{ $selectedPatient->email }}<br>
                        <strong>Phone:</strong> {{ $selectedPatient->phone_number ?: 'Not provided' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Age:</strong> {{ $selectedPatient->age ?: 'Unknown' }} years<br>
                        <strong>Gender:</strong> {{ $selectedPatient->gender }}<br>
                        <strong>Civil Status:</strong> {{ $selectedPatient->civil_status }}
                    </div>
                    <div class="col-md-3">
                        <strong>Position:</strong> {{ $selectedPatient->position ?: 'Not specified' }}<br>
                        <strong>Course:</strong> {{ $selectedPatient->course ?: 'Not specified' }}<br>
                        <strong>BMI:</strong> {{ $selectedPatient->bmi ?: 'Not recorded' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Blood Pressure:</strong> {{ $selectedPatient->blood_pressure ?: 'Not recorded' }}<br>
                        <strong>Contact Person:</strong> {{ $selectedPatient->contact_person ?: 'Not provided' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Trends Card -->
        @if(count($healthTrends['bmi']) > 0 || count($healthTrends['temperature']) > 0 || count($healthTrends['pulse_rate']) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>Health Trends & Analysis
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $healthTrends['weight_status'] == 'Normal' ? 'success' : ($healthTrends['weight_status'] == 'Unknown' ? 'secondary' : 'warning') }}">
                                <i class="fas fa-weight"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Weight Status</span>
                                <span class="info-box-number">{{ $healthTrends['weight_status'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-thermometer-half"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Temp. Records</span>
                                <span class="info-box-number">{{ count($healthTrends['temperature']) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-heartbeat"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Pulse Records</span>
                                <span class="info-box-number">{{ count($healthTrends['pulse_rate']) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-tint"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">BP Records</span>
                                <span class="info-box-number">{{ count($healthTrends['blood_pressure']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(count($healthTrends['recent_changes']) > 0)
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Recent Changes:</h5>
                    <ul class="mb-0">
                        @foreach($healthTrends['recent_changes'] as $change)
                            <li>{{ $change }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Records Display -->
        <div class="row">
            <!-- Visit Records -->
            @if($visits->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-2"></i>Visit Records
                            <span class="badge badge-primary ml-2">{{ $visits->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($visits as $visit)
                        <div class="timeline-item mb-3 border-left pl-3" style="border-left: 3px solid #007bff !important;">
                            <div class="d-flex justify-content-between">
                                <strong class="text-primary">{{ $visit->formatted_visit_date }}</strong>
                                <span class="badge badge-{{ $visit->getVitalSignsStatus() == 'normal' ? 'success' : ($visit->getVitalSignsStatus() == 'abnormal' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($visit->getVitalSignsStatus()) }}
                                </span>
                            </div>
                            @if($visit->disease)
                                <div><strong>Disease/Condition:</strong> {{ $visit->disease }}</div>
                            @endif
                            @if($visit->symptoms)
                                <div><strong>Symptoms:</strong> {{ $visit->symptoms }}</div>
                            @endif
                            @if($visit->hasVitalSigns())
                                <div class="mt-2">
                                    <small class="text-muted"><strong>Vital Signs:</strong></small><br>
                                    @foreach($visit->vital_signs as $sign => $value)
                                        @if($value != 'Not recorded')
                                            <small>{{ $sign }}: {{ $value }}</small><br>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if($visit->notes)
                                <div class="mt-2">
                                    <small class="text-muted">{{ $visit->notes }}</small>
                                </div>
                            @endif
                            @if($visit->next_visit_date)
                                <div class="mt-2">
                                    <small class="text-info"><i class="fas fa-calendar-plus"></i> Next visit: {{ $visit->formatted_next_visit_date }}</small>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Appointment Records -->
            @if($appointments->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check mr-2"></i>Appointment History
                            <span class="badge badge-success ml-2">{{ $appointments->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($appointments as $appointment)
                        <div class="timeline-item mb-3 border-left pl-3" style="border-left: 3px solid #28a745 !important;">
                            <div class="d-flex justify-content-between">
                                <strong class="text-success">{{ $appointment->formatted_date_time }}</strong>
                                <span class="badge badge-{{ $appointment->status == 'completed' ? 'primary' : ($appointment->status == 'active' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <div><strong>Reason:</strong> {{ $appointment->reason }}</div>
                            @if($appointment->diagnosis)
                                <div><strong>Diagnosis:</strong> {{ $appointment->diagnosis }}</div>
                            @endif
                            @if($appointment->treatment_notes)
                                <div><strong>Treatment:</strong> {{ $appointment->treatment_notes }}</div>
                            @endif
                            @if($appointment->notes)
                                <div class="mt-2">
                                    <small class="text-muted">{{ $appointment->notes }}</small>
                                </div>
                            @endif
                            @if($appointment->prescriptions->count() > 0)
                                <div class="mt-2">
                                    <small class="text-info"><i class="fas fa-pills"></i> {{ $appointment->prescriptions->count() }} prescription(s) issued</small>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="row">
            <!-- Prescription Records -->
            @if($prescriptions->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-prescription-bottle-alt mr-2"></i>Medication History
                            <span class="badge badge-info ml-2">{{ $prescriptions->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($prescriptions as $prescription)
                        <div class="timeline-item mb-3 border-left pl-3" style="border-left: 3px solid #17a2b8 !important;">
                            <div class="d-flex justify-content-between">
                                <strong class="text-info">{{ $prescription->prescribed_date->format('M d, Y') }}</strong>
                                {!! $prescription->status_badge !!}
                            </div>
                            <div><strong>Medicine:</strong> {{ $prescription->medicine_name }}</div>
                            <div><strong>Dosage:</strong> {{ $prescription->dosage }}mg {{ $prescription->frequency_text }}</div>
                            <div><strong>Duration:</strong> {{ $prescription->duration_text }}</div>
                            @if($prescription->instructions)
                                <div><strong>Instructions:</strong> {{ $prescription->instructions }}</div>
                            @endif
                            @if($prescription->prescribed_by)
                                <div class="mt-2">
                                    <small class="text-muted">Prescribed by: {{ $prescription->prescribedBy->name ?? 'Unknown' }}</small>
                                </div>
                            @endif
                            @if($prescription->expiry_date)
                                <div>
                                    <small class="text-muted">Expires: {{ $prescription->expiry_date->format('M d, Y') }}</small>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Medical Notes -->
            @if($medicalNotes->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-notes-medical mr-2"></i>Medical Notes
                            <span class="badge badge-warning ml-2">{{ $medicalNotes->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($medicalNotes as $note)
                        <div class="timeline-item mb-3 border-left pl-3" style="border-left: 3px solid #ffc107 !important;">
                            <div class="d-flex justify-content-between">
                                <strong class="text-warning">{{ $note->formatted_date }}</strong>
                                <div>
                                    {!! $note->type_badge !!}
                                    {!! $note->priority_badge !!}
                                </div>
                            </div>
                            @if($note->title)
                                <div><strong>{{ $note->title }}</strong></div>
                            @endif
                            <div class="mt-1">{{ $note->content }}</div>
                            @if($note->tags && is_array($note->tags) && count($note->tags) > 0)
                                <div class="mt-2">
                                    @foreach($note->tags as $tag)
                                        <span class="badge badge-light">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-2">
                                <small class="text-muted">By: {{ $note->createdBy->name ?? 'Unknown' }}</small>
                                @if($note->is_private)
                                    <span class="badge badge-danger badge-sm ml-2">Private</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if($visits->count() == 0 && $appointments->count() == 0 && $prescriptions->count() == 0 && $medicalNotes->count() == 0)
        <!-- No Records Found -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-medical-alt fa-4x text-muted mb-4"></i>
                <h3 class="text-muted">No Medical Records Found</h3>
                <p class="lead text-muted">
                    @if($filters['date_from'] || $filters['date_to'] || $filters['search'])
                        No records match your current filter criteria. Try adjusting the date range or search terms.
                    @else
                        This patient doesn't have any medical records yet. Records will appear here once visits, appointments, or prescriptions are added to the system.
                    @endif
                </p>
                @if($filters['date_from'] || $filters['date_to'] || $filters['search'])
                <div class="mt-4">
                    <a href="{{ route('patients.history', ['patient_id' => $selectedPatient->id]) }}" class="btn btn-primary">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    @endif
@endsection

@section('css')
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" />

<style>
.info-box-number {
    font-size: 0.9rem !important;
    font-weight: 500 !important;
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.timeline-item {
    position: relative;
    padding-left: 15px;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -2px;
    top: 0;
    bottom: -20px;
    width: 3px;
    background: #e9ecef;
}

.select2-container {
    width: 100% !important;
}

.select2-container--bootstrap4 .select2-selection {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

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
}

#filtersCard.collapsed-card {
    margin-bottom: 1rem;
}

#filtersCard:not(.collapsed-card) {
    margin-bottom: 1rem;
}

.badge-sm {
    font-size: 0.75em;
}

.border-left {
    border-left-width: 3px !important;
    border-left-style: solid !important;
}

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-info { color: #17a2b8 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

/* Highlight effect for patient dropdown */
.highlight-dropdown {
    animation: highlightPulse 1s ease-in-out;
    background-color: rgba(0, 123, 255, 0.1) !important;
    border-radius: 0.25rem;
    padding: 0.5rem;
    margin: -0.5rem;
    transition: all 0.3s ease;
}

@keyframes highlightPulse {
    0% { background-color: rgba(0, 123, 255, 0.2); }
    50% { background-color: rgba(0, 123, 255, 0.4); }
    100% { background-color: rgba(0, 123, 255, 0.1); }
}

/* Button loading state styling */
#selectPatientBtn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}
</style>
@endsection

@section('js')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Patient History page loading...');
    console.log('jQuery version:', $.fn.jquery);
    
    // Check if Select2 is available and try to use it
    if (typeof $.fn.select2 !== 'undefined') {
        console.log('Select2 is available, attempting to initialize...');
        
        try {
            // Initialize Select2 for patient dropdown
            $('#patient_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Select Patient --',
                allowClear: true,
                width: '100%'
            });
            
            console.log('Select2 initialized successfully');
        } catch (error) {
            console.error('Select2 initialization failed:', error);
            console.log('Falling back to regular dropdown');
        }
    } else {
        console.log('Select2 library not found. Using regular dropdown.');
    }
    
    // Debug: Check if dropdown exists
    console.log('Patient dropdown found:', $('#patient_id').length > 0);
    console.log('Form found:', $('#filterForm').length > 0);
    
    // Auto-submit form when patient is selected
    $('#patient_id').on('change', function() {
        console.log('Patient dropdown changed');
        console.log('Selected value:', $(this).val());
        console.log('Selected text:', $(this).find('option:selected').text());
        
        if ($(this).val()) {
            console.log('Submitting form...');
            try {
                $('#filterForm').submit();
                console.log('Form submission initiated');
            } catch (error) {
                console.error('Form submission failed:', error);
            }
        }
    });
    
    // Auto-submit form when record type changes
    $('#record_type').on('change', function() {
        console.log('Record type changed:', $(this).val());
        if ($('#patient_id').val()) {
            $('#filterForm').submit();
        }
    });
    
    // Auto-submit form when dates change
    $('#date_from, #date_to').on('change', function() {
        console.log('Date filter changed');
        if ($('#patient_id').val()) {
            $('#filterForm').submit();
        }
    });
    
    // Handle search with debounce
    let searchTimeout;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        const patientId = $('#patient_id').val();
        
        console.log('Search query:', query);
        
        if (patientId) {
            searchTimeout = setTimeout(function() {
                console.log('Submitting search...');
                $('#filterForm').submit();
            }, 500);
        }
    });
    
    // Manual button for selecting patient (fallback)
    $('#selectPatientBtn').on('click', function() {
        console.log('Manual select patient button clicked');
        
        // Add visual feedback
        const btn = $(this);
        const originalHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Opening...')
           .prop('disabled', true);
        
        // First, ensure the filters card is expanded
        const filtersCard = $('#filtersCard');
        if (filtersCard.hasClass('collapsed-card')) {
            console.log('Expanding filters card...');
            filtersCard.removeClass('collapsed-card');
            $('#toggleIcon').removeClass('fa-plus').addClass('fa-minus');
            $('#toggleText').text('Hide');
        }
        
        // Highlight the patient dropdown briefly
        const patientDropdown = $('#patient_id').closest('.form-group');
        patientDropdown.addClass('highlight-dropdown');
        
        // Scroll to the filters section
        $('html, body').animate({
            scrollTop: filtersCard.offset().top - 20
        }, 300);
        
        // Wait a moment for the card to expand, then focus on dropdown
        setTimeout(function() {
            if (typeof $.fn.select2 !== 'undefined') {
                console.log('Opening Select2 dropdown...');
                try {
                    $('#patient_id').select2('open');
                } catch (error) {
                    console.error('Select2 open failed:', error);
                    $('#patient_id').focus();
                }
            } else {
                console.log('Focusing on regular dropdown...');
                $('#patient_id').focus();
            }
            
            // Restore button and remove highlight
            setTimeout(function() {
                btn.html(originalHtml).prop('disabled', false);
                patientDropdown.removeClass('highlight-dropdown');
            }, 500);
        }, 400);
    });
    
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
                sessionStorage.setItem('filterCardExpanded', 'false');
                console.log('Card collapsed');
            } else {
                icon.removeClass('fa-plus').addClass('fa-minus');
                text.text('Hide');
                sessionStorage.setItem('filterCardExpanded', 'true');
                console.log('Card expanded');
            }
        }, 100);
    });
    
    // Handle filter card behavior
    const hasActiveFilters = {{ ($selectedPatient || $filters['date_from'] || $filters['date_to'] || $filters['search'] || $filters['record_type'] != 'all') ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        console.log('Active filters or patient selected, keeping card expanded');
        $('#filtersCard').removeClass('collapsed-card');
        $('#toggleIcon').removeClass('fa-plus').addClass('fa-minus');
        $('#toggleText').text('Hide');
    }
    
    // Prevent auto-collapse when form is submitted
    $('#filterForm').on('submit', function() {
        console.log('Form submitted, preserving card state');
        // Store the current state
        const isExpanded = !$('#filtersCard').hasClass('collapsed-card');
        sessionStorage.setItem('filterCardExpanded', isExpanded);
    });
    
    // Restore card state after page load
    const savedState = sessionStorage.getItem('filterCardExpanded');
    if (savedState === 'true' && !hasActiveFilters) {
        $('#filtersCard').removeClass('collapsed-card');
        $('#toggleIcon').removeClass('fa-plus').addClass('fa-minus');
        $('#toggleText').text('Hide');
        console.log('Restored expanded state from session');
    }
    
    console.log('Patient History page loaded with advanced functionality');
});
</script>
@endsection
