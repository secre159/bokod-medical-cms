@extends('adminlte::page')

@section('title', 'Appointments | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Appointment Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Appointments</li>
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

    <!-- Statistics Row -->
    <div class="row mb-3">
        <div class="col-lg-2 col-6">
            <a href="{{ route('appointments.index', ['status' => 'active']) }}" class="small-box-link">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Active</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-6">
            <a href="{{ route('appointments.index', ['date_filter' => 'today']) }}" class="small-box-link">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['today'] }}</h3>
                        <p>Today's</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-6">
            <a href="{{ route('appointments.index', ['approval' => 'pending']) }}" class="small-box-link">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pending_approval'] }}</h3>
                        <p>Pending Approval</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-6">
            <a href="{{ route('appointments.index', ['reschedule_filter' => 'pending']) }}" class="small-box-link">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['pending_reschedule'] }}</h3>
                        <p>Reschedules</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-redo"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-6">
            <a href="{{ route('appointments.index', ['date_filter' => 'overdue']) }}" class="small-box-link">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['overdue'] }}</h3>
                        <p>Overdue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $appointments->total() }}</h3>
                    <p>Showing</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card {{ (request('status') || request('approval') || request('date_filter') || request('reschedule_filter') || request('search')) ? '' : 'collapsed-card' }}" id="filtersCard">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filters & Search</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-card-widget="collapse" id="filterToggle" title="Toggle Filters">
                    <i class="fas {{ (request('status') || request('approval') || request('date_filter') || request('reschedule_filter') || request('search')) ? 'fa-minus' : 'fa-plus' }}" id="toggleIcon"></i>
                    <span class="ml-1" id="toggleText">{{ (request('status') || request('approval') || request('date_filter') || request('reschedule_filter') || request('search')) ? 'Hide' : 'Show' }}</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('appointments.index') }}" id="filterForm">
                <!-- Active Filters Display Area -->
                <div id="activeFiltersContainer"></div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="approval">Approval Status</label>
                            <select name="approval" id="approval" class="form-control">
                                <option value="">All Approvals</option>
                                <option value="pending" {{ request('approval') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('approval') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('approval') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_filter">Date Filter</label>
                            <select name="date_filter" id="date_filter" class="form-control">
                                <option value="">All Dates</option>
                                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="overdue" {{ request('date_filter') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="reschedule_filter">Reschedule</label>
                            <select name="reschedule_filter" id="reschedule_filter" class="form-control">
                                <option value="">All</option>
                                <option value="pending" {{ request('reschedule_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="none" {{ request('reschedule_filter') == 'none' ? 'selected' : '' }}>No Reschedule</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Patient name, email, phone..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="applyFiltersBtn">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary" id="clearFiltersBtn">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                        <div class="float-right">
                            <small class="text-muted">Showing {{ $appointments->total() }} appointment(s)</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Appointments Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt mr-2"></i>Appointments List
            </h3>
            <div class="card-tools">
                <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>Schedule New Appointment
                </a>
                <a href="{{ route('appointments.calendar') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-calendar mr-1"></i>Calendar View
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if($appointments->count() > 0)
                <table class="table table-bordered table-striped" id="appointmentsTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="18%">Patient</th>
                            <th width="13%">Date & Time</th>
                            <th width="20%">Reason</th>
                            <th width="8%">Status</th>
                            <th width="8%">Approval</th>
                            <th width="13%">Reschedule</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_id }}</td>
                            <td>
                                <strong>{{ $appointment->patient->patient_name }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-envelope mr-1"></i>{{ $appointment->patient->email }}<br>
                                    <i class="fas fa-phone mr-1"></i>{{ $appointment->patient->phone_number }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>{{ $appointment->appointment_time->format('h:i A') }}
                                </small>
                                @if($appointment->isOverdue())
                                    <br><span class="badge badge-danger">Overdue</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($appointment->reason, 50) }}</td>
                            <td>
                                @if($appointment->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($appointment->status == 'completed')
                                    <span class="badge badge-primary">Completed</span>
                                @elseif($appointment->status == 'cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                    @if($appointment->cancelled_at)
                                        <br><small class="text-muted">{{ $appointment->cancelled_at->format('M d, Y') }}</small>
                                    @endif
                                    @if($appointment->cancellation_reason)
                                        <br><small class="text-danger" title="{{ $appointment->cancellation_reason }}">
                                            <i class="fas fa-info-circle mr-1"></i>{{ Str::limit($appointment->cancellation_reason, 25) }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($appointment->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($appointment->approval_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($appointment->approval_status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($appointment->reschedule_status == 'pending')
                                    <span class="badge badge-warning mb-1"><i class="fas fa-redo mr-1"></i>Pending</span>
                                    @if($appointment->requested_date && $appointment->requested_time)
                                        <br><small class="text-muted">
                                            <i class="fas fa-arrow-right mr-1"></i><strong>New:</strong><br>
                                            {{ $appointment->requested_date->format('M d, Y') }}<br>
                                            {{ $appointment->requested_time->format('h:i A') }}
                                        </small>
                                    @endif
                                    @if($appointment->reschedule_reason)
                                        <br><small class="text-info mt-1">
                                            <i class="fas fa-comment-dots mr-1"></i>{{ Str::limit($appointment->reschedule_reason, 30) }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                        @if($appointment->status == 'active')
                                            <a class="dropdown-item" href="{{ route('appointments.edit', $appointment) }}">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>
                                            @if($appointment->approval_status == 'pending')
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('appointments.approve', $appointment) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="dropdown-item" onclick="confirmApproval(this.closest('form'))">
                                                        <i class="fas fa-check mr-2 text-success"></i>Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('appointments.reject', $appointment) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="dropdown-item" onclick="confirmRejection(this.closest('form'))">
                                                        <i class="fas fa-times mr-2 text-danger"></i>Reject
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger" onclick="confirmCancellation(this.closest('form'))">
                                                    <i class="fas fa-ban mr-2"></i>Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Links -->
                @if($appointments->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-sm text-muted mb-0">
                                Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }} 
                                of {{ $appointments->total() }} results
                            </p>
                        </div>
                        <div>
                            {{ $appointments->appends(request()->query())->links('custom.simple-pagination') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No appointments found</h5>
                    <p class="text-muted">Try adjusting your search criteria or add a new appointment.</p>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Schedule First Appointment
                    </a>
                </div>
            @endif
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
    }
    
    .dropdown-menu {
        min-width: 160px;
    }
    
    .badge {
        font-size: 0.75em;
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
    
    /* Filter feedback styles */
    #activeFiltersDisplay {
        border-left: 4px solid #17a2b8;
    }
    
    /* Loading states */
    .btn.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Filter form enhancements */
    .form-control:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }
    
    /* Active filter highlight */
    .form-control:not([value=""]) {
        border-color: #28a745;
    }
    
    /* Reschedule styling */
    .badge.badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .text-info {
        color: #17a2b8 !important;
    }
    
    /* Make reschedule column more compact */
    .table td:nth-child(7) {
        font-size: 0.85rem;
        line-height: 1.3;
    }
    
    /* Reschedule pending badge animation */
    .badge.badge-warning {
        animation: pulse-warning 2s infinite;
    }
    
    @keyframes pulse-warning {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        }
    }
    select.form-control option:checked {
        background-color: #17a2b8;
        color: white;
    }
    
    /* Clickable statistics boxes */
    .small-box-link {
        color: inherit;
        text-decoration: none;
        display: block;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    
    .small-box-link:hover {
        color: inherit;
        text-decoration: none;
        transform: translateY(-2px);
        opacity: 0.9;
    }
    
    .small-box-link:hover .small-box {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* DataTable basic styling */
    .dataTables_wrapper {
        margin-top: 0;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Appointments index: Initializing filters and DataTable');
    
    // Initialize DataTable first (if appointments exist)
    let dataTable = null;
    @if($appointments->count() > 0)
    try {
        dataTable = $('#appointmentsTable').DataTable({
            "paging": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [[ 2, "asc" ]], // Sort by date/time column
            "columnDefs": [
                { "orderable": false, "targets": [7] } // Disable sorting on actions column
            ],
            "language": {
                "emptyTable": "No appointments found",
                "zeroRecords": "No appointments match the current filters"
            }
        });
        console.log('DataTable initialized successfully');
    } catch (e) {
        console.error('DataTable initialization failed:', e);
    }
    @endif
    
    // Auto-submit form on filter change
    $('#status, #approval, #date_filter, #reschedule_filter').on('change', function() {
        console.log('Filter changed:', this.id, '=', this.value);
        
        // Show loading indication
        showLoadingState();
        
        // Submit the form with a small delay to show the loading state
        setTimeout(function() {
            $('#filterForm').submit();
        }, 200);
    });
    
    // Function to show loading state
    function showLoadingState() {
        const submitBtn = $('#applyFiltersBtn');
        const searchBtn = $('#searchBtn');
        const clearBtn = $('#clearFiltersBtn');
        
        // Store original text
        if (!submitBtn.data('original-text')) {
            submitBtn.data('original-text', submitBtn.html());
            searchBtn.data('original-text', searchBtn.html());
            clearBtn.data('original-text', clearBtn.html());
        }
        
        // Update buttons
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Applying...').prop('disabled', true);
        searchBtn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        clearBtn.addClass('disabled').css('pointer-events', 'none');
        
        // Add loading overlay to table
        if ($('#appointmentsTable').length > 0) {
            $('#appointmentsTable').parent().css('position', 'relative');
            if ($('#loadingOverlay').length === 0) {
                $('#appointmentsTable').parent().append(
                    '<div id="loadingOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000;">' +
                    '<div class="text-center">' +
                    '<i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i><br>' +
                    '<small class="text-muted">Filtering appointments...</small>' +
                    '</div>' +
                    '</div>'
                );
            }
        }
    }
    
    // Search form handling
    $('#search').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            showLoadingState();
            setTimeout(function() {
                $('#filterForm').submit();
            }, 200);
        }
    });
    
    // Search button click handling
    $('#searchBtn').on('click', function(e) {
        e.preventDefault();
        showLoadingState();
        setTimeout(function() {
            $('#filterForm').submit();
        }, 200);
    });
    
    // Apply filters button handling
    $('#applyFiltersBtn').on('click', function(e) {
        e.preventDefault();
        showLoadingState();
        setTimeout(function() {
            $('#filterForm').submit();
        }, 200);
    });
    
    // Handle filter card toggle behavior
    const hasActiveFilters = {{ (request('status') || request('approval') || request('date_filter') || request('search')) ? 'true' : 'false' }};
    
    console.log('Has active filters:', hasActiveFilters);
    
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
    
    // Add visual feedback for active filters
    function updateFilterStatus() {
        const activeFilters = [];
        
        if ($('#status').val()) activeFilters.push('Status: ' + $('#status option:selected').text());
        if ($('#approval').val()) activeFilters.push('Approval: ' + $('#approval option:selected').text());
        if ($('#date_filter').val()) activeFilters.push('Date: ' + $('#date_filter option:selected').text());
        if ($('#search').val()) activeFilters.push('Search: "' + $('#search').val() + '"');
        
        const container = $('#activeFiltersContainer');
        
        if (activeFilters.length > 0) {
            const alertHtml = 
                '<div class="alert alert-info alert-dismissible mb-3" id="activeFiltersDisplay">' +
                '<button type="button" class="close" onclick="clearAllFilters()" aria-label="Clear Filters">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<i class="fas fa-filter mr-2"></i>' +
                '<strong>Active Filters:</strong> ' +
                '<span class="ml-2">' + activeFilters.join(', ') + '</span>' +
                '</div>';
            container.html(alertHtml);
        } else {
            container.empty();
        }
    }
    
    // Function to clear all filters
    window.clearAllFilters = function() {
        $('#status').val('');
        $('#approval').val('');
        $('#date_filter').val('');
        $('#search').val('');
        $('#filterForm').submit();
    };
    
    // Update filter status on page load
    updateFilterStatus();
    
    console.log('Appointments filter system initialized');
});

// Confirmation functions for appointment actions
function confirmApproval(form) {
    modalConfirm(
        'Are you sure you want to approve this appointment?',
        'Approve Appointment',
        {
            confirmText: 'Approve',
            confirmClass: 'btn-success'
        }
    ).then(function(confirmed) {
        if (confirmed) {
            form.submit();
        }
    });
}

function confirmRejection(form) {
    modalConfirm(
        'Are you sure you want to reject this appointment?',
        'Reject Appointment',
        {
            confirmText: 'Reject',
            confirmClass: 'btn-danger'
        }
    ).then(function(confirmed) {
        if (confirmed) {
            form.submit();
        }
    });
}

function confirmCancellation(form) {
    modalConfirm(
        'Are you sure you want to cancel this appointment? This action cannot be undone.',
        'Cancel Appointment',
        {
            confirmText: 'Cancel Appointment',
            confirmClass: 'btn-danger'
        }
    ).then(function(confirmed) {
        if (confirmed) {
            form.submit();
        }
    });
}
</script>
@endsection