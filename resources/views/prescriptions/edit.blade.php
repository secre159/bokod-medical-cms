@extends('adminlte::page')

@section('title', 'Edit Prescription | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Prescription</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('prescriptions.index') }}">Prescriptions</a></li>
                <li class="breadcrumb-item"><a href="{{ route('prescriptions.show', $prescription) }}">Details</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Status Warning -->
    @if($prescription->status !== 'active')
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Notice:</h5>
            This prescription is currently {{ $prescription->status }}. Only active prescriptions can be fully edited.
        </div>
    @endif

    <form action="{{ route('prescriptions.update', $prescription) }}" method="POST" id="prescriptionEditForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Current Prescription Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Current Prescription Info
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $prescription->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($prescription->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">ID:</th>
                                <td>#{{ $prescription->id }}</td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $prescription->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                            @if(isset($prescription->dispensed_quantity) && $prescription->dispensed_quantity > 0)
                            <tr>
                                <th>Dispensed:</th>
                                <td>
                                    <span class="badge badge-success">{{ $prescription->dispensed_quantity }}</span>
                                    out of {{ $prescription->quantity }} units
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Current Medicine:</th>
                                <td>{{ $prescription->medicine_name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-2"></i>Patient Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="patient_id">Patient <span class="text-danger">*</span></label>
                            <select name="patient_id" id="patient_id" class="form-control select2" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" 
                                            data-email="{{ $patient->email }}" 
                                            data-phone="{{ $patient->phone }}"
                                            {{ (old('patient_id', $prescription->patient_id) == $patient->id) ? 'selected' : '' }}>
                                        {{ $patient->patient_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="patientDetails" class="alert alert-info">
                            <h6><i class="fas fa-info-circle mr-1"></i>Patient Details</h6>
                            <p class="mb-1"><strong>Email:</strong> <span id="patientEmail">{{ $prescription->patient->email }}</span></p>
                            <p class="mb-0"><strong>Phone:</strong> <span id="patientPhone">{{ $prescription->patient->phone ?? '-' }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Medicine Selection -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pills mr-2"></i>Medicine Selection
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="medicine_selection_type">Medicine Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medicine_selection_type" id="from_inventory" value="inventory" 
                                       {{ $prescription->medicine_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="from_inventory">
                                    From Inventory
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medicine_selection_type" id="custom_medicine" value="custom"
                                       {{ !$prescription->medicine_id ? 'checked' : '' }}>
                                <label class="form-check-label" for="custom_medicine">
                                    Custom Medicine
                                </label>
                            </div>
                        </div>

                        <div id="inventorySelection" style="{{ !$prescription->medicine_id ? 'display: none;' : '' }}">
                            <div class="form-group">
                                <label for="medicine_id">Select Medicine</label>
                                <select name="medicine_id" id="medicine_id" class="form-control select2">
                                    <option value="">Choose from inventory</option>
                                    @foreach($medicines as $medicine)
                                        <option value="{{ $medicine->id }}" 
                                                data-name="{{ $medicine->medicine_name }}"
                                                data-generic="{{ $medicine->generic_name }}"
                                                data-brand="{{ $medicine->brand_name }}"
                                                data-strength="{{ $medicine->strength }}"
                                                data-form="{{ $medicine->dosage_form }}"
                                                data-stock="{{ $medicine->stock_quantity }}"
                                                {{ old('medicine_id', $prescription->medicine_id) == $medicine->id ? 'selected' : '' }}>
                                            {{ $medicine->medicine_name }} 
                                            @if($medicine->brand_name)
                                                ({{ $medicine->brand_name }})
                                            @endif
                                            - {{ $medicine->strength }} {{ $medicine->dosage_form }}
                                            <small>(Stock: {{ $medicine->stock_quantity }})</small>
                                        </option>
                                    @endforeach
                                </select>
                                @error('medicine_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div id="medicineInfo" class="alert alert-success" style="{{ !$prescription->medicine_id ? 'display: none;' : '' }}">
                                <h6><i class="fas fa-info-circle mr-1"></i>Medicine Details</h6>
                                <p class="mb-1"><strong>Generic:</strong> <span id="medicineGeneric">{{ $prescription->generic_name ?? '-' }}</span></p>
                                <p class="mb-1"><strong>Brand:</strong> <span id="medicineBrand">-</span></p>
                                <p class="mb-1"><strong>Strength:</strong> <span id="medicineStrength">-</span></p>
                                <p class="mb-0"><strong>Available Stock:</strong> <span id="medicineStock">-</span> units</p>
                            </div>
                        </div>

                        <div id="customMedicineFields" style="{{ $prescription->medicine_id ? 'display: none;' : '' }}">
                            <div class="form-group">
                                <label for="medicine_name_custom">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="medicine_name_custom" id="medicine_name_custom" 
                                       class="form-control" placeholder="Enter medicine name"
                                       value="{{ old('medicine_name_custom', !$prescription->medicine_id ? $prescription->medicine_name : '') }}">
                            </div>
                            <div class="form-group">
                                <label for="generic_name_custom">Generic Name</label>
                                <input type="text" name="generic_name_custom" id="generic_name_custom" 
                                       class="form-control" placeholder="Enter generic name"
                                       value="{{ old('generic_name_custom', !$prescription->medicine_id ? $prescription->generic_name : '') }}">
                            </div>
                        </div>

                        <!-- Hidden fields for medicine data -->
                        <input type="hidden" name="medicine_name" id="medicine_name" value="{{ old('medicine_name', $prescription->medicine_name) }}">
                        <input type="hidden" name="generic_name" id="generic_name" value="{{ old('generic_name', $prescription->generic_name) }}">
                    </div>
                </div>
            </div>

            <!-- Prescription Details -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-prescription mr-2"></i>Prescription Details
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="dosage">Dosage <span class="text-danger">*</span></label>
                            <input type="text" name="dosage" id="dosage" class="form-control" 
                                   placeholder="e.g., 500mg, 2 tablets, 5ml"
                                   value="{{ old('dosage', $prescription->dosage) }}" required>
                            @error('dosage')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="frequency">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" id="frequency" class="form-control" required>
                                <option value="">Select Frequency</option>
                                <option value="once_daily" {{ old('frequency', $prescription->frequency) == 'once_daily' ? 'selected' : '' }}>Once Daily</option>
                                <option value="twice_daily" {{ old('frequency', $prescription->frequency) == 'twice_daily' ? 'selected' : '' }}>Twice Daily</option>
                                <option value="three_times_daily" {{ old('frequency', $prescription->frequency) == 'three_times_daily' ? 'selected' : '' }}>Three Times Daily</option>
                                <option value="four_times_daily" {{ old('frequency', $prescription->frequency) == 'four_times_daily' ? 'selected' : '' }}>Four Times Daily</option>
                                <option value="every_4_hours" {{ old('frequency', $prescription->frequency) == 'every_4_hours' ? 'selected' : '' }}>Every 4 Hours</option>
                                <option value="every_6_hours" {{ old('frequency', $prescription->frequency) == 'every_6_hours' ? 'selected' : '' }}>Every 6 Hours</option>
                                <option value="every_8_hours" {{ old('frequency', $prescription->frequency) == 'every_8_hours' ? 'selected' : '' }}>Every 8 Hours</option>
                                <option value="every_12_hours" {{ old('frequency', $prescription->frequency) == 'every_12_hours' ? 'selected' : '' }}>Every 12 Hours</option>
                                <option value="as_needed" {{ old('frequency', $prescription->frequency) == 'as_needed' ? 'selected' : '' }}>As Needed</option>
                                <option value="weekly" {{ old('frequency', $prescription->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency', $prescription->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('frequency')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   min="1" placeholder="Enter quantity"
                                   value="{{ old('quantity', $prescription->quantity) }}" required>
                            @if(isset($prescription->dispensed_quantity) && $prescription->dispensed_quantity > 0)
                                <small class="form-text text-info">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ $prescription->dispensed_quantity }} units have already been dispensed
                                </small>
                            @endif
                            @error('quantity')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prescribed_date">Prescribed Date <span class="text-danger">*</span></label>
                                    <input type="date" name="prescribed_date" id="prescribed_date" class="form-control" 
                                           value="{{ old('prescribed_date', $prescription->prescribed_date->format('Y-m-d')) }}" required>
                                    @error('prescribed_date')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" 
                                           value="{{ old('expiry_date', $prescription->expiry_date ? $prescription->expiry_date->format('Y-m-d') : '') }}">
                                    <small class="form-text text-muted">Leave empty to auto-set to 30 days</small>
                                    @error('expiry_date')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions and Notes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list-alt mr-2"></i>Instructions & Notes
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="instructions">Instructions <span class="text-danger">*</span></label>
                    <textarea name="instructions" id="instructions" class="form-control" rows="4" 
                              placeholder="Detailed instructions for taking the medication..."
                              required>{{ old('instructions', $prescription->instructions) }}</textarea>
                    @error('instructions')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" 
                              placeholder="Any additional notes or comments...">{{ old('notes', $prescription->notes) }}</textarea>
                    @error('notes')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card">
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Update Prescription
                        </button>
                        <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-info float-right" id="previewBtn">
                            <i class="fas fa-eye mr-2"></i>Preview Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Prescription Update Preview</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="prescriptionPreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmUpdate">
                        <i class="fas fa-check mr-2"></i>Confirm & Update
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .form-check {
        margin-bottom: 10px;
    }
    
    .select2-container .select2-selection--single {
        height: 38px;
        border-color: #ced4da;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    
    .alert h6 {
        margin-bottom: 10px;
        font-weight: bold;
    }
    
    .text-danger {
        font-weight: 500;
    }
    
    .badge {
        font-size: 0.8em;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Patient selection change
    $('#patient_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const email = selectedOption.data('email');
        const phone = selectedOption.data('phone');
        
        $('#patientEmail').text(email || '-');
        $('#patientPhone').text(phone || '-');
    });

    // Medicine type selection
    $('input[name="medicine_selection_type"]').change(function() {
        if ($(this).val() === 'inventory') {
            $('#inventorySelection').show();
            $('#customMedicineFields').hide();
            $('#medicine_id').prop('required', true);
            $('#medicine_name_custom').prop('required', false);
        } else {
            $('#inventorySelection').hide();
            $('#customMedicineFields').show();
            $('#medicine_id').prop('required', false);
            $('#medicine_name_custom').prop('required', true);
            $('#medicineInfo').hide();
        }
    });

    // Medicine selection change
    $('#medicine_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const name = selectedOption.data('name');
        const generic = selectedOption.data('generic');
        const brand = selectedOption.data('brand');
        const strength = selectedOption.data('strength');
        const stock = selectedOption.data('stock');
        
        if ($(this).val()) {
            $('#medicineGeneric').text(generic || '-');
            $('#medicineBrand').text(brand || '-');
            $('#medicineStrength').text(strength || '-');
            $('#medicineStock').text(stock || '0');
            $('#medicineInfo').show();
            
            // Set hidden fields
            $('#medicine_name').val(name);
            $('#generic_name').val(generic);
        } else {
            $('#medicineInfo').hide();
            $('#medicine_name').val('');
            $('#generic_name').val('');
        }
    });

    // Custom medicine fields change
    $('#medicine_name_custom, #generic_name_custom').on('input', function() {
        if ($('input[name="medicine_selection_type"]:checked').val() === 'custom') {
            $('#medicine_name').val($('#medicine_name_custom').val());
            $('#generic_name').val($('#generic_name_custom').val());
        }
    });

    // Preview functionality
    $('#previewBtn').click(function(e) {
        e.preventDefault();
        generatePreview();
    });

    // Confirm update
    $('#confirmUpdate').click(function() {
        $('#previewModal').modal('hide');
        $('#prescriptionEditForm').submit();
    });

    // Generate preview
    function generatePreview() {
        const patientName = $('#patient_id option:selected').text();
        const medicineName = $('#medicine_name').val() || $('#medicine_name_custom').val();
        const genericName = $('#generic_name').val() || $('#generic_name_custom').val();
        const dosage = $('#dosage').val();
        const frequency = $('#frequency option:selected').text();
        const quantity = $('#quantity').val();
        const instructions = $('#instructions').val();
        const prescribedDate = $('#prescribed_date').val();
        const expiryDate = $('#expiry_date').val();
        const notes = $('#notes').val();

        const preview = `
            <div class="prescription-preview">
                <h5><i class="fas fa-prescription mr-2"></i>Updated Prescription Summary</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Patient:</strong> ${patientName}</p>
                        <p><strong>Medicine:</strong> ${medicineName}</p>
                        ${genericName ? `<p><strong>Generic:</strong> ${genericName}</p>` : ''}
                        <p><strong>Dosage:</strong> ${dosage}</p>
                        <p><strong>Frequency:</strong> ${frequency}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Quantity:</strong> ${quantity}</p>
                        <p><strong>Prescribed Date:</strong> ${prescribedDate}</p>
                        ${expiryDate ? `<p><strong>Expiry Date:</strong> ${expiryDate}</p>` : ''}
                    </div>
                </div>
                <div class="mt-3">
                    <p><strong>Instructions:</strong></p>
                    <p class="border p-2">${instructions}</p>
                </div>
                ${notes ? `
                    <div class="mt-3">
                        <p><strong>Additional Notes:</strong></p>
                        <p class="border p-2">${notes}</p>
                    </div>
                ` : ''}
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Changes will be applied to Prescription #{{ $prescription->id }}</strong>
                </div>
            </div>
        `;

        $('#prescriptionPreview').html(preview);
        $('#previewModal').modal('show');
    }

    // Initialize medicine info if editing from inventory
    @if($prescription->medicine_id)
    $('#medicine_id').trigger('change');
    @endif
});
</script>
@endsection