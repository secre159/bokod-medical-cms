@extends('adminlte::page')

@section('title', 'Prescription Details | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Prescription Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('prescriptions.index') }}">Prescriptions</a></li>
                <li class="breadcrumb-item active">Details</li>
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

    <div class="row">
        <!-- Main Prescription Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-prescription-bottle-alt mr-2"></i>Prescription Information
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-lg 
                            @if($prescription->status == 'active')
                                @if($prescription->is_expired)
                                    badge-danger
                                @elseif($prescription->is_expiring_soon)
                                    badge-warning
                                @else
                                    badge-success
                                @endif
                            @elseif($prescription->status == 'completed')
                                badge-primary
                            @elseif($prescription->status == 'cancelled')
                                badge-secondary
                            @else
                                badge-danger
                            @endif">
                            @if($prescription->status == 'active')
                                @if($prescription->is_expired)
                                    Expired
                                @elseif($prescription->is_expiring_soon)
                                    Expiring Soon
                                @else
                                    Active
                                @endif
                            @else
                                {{ ucfirst($prescription->status) }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-pills text-primary mr-2"></i>Medicine Details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Medicine Name:</th>
                                    <td><strong>{{ $prescription->medicine_name }}</strong></td>
                                </tr>
                                @if($prescription->generic_name)
                                <tr>
                                    <th>Generic Name:</th>
                                    <td>{{ $prescription->generic_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Dosage:</th>
                                    <td><span class="badge badge-info">{{ $prescription->dosage }}</span></td>
                                </tr>
                                <tr>
                                    <th>Frequency:</th>
                                    <td>{{ $prescription->frequency_text }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td>
                                        <strong>{{ $prescription->quantity }}</strong> units
                                        @if(isset($prescription->dispensed_quantity) && $prescription->dispensed_quantity > 0)
                                            <br><small class="text-success">
                                                <i class="fas fa-check mr-1"></i>{{ $prescription->dispensed_quantity }} dispensed
                                            </small>
                                            <br><small class="text-info">
                                                {{ $prescription->quantity - $prescription->dispensed_quantity }} remaining
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="fas fa-calendar text-success mr-2"></i>Date Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Prescribed Date:</th>
                                    <td><strong>{{ $prescription->prescribed_date->format('F d, Y') }}</strong></td>
                                </tr>
                                @if($prescription->expiry_date)
                                <tr>
                                    <th>Expiry Date:</th>
                                    <td>
                                        {{ $prescription->expiry_date->format('F d, Y') }}
                                        @if($prescription->is_expired)
                                            <br><span class="badge badge-danger">Expired</span>
                                        @elseif($prescription->is_expiring_soon)
                                            <br><span class="badge badge-warning">Expiring in {{ $prescription->expiry_date->diffInDays(now()) }} days</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $prescription->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @if($prescription->updated_at != $prescription->created_at)
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $prescription->updated_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-list-alt text-warning mr-2"></i>Instructions</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $prescription->instructions }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($prescription->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-sticky-note text-info mr-2"></i>Additional Notes</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $prescription->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dispensing History -->
            @if(isset($prescription->dispensingRecords) && $prescription->dispensingRecords->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>Dispensing History
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity Dispensed</th>
                                    <th>Dispensed By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->dispensingRecords as $record)
                                <tr>
                                    <td>{{ $record->dispensed_at->format('M d, Y g:i A') }}</td>
                                    <td><span class="badge badge-success">{{ $record->quantity }}</span></td>
                                    <td>{{ $record->dispensedBy->name ?? 'System' }}</td>
                                    <td>{{ $record->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Patient Information & Actions -->
        <div class="col-md-4">
            <!-- Patient Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>Patient Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle fa-4x text-muted"></i>
                        </div>
                        <h4 class="mt-2">{{ $prescription->patient->patient_name }}</h4>
                        <p class="text-muted">{{ $prescription->patient->email }}</p>
                    </div>
                    
                    <table class="table table-sm">
                        @if($prescription->patient->phone)
                        <tr>
                            <th><i class="fas fa-phone mr-1"></i> Phone:</th>
                            <td>{{ $prescription->patient->phone }}</td>
                        </tr>
                        @endif
                        @if($prescription->patient->date_of_birth)
                        <tr>
                            <th><i class="fas fa-birthday-cake mr-1"></i> Age:</th>
                            <td>{{ $prescription->patient->date_of_birth->age }} years</td>
                        </tr>
                        @endif
                        @if($prescription->patient->gender)
                        <tr>
                            <th><i class="fas fa-venus-mars mr-1"></i> Gender:</th>
                            <td>{{ ucfirst($prescription->patient->gender) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th><i class="fas fa-calendar mr-1"></i> Patient Since:</th>
                            <td>{{ $prescription->patient->created_at->format('M Y') }}</td>
                        </tr>
                    </table>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('patients.show', $prescription->patient) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user mr-1"></i>View Patient Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Prescription Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        @if($prescription->status == 'active')
                            <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-warning btn-sm mb-2">
                                <i class="fas fa-edit mr-2"></i>Edit Prescription
                            </a>
                            
                            @if(!isset($prescription->dispensed_quantity) || $prescription->dispensed_quantity < $prescription->quantity)
                                <button type="button" class="btn btn-success btn-sm mb-2" id="dispenseBtn">
                                    <i class="fas fa-hand-holding-medical mr-2"></i>Dispense Medication
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-danger btn-sm mb-2" data-toggle="modal" data-target="#cancelModal">
                                <i class="fas fa-ban mr-2"></i>Cancel Prescription
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-info btn-sm mb-2" onclick="printPrescription()">
                            <i class="fas fa-print mr-2"></i>Print Prescription
                        </button>
                        
                        <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Prescription Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-info"><i class="fas fa-prescription"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Prescribed For</span>
                            <span class="info-box-number">
                                {{ $prescription->prescribed_date->diffInDays(now()) }} days
                            </span>
                        </div>
                    </div>
                    
                    @if($prescription->expiry_date)
                    <div class="info-box mb-2">
                        <span class="info-box-icon 
                            @if($prescription->is_expired) bg-danger
                            @elseif($prescription->is_expiring_soon) bg-warning
                            @else bg-success
                            @endif">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">
                                @if($prescription->is_expired)
                                    Expired
                                @elseif($prescription->is_expiring_soon)
                                    Expires In
                                @else
                                    Valid For
                                @endif
                            </span>
                            <span class="info-box-number">
                                @if($prescription->is_expired)
                                    {{ now()->diffInDays($prescription->expiry_date) }} days ago
                                @else
                                    {{ $prescription->expiry_date->diffInDays(now()) }} days
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-pills"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completion</span>
                            <span class="info-box-number">
                                {{ round((($prescription->dispensed_quantity ?? 0) / $prescription->quantity) * 100) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cancel Prescription</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Are you sure you want to cancel this prescription?
                    </div>
                    <p>This action cannot be undone. The prescription status will be changed to "Cancelled".</p>
                    @if(isset($prescription->dispensed_quantity) && $prescription->dispensed_quantity > 0)
                        <p><strong>Note:</strong> {{ $prescription->dispensed_quantity }} units have already been dispensed.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep Active</button>
                    <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban mr-2"></i>Yes, Cancel Prescription
                        </button>
                    </form>
                </div>
            </div>
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
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dispensing <strong>{{ $prescription->medicine_name }}</strong> for <strong>{{ $prescription->patient->patient_name }}</strong>
                        </div>
                        
                        <div class="form-group">
                            <label>Total Prescribed:</label>
                            <p class="form-control-static">
                                <strong>{{ $prescription->quantity }}</strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label>Already Dispensed:</label>
                            <p class="form-control-static">
                                <strong>{{ $prescription->dispensed_quantity ?? 0 }}</strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label>Remaining:</label>
                            <p class="form-control-static">
                                <strong>{{ $prescription->quantity - ($prescription->dispensed_quantity ?? 0) }}</strong> units
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label for="dispenseAmount">Quantity to Dispense:</label>
                            <input type="number" class="form-control" id="dispenseAmount" 
                                   min="1" max="{{ $prescription->quantity - ($prescription->dispensed_quantity ?? 0) }}" required>
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

@endsection

@section('css')
<style>
    .info-box {
        display: block;
        min-height: 70px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,.1);
        border-radius: .25rem;
        margin-bottom: 15px;
    }
    
    .info-box-icon {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
        display: block;
        float: left;
        height: 70px;
        width: 70px;
        text-align: center;
        font-size: 1.5rem;
        line-height: 70px;
        background: rgba(0,0,0,.2);
        color: rgba(255,255,255,.8);
    }
    
    .info-box-content {
        padding: 5px 10px;
        margin-left: 70px;
    }
    
    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .info-box-text {
        display: block;
        font-size: .875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 5px;
    }
    
    .user-avatar {
        margin-bottom: 15px;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Dispense medication
    $('#dispenseBtn').click(function() {
        $('#dispenseModal').modal('show');
    });
    
    // Confirm dispense
    $('#confirmDispense').click(function() {
        var amount = $('#dispenseAmount').val();
        var notes = $('#dispenseNotes').val();
        
        if (!amount || amount < 1) {
            modalError('Please enter a valid quantity to dispense', 'Invalid Quantity');
            return;
        }
        
        var remaining = {{ $prescription->quantity - ($prescription->dispensed_quantity ?? 0) }};
        if (parseInt(amount) > remaining) {
            modalError('Cannot dispense more than remaining quantity', 'Insufficient Quantity');
            return;
        }
        
        // AJAX call to dispense
        $.ajax({
            url: '{{ route("prescriptions.dispense", $prescription) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                quantity: amount,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    modalSuccess(response.message, 'Dispensed Successfully');
                    location.reload();
                } else {
                    modalError(response.error || 'Error dispensing medication', 'Dispensing Failed');
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON ? xhr.responseJSON.error : 'Error dispensing medication';
                modalError(error, 'Network Error');
            }
        });
        
        $('#dispenseModal').modal('hide');
    });
});

// Print prescription
function printPrescription() {
    var printWindow = window.open('', '_blank');
    var prescriptionContent = `
        <html>
        <head>
            <title>Prescription - {{ $prescription->patient->patient_name }}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .patient-info, .prescription-info { margin-bottom: 20px; }
                .prescription-info { border: 1px solid #ccc; padding: 15px; }
                h2 { color: #333; border-bottom: 2px solid #333; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>PRESCRIPTION</h1>
                <p>Bokod CMS - Medical Prescription</p>
            </div>
            
            <div class="patient-info">
                <h2>Patient Information</h2>
                <p><strong>Name:</strong> {{ $prescription->patient->patient_name }}</p>
                <p><strong>Email:</strong> {{ $prescription->patient->email }}</p>
                @if($prescription->patient->phone)
                <p><strong>Phone:</strong> {{ $prescription->patient->phone }}</p>
                @endif
                <p><strong>Date:</strong> {{ $prescription->prescribed_date->format('F d, Y') }}</p>
            </div>
            
            <div class="prescription-info">
                <h2>Prescription Details</h2>
                <p><strong>Medicine:</strong> {{ $prescription->medicine_name }}</p>
                @if($prescription->generic_name)
                <p><strong>Generic:</strong> {{ $prescription->generic_name }}</p>
                @endif
                <p><strong>Dosage:</strong> {{ $prescription->dosage }}</p>
                <p><strong>Frequency:</strong> {{ $prescription->frequency_text }}</p>
                <p><strong>Quantity:</strong> {{ $prescription->quantity }} units</p>
                @if($prescription->expiry_date)
                <p><strong>Valid Until:</strong> {{ $prescription->expiry_date->format('F d, Y') }}</p>
                @endif
            </div>
            
            <div class="prescription-info">
                <h2>Instructions</h2>
                <p>{{ $prescription->instructions }}</p>
                @if($prescription->notes)
                <p><strong>Notes:</strong> {{ $prescription->notes }}</p>
                @endif
            </div>
            
            <div class="footer">
                <p>Generated on {{ now()->format('F d, Y g:i A') }}</p>
                <p>This prescription is valid and authorized by the medical system.</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(prescriptionContent);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection