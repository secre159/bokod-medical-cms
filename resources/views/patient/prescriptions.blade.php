@extends('adminlte::page')

@section('title', 'My Prescriptions | Bokod CMS')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-pills mr-2"></i>My Prescriptions
                <small class="text-muted">View your medication history</small>
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Prescriptions</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if(!auth()->user()->patient)
        <div class="alert alert-warning">
            <h4><i class="icon fas fa-exclamation-triangle"></i> Profile Incomplete!</h4>
            Your patient profile is not set up yet. Please contact the administrator to complete your registration.
        </div>
    @else
        @php
            $patient = auth()->user()->patient;
            
            // Get prescriptions directly from patient
            $allPrescriptions = $patient->prescriptions()->with(['medicine', 'appointment'])->orderBy('created_at', 'desc')->get();
            
            // Add appointment data if available
            foreach($allPrescriptions as $prescription) {
                if ($prescription->appointment) {
                    $prescription->appointment_date = $prescription->appointment->appointment_date;
                    $prescription->appointment_reason = $prescription->appointment->reason;
                }
            }
            
            $activePrescriptions = $allPrescriptions->where('status', 'active');
            $completedPrescriptions = $allPrescriptions->where('status', 'completed');
            $pendingPrescriptions = $allPrescriptions->where('status', 'pending');
        @endphp

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $activePrescriptions->count() }}</h3>
                        <p>Active Prescriptions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $allPrescriptions->count() }}</h3>
                        <p>Total Prescriptions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-prescription-bottle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $completedPrescriptions->count() }}</h3>
                        <p>Completed</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingPrescriptions->count() }}</h3>
                        <p>Pending</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('patient.appointments') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus mr-2"></i>Book Appointment
                        </a>
                        <a href="{{ route('patient.history') }}" class="btn btn-info btn-lg ml-2">
                            <i class="fas fa-history mr-2"></i>View Full History
                        </a>
                        <button type="button" class="btn btn-success btn-lg ml-2" onclick="exportPrescriptions()">
                            <i class="fas fa-download mr-2"></i>Export Prescriptions
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-2"></i>Filter Prescriptions
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterStatus">Status</label>
                                    <select class="form-control" id="filterStatus">
                                        <option value="">All Statuses</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="dispensed">Dispensed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterYear">Year</label>
                                    <select class="form-control" id="filterYear">
                                        <option value="">All Years</option>
                                        @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterMedicine">Medicine</label>
                                    <select class="form-control" id="filterMedicine">
                                        <option value="">All Medicines</option>
                                        @foreach($allPrescriptions->pluck('medicine.name')->unique()->filter() as $medicineName)
                                            <option value="{{ $medicineName }}">{{ $medicineName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                            <i class="fas fa-search mr-2"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">
                                            <i class="fas fa-times mr-2"></i>Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>My Prescriptions
                        </h3>
                    </div>
                    <div class="card-body" id="prescriptionsContent">
                        @if($allPrescriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Medicine</th>
                                            <th>Dosage</th>
                                            <th>Instructions</th>
                                            <th>Status</th>
                                            <th>Appointment Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allPrescriptions as $prescription)
                                        <tr class="prescription-row" 
                                            data-status="{{ $prescription->status }}" 
                                            data-year="{{ $prescription->created_at->format('Y') }}"
                                            data-medicine="{{ $prescription->medicine->name ?? 'N/A' }}">
                                            <td>
                                                <strong>{{ $prescription->created_at->format('M d, Y') }}</strong>
                                                <br><small class="text-muted">{{ $prescription->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $prescription->medicine->name ?? 'N/A' }}</strong>
                                                @if($prescription->medicine && $prescription->medicine->generic_name)
                                                    <br><small class="text-muted">{{ $prescription->medicine->generic_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $prescription->dosage }}</td>
                                            <td>
                                                <span title="{{ $prescription->instructions }}">
                                                    {{ Str::limit($prescription->instructions, 50) }}
                                                </span>
                                                @if($prescription->frequency)
                                                    <br><small class="text-info">{{ $prescription->frequency }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($prescription->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @elseif($prescription->status == 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @elseif($prescription->status == 'dispensed')
                                                    <span class="badge badge-info">Dispensed</span>
                                                @elseif($prescription->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($prescription->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($prescription->appointment_date)
                                                    <small class="text-muted">{{ $prescription->appointment_date->format('M d, Y') }}</small>
                                                    <br>
                                                @endif
                                                <span title="{{ $prescription->appointment_reason }}">
                                                    {{ Str::limit($prescription->appointment_reason ?? 'N/A', 40) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-pills fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Prescriptions Found</h4>
                                <p class="text-muted">You don't have any prescriptions yet. Book an appointment to get medical consultations.</p>
                                <a href="{{ route('patient.appointments') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Book Your First Appointment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-download text-success mr-2"></i>Export Started
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                    <h5>Prescription Export</h5>
                    <p class="text-muted">Your prescription history export has been started. The download should begin shortly.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> The file includes all your prescription history with dates, medicines, dosages, and status information.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
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
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .prescription-row:hover {
        background-color: #f8f9fa;
    }
    
    @media print {
        .btn, .card-tools, .breadcrumb, .content-header {
            display: none !important;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Patient prescriptions loaded');
});

function applyFilters() {
    const status = $('#filterStatus').val();
    const year = $('#filterYear').val();
    const medicine = $('#filterMedicine').val();
    
    $('.prescription-row').hide();
    
    $('.prescription-row').each(function() {
        const row = $(this);
        const rowStatus = row.data('status');
        const rowYear = row.data('year').toString();
        const rowMedicine = row.data('medicine');
        
        let showRow = true;
        
        if (status && rowStatus !== status) {
            showRow = false;
        }
        
        if (year && rowYear !== year) {
            showRow = false;
        }
        
        if (medicine && rowMedicine !== medicine) {
            showRow = false;
        }
        
        if (showRow) {
            row.show();
        }
    });
    
    // Check if any rows are visible
    if ($('.prescription-row:visible').length === 0) {
        $('#prescriptionsContent .table-responsive').hide();
        if ($('#noResultsMessage').length === 0) {
            $('#prescriptionsContent').append(
                '<div id="noResultsMessage" class="text-center py-4">' +
                '<i class="fas fa-search fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted">No Results Found</h4>' +
                '<p class="text-muted">No prescriptions match your filter criteria.</p>' +
                '</div>'
            );
        }
    } else {
        $('#prescriptionsContent .table-responsive').show();
        $('#noResultsMessage').remove();
    }
}

function clearFilters() {
    $('#filterStatus, #filterYear, #filterMedicine').val('');
    $('.prescription-row').show();
    $('#prescriptionsContent .table-responsive').show();
    $('#noResultsMessage').remove();
}

function exportPrescriptions() {
    // Create a form to submit for CSV export
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("patient.prescriptions.export") }}';
    form.style.display = 'none';
    
    // Add CSRF token
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = $('meta[name="csrf-token"]').attr('content');
    form.appendChild(csrfInput);
    
    // Append form to body and submit
    document.body.appendChild(form);
    form.submit();
    
    // Show feedback modal
    setTimeout(function() {
        $('#exportModal').modal('show');
    }, 100);
    
    // Remove form after submission
    setTimeout(function() {
        document.body.removeChild(form);
    }, 1000);
}
</script>
@endsection