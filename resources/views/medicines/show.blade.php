@extends('adminlte::page')

@section('title', 'Medicine Details | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Medicine Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('medicines.index') }}">Medicines</a></li>
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
        <!-- Main Medicine Information -->
        <div class="col-md-8">
            <!-- Basic Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-pills mr-2"></i>{{ $medicine->medicine_name }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-lg 
                            @if($medicine->status == 'active')
                                badge-success
                            @elseif($medicine->status == 'inactive')
                                badge-secondary
                            @else
                                badge-danger
                            @endif">
                            {{ ucfirst($medicine->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle text-primary mr-2"></i>Basic Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Medicine Name:</th>
                                    <td><strong>{{ $medicine->medicine_name }}</strong></td>
                                </tr>
                                @if($medicine->generic_name)
                                <tr>
                                    <th>Generic Name:</th>
                                    <td>{{ $medicine->generic_name }}</td>
                                </tr>
                                @endif
                                @if($medicine->brand_name)
                                <tr>
                                    <th>Brand Name:</th>
                                    <td>{{ $medicine->brand_name }}</td>
                                </tr>
                                @endif
                                @if($medicine->manufacturer)
                                <tr>
                                    <th>Manufacturer:</th>
                                    <td>{{ $medicine->manufacturer }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Category:</th>
                                    <td><span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $medicine->category)) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Dosage Form:</th>
                                    <td>{{ ucfirst($medicine->dosage_form) }}</td>
                                </tr>
                                <tr>
                                    <th>Strength:</th>
                                    <td><strong>{{ $medicine->strength }}</strong></td>
                                </tr>
                                @if($medicine->therapeutic_class)
                                <tr>
                                    <th>Therapeutic Class:</th>
                                    <td><span class="badge badge-primary">{{ $medicine->therapeutic_class }}</span></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="fas fa-boxes text-success mr-2"></i>Inventory Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Current Stock:</th>
                                    <td>
                                        <strong class="
                                            @if($medicine->stock_quantity <= ($medicine->minimum_stock ?? 10))
                                                text-danger
                                            @elseif($medicine->stock_quantity <= (($medicine->minimum_stock ?? 10) * 2))
                                                text-warning
                                            @else
                                                text-success
                                            @endif
                                        ">{{ $medicine->stock_quantity }} units</strong>
                                        @if($medicine->stock_quantity <= ($medicine->minimum_stock ?? 10))
                                            <br><small class="text-danger">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock Alert!
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Minimum Stock:</th>
                                    <td>{{ $medicine->minimum_stock ?? 10 }} units</td>
                                </tr>
                                @if($medicine->supplier)
                                <tr>
                                    <th>Supplier:</th>
                                    <td>{{ $medicine->supplier }}</td>
                                </tr>
                                @endif
                                @if($medicine->batch_number)
                                <tr>
                                    <th>Current Batch:</th>
                                    <td>{{ $medicine->batch_number }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Prescription Required:</th>
                                    <td>
                                        @if($medicine->prescription_required)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($medicine->description)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-file-alt text-info mr-2"></i>Description</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->description }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Date Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>Date Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                @if($medicine->manufacturing_date)
                                <tr>
                                    <th width="50%">Manufacturing Date:</th>
                                    <td>{{ $medicine->manufacturing_date->format('F d, Y') }}</td>
                                </tr>
                                @endif
                                @if($medicine->expiry_date)
                                <tr>
                                    <th>Expiry Date:</th>
                                    <td>
                                        {{ $medicine->expiry_date->format('F d, Y') }}
                                        @if($medicine->expiry_date->isPast())
                                            <br><span class="badge badge-danger">Expired</span>
                                        @elseif($medicine->expiry_date->startOfDay()->diffInDays(now()->startOfDay(), false) <= 30 && $medicine->expiry_date->isFuture())
                                            <br><span class="badge badge-warning">Expires in {{ now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) }} days</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="50%">Added to System:</th>
                                    <td>{{ $medicine->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @if($medicine->updated_at != $medicine->created_at)
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $medicine->updated_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information Card -->
            @if($medicine->indication || $medicine->dosage_instructions || $medicine->age_restrictions || $medicine->drug_interactions || $medicine->pregnancy_category || $medicine->warnings)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-stethoscope mr-2"></i>Medical Information
                    </h3>
                </div>
                <div class="card-body">
                    @if($medicine->indication)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><i class="fas fa-diagnoses text-info mr-2"></i>Indication</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->indication }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($medicine->dosage_instructions)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><i class="fas fa-pills text-success mr-2"></i>Dosage Instructions</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->dosage_instructions }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            @if($medicine->age_restrictions)
                            <h6><i class="fas fa-child text-warning mr-2"></i>Age Restrictions</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->age_restrictions }}
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($medicine->pregnancy_category)
                            <h6><i class="fas fa-baby text-pink mr-2"></i>Pregnancy Category</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <span class="badge badge-info">{{ $medicine->pregnancy_category }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($medicine->drug_interactions)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><i class="fas fa-exclamation-triangle text-danger mr-2"></i>Drug Interactions</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->drug_interactions }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($medicine->warnings)
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="fas fa-exclamation-circle text-danger mr-2"></i>Warnings</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->warnings }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Additional Medical Information Card -->
            @if($medicine->side_effects || $medicine->contraindications || $medicine->storage_conditions)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-medical-file mr-2"></i>Safety & Storage
                    </h3>
                </div>
                <div class="card-body">
                    @if($medicine->storage_conditions)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><i class="fas fa-thermometer-half text-primary mr-2"></i>Storage Conditions</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->storage_conditions }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($medicine->side_effects)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6><i class="fas fa-exclamation-circle text-warning mr-2"></i>Side Effects</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->side_effects }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($medicine->contraindications)
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="fas fa-ban text-danger mr-2"></i>Contraindications</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $medicine->contraindications }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Prescription Usage -->
            @if(isset($prescriptionUsage) && $prescriptionUsage->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-prescription mr-2"></i>Recent Prescriptions
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('prescriptions.index', ['search' => $medicine->medicine_name]) }}" class="btn btn-sm btn-primary">
                            View All Prescriptions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Dosage</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescriptionUsage->take(5) as $prescription)
                                <tr>
                                    <td>{{ $prescription->prescribed_date->format('M d, Y') }}</td>
                                    <td>{{ $prescription->patient->patient_name }}</td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ $prescription->quantity }}</td>
                                    <td>
                                        <span class="badge badge-{{ $prescription->status == 'active' ? 'success' : 'primary' }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Medicine Image -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image mr-2"></i>Medicine Image
                    </h3>
                </div>
                <div class="card-body text-center">
                    @if($medicine->medicine_image)
                        <img src="{{ $medicine->medicine_image }}" alt="{{ $medicine->medicine_name }}" class="img-fluid rounded medicine-image" data-fallback="true">
                    @else
                        <div class="medicine-placeholder">
                            <i class="fas fa-pills fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No image available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        @if($medicine->status == 'active')
                            <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-warning btn-sm mb-2">
                                <i class="fas fa-edit mr-2"></i>Edit Medicine
                            </a>
                            
                            <button type="button" class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#stockModal">
                                <i class="fas fa-plus mr-2"></i>Update Stock
                            </button>
                            
                            <a href="{{ route('prescriptions.create', ['medicine_id' => $medicine->id]) }}" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-prescription mr-2"></i>Create Prescription
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-info btn-sm mb-2" onclick="printMedicineInfo()">
                            <i class="fas fa-print mr-2"></i>Print Details
                        </button>
                        
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-2">
                        <span class="info-box-icon 
                            @if($medicine->stock_quantity <= ($medicine->minimum_stock ?? 10))
                                bg-danger
                            @elseif($medicine->stock_quantity <= (($medicine->minimum_stock ?? 10) * 2))
                                bg-warning
                            @else
                                bg-success
                            @endif">
                            <i class="fas fa-boxes"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Stock Level</span>
                            <span class="info-box-number">{{ $medicine->stock_quantity }}</span>
                        </div>
                    </div>
                    
                    @if(isset($prescriptionCount))
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-prescription"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Prescriptions</span>
                            <span class="info-box-number">{{ $prescriptionCount }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($medicine->expiry_date)
                    <div class="info-box">
                        <span class="info-box-icon 
                            @if($medicine->expiry_date->isPast())
                                bg-danger
                            @elseif($medicine->expiry_date->diffInDays(now()) <= 30)
                                bg-warning
                            @else
                                bg-success
                            @endif">
                            <i class="fas fa-calendar-times"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Days to Expiry</span>
                            <span class="info-box-number">
                                @if($medicine->expiry_date->isPast())
                                    Expired
                                @else
                                    {{ now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Notes -->
            @if($medicine->notes)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note mr-2"></i>Notes
                    </h3>
                </div>
                <div class="card-body">
                    <p>{{ $medicine->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div class="modal fade" id="stockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Stock</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('medicines.updateStock', $medicine) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Current stock level: <strong>{{ $medicine->stock_quantity }} units</strong>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_action">Action</label>
                            <select name="action" id="stock_action" class="form-control" required>
                                <option value="">Select Action</option>
                                <option value="add">Add Stock</option>
                                <option value="remove">Remove Stock</option>
                                <option value="set">Set Stock Level</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_quantity">Quantity</label>
                            <input type="number" name="quantity" id="stock_quantity" class="form-control" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_reason">Reason</label>
                            <textarea name="reason" id="stock_reason" class="form-control" rows="2" placeholder="Reason for stock change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-2"></i>Update Stock
                        </button>
                    </div>
                </form>
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
    
    .medicine-placeholder {
        padding: 40px 20px;
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
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
// Handle image loading errors
$(document).ready(function() {
    $('.medicine-image[data-fallback="true"]').on('error', function() {
        $(this).replaceWith('<div class="medicine-placeholder"><i class="fas fa-pills fa-4x text-muted mb-3"></i><p class="text-muted">Image not available</p></div>');
    });
});

// Print medicine information
function printMedicineInfo() {
    var printWindow = window.open('', '_blank');
    var medicineContent = `
        <html>
        <head>
            <title>Medicine Details - {{ $medicine->medicine_name }}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .medicine-info { margin-bottom: 20px; }
                .medicine-info { border: 1px solid #ccc; padding: 15px; }
                h2 { color: #333; border-bottom: 2px solid #333; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                td, th { padding: 8px; text-align: left; border: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>MEDICINE INFORMATION</h1>
                <p>Bokod CMS - Medicine Details Report</p>
            </div>
            
            <div class="medicine-info">
                <h2>Basic Information</h2>
                <table>
                    <tr><td><strong>Medicine Name:</strong></td><td>{{ $medicine->medicine_name }}</td></tr>
                    @if($medicine->generic_name)
                    <tr><td><strong>Generic Name:</strong></td><td>{{ $medicine->generic_name }}</td></tr>
                    @endif
                    @if($medicine->brand_name)
                    <tr><td><strong>Brand Name:</strong></td><td>{{ $medicine->brand_name }}</td></tr>
                    @endif
                    @if($medicine->manufacturer)
                    <tr><td><strong>Manufacturer:</strong></td><td>{{ $medicine->manufacturer }}</td></tr>
                    @endif
                    <tr><td><strong>Category:</strong></td><td>{{ ucwords(str_replace('_', ' ', $medicine->category)) }}</td></tr>
                    <tr><td><strong>Dosage Form:</strong></td><td>{{ ucfirst($medicine->dosage_form) }}</td></tr>
                    <tr><td><strong>Strength:</strong></td><td>{{ $medicine->strength }}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>{{ ucfirst($medicine->status) }}</td></tr>
                </table>
            </div>
            
            <div class="medicine-info">
                <h2>Inventory Information</h2>
                <table>
                    <tr><td><strong>Current Stock:</strong></td><td>{{ $medicine->stock_quantity }} units</td></tr>
                    <tr><td><strong>Minimum Stock:</strong></td><td>{{ $medicine->minimum_stock ?? 10 }} units</td></tr>
                    @if($medicine->therapeutic_class)
                    <tr><td><strong>Therapeutic Class:</strong></td><td>{{ $medicine->therapeutic_class }}</td></tr>
                    @endif
                    @if($medicine->pregnancy_category)
                    <tr><td><strong>Pregnancy Category:</strong></td><td>{{ $medicine->pregnancy_category }}</td></tr>
                    @endif
                    <tr><td><strong>Prescription Required:</strong></td><td>{{ $medicine->prescription_required ? 'Yes' : 'No' }}</td></tr>
                </table>
            </div>
            
            @if($medicine->description)
            <div class="medicine-info">
                <h2>Description</h2>
                <p>{{ $medicine->description }}</p>
            </div>
            @endif
            
            @if($medicine->therapeutic_class || $medicine->indication || $medicine->dosage_instructions || $medicine->age_restrictions || $medicine->drug_interactions || $medicine->pregnancy_category || $medicine->warnings)
            <div class="medicine-info">
                <h2>Medical Information</h2>
                <table>
                    @if($medicine->therapeutic_class)
                    <tr><td><strong>Therapeutic Class:</strong></td><td>{{ $medicine->therapeutic_class }}</td></tr>
                    @endif
                    @if($medicine->indication)
                    <tr><td><strong>Indication:</strong></td><td>{{ $medicine->indication }}</td></tr>
                    @endif
                    @if($medicine->dosage_instructions)
                    <tr><td><strong>Dosage Instructions:</strong></td><td>{{ $medicine->dosage_instructions }}</td></tr>
                    @endif
                    @if($medicine->age_restrictions)
                    <tr><td><strong>Age Restrictions:</strong></td><td>{{ $medicine->age_restrictions }}</td></tr>
                    @endif
                    @if($medicine->pregnancy_category)
                    <tr><td><strong>Pregnancy Category:</strong></td><td>{{ $medicine->pregnancy_category }}</td></tr>
                    @endif
                    @if($medicine->drug_interactions)
                    <tr><td><strong>Drug Interactions:</strong></td><td>{{ $medicine->drug_interactions }}</td></tr>
                    @endif
                    @if($medicine->warnings)
                    <tr><td><strong>Warnings:</strong></td><td>{{ $medicine->warnings }}</td></tr>
                    @endif
                </table>
            </div>
            @endif
            
            @if($medicine->storage_conditions)
            <div class="medicine-info">
                <h2>Storage Conditions</h2>
                <p>{{ $medicine->storage_conditions }}</p>
            </div>
            @endif
            
            @if($medicine->side_effects)
            <div class="medicine-info">
                <h2>Side Effects</h2>
                <p>{{ $medicine->side_effects }}</p>
            </div>
            @endif
            
            @if($medicine->contraindications)
            <div class="medicine-info">
                <h2>Contraindications</h2>
                <p>{{ $medicine->contraindications }}</p>
            </div>
            @endif
            
            <div class="footer">
                <p>Generated on {{ now()->format('F d, Y g:i A') }}</p>
                <p>Bokod CMS - Medicine Management System</p>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(medicineContent);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection