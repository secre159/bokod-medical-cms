@extends('adminlte::page')

@section('title', 'Prescription Management | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Prescription Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Prescriptions</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div id="prescriptionContent">
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
        <div class="col-lg-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_active'] }}</h3>
                    <p>Active Prescriptions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-prescription-bottle-alt"></i>
                </div>
                <a href="?status=active" class="small-box-footer">
                    View Active <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['completed'] }}</h3>
                    <p>Completed Prescriptions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="?status=completed" class="small-box-footer">
                    View Completed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card {{ (request('status') || request('search')) ? '' : 'collapsed-card' }}" id="filtersCard">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Search & Filter</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-card-widget="collapse" id="filterToggle" title="Toggle Filters">
                    <i class="fas {{ (request('status') || request('search')) ? 'fa-minus' : 'fa-plus' }}" id="toggleIcon"></i>
                    <span class="ml-1" id="toggleText">{{ (request('status') || request('search')) ? 'Hide' : 'Show' }}</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('prescriptions.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Patient name, medicine name, dosage, or instructions..." 
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Prescriptions Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-prescription-bottle-alt mr-2"></i>Prescriptions List
            </h3>
            <div class="card-tools">
                <a href="{{ route('prescriptions.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>New Prescription
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if($prescriptions->count() > 0)
                <table class="table table-bordered table-striped" id="prescriptionsTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Patient</th>
                            <th width="25%">Medicine & Dosage</th>
                            <th width="15%">Prescribed</th>
                            <th width="10%">Quantity</th>
                            <th width="10%">Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->id }}</td>
                            <td>
                                <strong>{{ $prescription->patient->patient_name }}</strong>
                                <br><small class="text-muted">
                                    <i class="fas fa-envelope mr-1"></i>{{ $prescription->patient->email }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-pills text-primary mr-2"></i>
                                    <div>
                                        <strong>{{ $prescription->medicine_name }}</strong>
                                        <br><small class="text-info">{{ $prescription->dosage }}</small>
                                        @if($prescription->frequency ?? false)
                                            <br><small class="text-muted">{{ $prescription->frequency_text }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $prescription->prescribed_date->format('M d, Y') }}</strong>
                                @if($prescription->expiry_date)
                                    <br><small class="text-muted">
                                        Expires: {{ $prescription->expiry_date->format('M d, Y') }}
                                    </small>
                                    @if($prescription->is_expired)
                                        <br><span class="badge badge-danger">Expired</span>
                                    @elseif($prescription->is_expiring_soon)
                                        <br><span class="badge badge-warning">Expiring Soon</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $prescription->quantity }}</span>
                                @if(isset($prescription->dispensed_quantity) && $prescription->dispensed_quantity > 0)
                                    <br><small class="text-success">
                                        <i class="fas fa-check mr-1"></i>{{ $prescription->dispensed_quantity }} dispensed
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($prescription->status == 'active')
                                    @if($prescription->is_expired)
                                        <span class="badge badge-danger">Expired</span>
                                    @elseif($prescription->is_expiring_soon)
                                        <span class="badge badge-warning">Expiring Soon</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                @elseif($prescription->status == 'completed')
                                    <span class="badge badge-primary">Completed</span>
                                @elseif($prescription->status == 'cancelled')
                                    <span class="badge badge-secondary">Cancelled</span>
                                @else
                                    <span class="badge badge-danger">Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('prescriptions.show', $prescription) }}">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                        @if($prescription->status == 'active')
                                            <a class="dropdown-item" href="{{ route('prescriptions.edit', $prescription) }}">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            @if(!isset($prescription->dispensed_quantity) || $prescription->dispensed_quantity < $prescription->quantity)
                                                <a class="dropdown-item dispense-prescription" href="#" 
                                                   data-id="{{ $prescription->id }}" 
                                                   data-patient="{{ $prescription->patient->patient_name }}"
                                                   data-medicine="{{ $prescription->medicine_name }}"
                                                   data-quantity="{{ $prescription->quantity }}"
                                                   data-dispensed="{{ $prescription->dispensed_quantity ?? 0 }}"
                                                   data-remaining="{{ $prescription->quantity - ($prescription->dispensed_quantity ?? 0) }}">
                                                    <i class="fas fa-hand-holding-medical mr-2 text-success"></i>Dispense
                                                </a>
                                            @endif
                                            <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Cancel this prescription?')">
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

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $prescriptions->firstItem() }} to {{ $prescriptions->lastItem() }} of {{ $prescriptions->total() }} results
                    </div>
                    {{ $prescriptions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-prescription-bottle-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No prescriptions found</h5>
                    <p class="text-muted">Try adjusting your search criteria or create a new prescription.</p>
                    <a href="{{ route('prescriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create First Prescription
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Dispense Modal -->
    <div class="modal fade" id="dispenseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dispense Medication</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dispenseForm">
                        <input type="hidden" id="prescriptionId">
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dispensing <strong id="dispenseMedicine"></strong> for <strong id="dispensePatient"></strong>
                        </div>
                        
                        <div class="form-group">
                            <label>Total Prescribed:</label>
                            <p class="form-control-static">
                                <strong id="totalQuantity"></strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label>Already Dispensed:</label>
                            <p class="form-control-static">
                                <strong id="dispensedQuantity"></strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label>Remaining:</label>
                            <p class="form-control-static">
                                <strong id="remainingQuantity"></strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label for="dispenseAmount">Quantity to Dispense:</label>
                            <input type="number" class="form-control" id="dispenseAmount" min="1" step="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="dispenseNotes">Notes (optional):</label>
                            <textarea class="form-control" id="dispenseNotes" rows="2" placeholder="Dispensing notes..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmDispense">
                        <i class="fas fa-check mr-2"></i>Dispense Medication
                    </button>
                </div>
            </div>
        </div>
    </div>
</div><!-- End prescriptionContent -->

@endsection

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

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
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('jQuery loaded and document ready');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Dispense buttons found:', $('.dispense-prescription').length);
    
    // Show alert function
    function showAlert(message, type) {
        var alertClass = 'alert-' + type;
        var iconClass = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
        
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas ' + iconClass + ' mr-2"></i>' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
            '</button>' +
        '</div>';
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of content
        $('#prescriptionContent').prepend(alertHtml);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        }
    }
    
    // Make showAlert globally available
    window.showAlert = showAlert;
    
    // Auto-submit form on filter change
    $('#status').change(function() {
        $('#filterForm').submit();
    });

    // DataTable for better sorting (only if DataTables is available)
    @if($prescriptions->count() > 0)
    if (typeof $.fn.DataTable !== 'undefined') {
        try {
            $('#prescriptionsTable').DataTable({
                "paging": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "order": [[ 3, "desc" ]], // Sort by prescribed date
                "columnDefs": [
                    { "orderable": false, "targets": [6] } // Disable sorting on actions column
                ]
            });
            console.log('DataTable initialized successfully');
        } catch (e) {
            console.log('DataTable initialization failed:', e);
        }
    } else {
        console.log('DataTables library not available, using regular table');
    }
    @endif
    
    // Handle filter card toggle behavior
    const hasActiveFilters = {{ (request('status') || request('search')) ? 'true' : 'false' }};
    
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

    // Dispense prescription modal
    $('.dispense-prescription').click(function(e) {
        e.preventDefault();
        console.log('Dispense button clicked');
        
        var prescriptionId = $(this).data('id');
        var patient = $(this).data('patient');
        var medicine = $(this).data('medicine');
        var quantity = $(this).data('quantity');
        var dispensed = $(this).data('dispensed');
        var remaining = $(this).data('remaining');
        
        console.log('Prescription data:', {
            id: prescriptionId,
            patient: patient,
            medicine: medicine,
            quantity: quantity,
            dispensed: dispensed,
            remaining: remaining
        });
        
        $('#prescriptionId').val(prescriptionId);
        $('#dispensePatient').text(patient);
        $('#dispenseMedicine').text(medicine);
        $('#totalQuantity').text(quantity);
        $('#dispensedQuantity').text(dispensed);
        $('#remainingQuantity').text(remaining);
        $('#dispenseAmount').attr('max', remaining).val('');
        $('#dispenseNotes').val('');
        
        $('#dispenseModal').modal('show');
    });

    // Confirm dispense
    $('#confirmDispense').click(function() {
        var prescriptionId = $('#prescriptionId').val();
        var amount = $('#dispenseAmount').val();
        var notes = $('#dispenseNotes').val();

        if (!amount || amount < 1) {
            showAlert('Please enter a valid quantity to dispense', 'danger');
            return;
        }

        var remaining = parseInt($('#remainingQuantity').text());
        if (parseInt(amount) > remaining) {
            showAlert('Cannot dispense more than remaining quantity', 'danger');
            return;
        }

        // Disable the button and show loading state
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Dispensing...');

        // Make AJAX call to dispense
        console.log('Making AJAX call to dispense:', {
            url: '/prescriptions/' + prescriptionId + '/dispense',
            quantity: amount,
            notes: notes,
            token: $('meta[name="csrf-token"]').attr('content')
        });
        
        $.ajax({
            url: '/prescriptions/' + prescriptionId + '/dispense',
            method: 'POST',
            data: {
                quantity: amount,
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#dispenseModal').modal('hide');
                    
                    // Refresh the page to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(response.message || 'Dispensing failed', 'danger');
                }
            },
                error: function(xhr) {
                    console.log('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        responseJSON: xhr.responseJSON
                    });
                    
                    var errorMessage = 'Error dispensing medication';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else {
                        errorMessage += ' (Status: ' + xhr.status + ')';
                    }
                    showAlert(errorMessage, 'danger');
                },
            complete: function() {
                // Re-enable the button
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Handle cancel prescription forms with AJAX
    $(document).on('submit', 'form[action*="prescriptions"][method="POST"]', function(e) {
        var $form = $(this);
        var actionUrl = $form.attr('action');
        
        // Only handle forms that are for DELETE method (cancel prescription)
        if ($form.find('input[name="_method"][value="DELETE"]').length > 0) {
            e.preventDefault();
            
            if (!confirm('Cancel this prescription?')) {
                return;
            }
            
            // Disable submit button to prevent double submission
            var $submitBtn = $form.find('button[type="submit"]');
            var originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Canceling...');
            
            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        // Refresh the page to show updated data
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Operation failed', 'danger');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Error performing operation';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'danger');
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }
    });
    
    // Alternative: Test with vanilla JavaScript event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('dispense-prescription') || 
            e.target.closest('.dispense-prescription')) {
            console.log('Vanilla JS: Dispense button clicked via event delegation');
            e.preventDefault();
            
            var button = e.target.closest('.dispense-prescription') || e.target;
            var prescriptionId = button.getAttribute('data-id');
            console.log('Vanilla JS: Prescription ID:', prescriptionId);
            
            // Try to trigger modal with vanilla JS if jQuery fails
            var modal = document.getElementById('dispenseModal');
            if (modal && typeof bootstrap !== 'undefined') {
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                console.log('Vanilla JS: Modal triggered with Bootstrap');
            } else if (typeof $('#dispenseModal').modal === 'function') {
                $('#dispenseModal').modal('show');
                console.log('Vanilla JS: Modal triggered with jQuery');
            } else {
                console.log('Vanilla JS: No modal system available');
                modalAlert('Dispense functionality requires modal system. Please refresh the page if modals are not working.', 'Dispense Function');
            }
        }
    });
    
    // Test if element exists
    setTimeout(function() {
        console.log('Delayed check - Dispense buttons found:', document.querySelectorAll('.dispense-prescription').length);
        console.log('Delayed check - jQuery dispense buttons:', $('.dispense-prescription').length);
    }, 1000);
});
</script>
@endsection
