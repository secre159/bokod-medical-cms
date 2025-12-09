@extends('adminlte::page')

@section('title', 'Create Prescription | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                Create New Prescription
                @if(($isPatientLocked ?? false) && ($selectedPatient ?? null))
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-user-lock text-info mr-1"></i>
                        for {{ $selectedPatient->patient_name }}
                    </small>
                @endif
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('prescriptions.index') }}">Prescriptions</a></li>
                @if(($isPatientLocked ?? false) && ($selectedPatient ?? null))
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $selectedPatient) }}">{{ $selectedPatient->patient_name }}</a></li>
                @endif
                <li class="breadcrumb-item active">Create</li>
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

    <!-- Instructions Card -->
    <div class="card border-info">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">
                <i class="fas fa-info-circle mr-2"></i>Prescription Creation Guide
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-stethoscope text-info mr-2"></i>Treatment Type Options:</h6>
                    <ul class="list-unstyled ml-3">
                        <li><i class="fas fa-boxes text-primary mr-2"></i><strong>From Inventory:</strong> Select medicines from available stock</li>
                        <li><i class="fas fa-edit text-primary mr-2"></i><strong>Custom Medicine:</strong> Enter medicine details manually</li>
                        <li><i class="fas fa-comments text-primary mr-2"></i><strong>No Medicine:</strong> Consultation, advice, or non-medication treatment</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Important Notes:</h6>
                    <ul class="list-unstyled ml-3">
                        <li><i class="fas fa-arrow-right text-muted mr-2"></i>Inventory medicines will deduct from stock</li>
                        <li><i class="fas fa-arrow-right text-muted mr-2"></i>Expiry date defaults to 30 days if not specified</li>
                        <li><i class="fas fa-arrow-right text-muted mr-2"></i>No Medicine option is perfect for consultations and advice</li>
                        <li><i class="fas fa-arrow-right text-muted mr-2"></i>Instructions field is always required for documentation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm">
        @csrf
        
        <div class="row">
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
                            @if(($isPatientLocked ?? false) && ($selectedPatient ?? null))
                                <!-- Locked Patient Display -->
                                <div class="input-group">
                                    <input type="text" class="form-control patient-locked" 
                                           value="{{ $selectedPatient->patient_name }}" 
                                           readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info text-white">
                                            <i class="fas fa-lock" title="Patient is locked for this prescription"></i>
                                        </span>
                                    </div>
                                    <!-- Hidden field to maintain the patient_id -->
                                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $selectedPatient->id }}">
                                </div>
                                <small class="form-text text-info">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    This patient is pre-selected and cannot be changed. 
                                    @if(request('from') === 'appointment')
                                        This prescription is being created from an appointment.
                                    @elseif(request('from') === 'patient')
                                        This prescription is being created from the patient's profile.
                                    @endif
                                    <a href="{{ route('prescriptions.create') }}" class="text-primary ml-2">
                                        <i class="fas fa-plus-circle mr-1"></i>Create for different patient
                                    </a>
                                </small>
                            @else
                                <!-- Normal Patient Selection -->
                                <select name="patient_id" id="patient_id" class="form-control select2" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" 
                                                data-email="{{ $patient->email }}" 
                                                data-phone="{{ $patient->phone_number }}"
                                                {{ (old('patient_id') ?: ($selectedPatientId ?? null)) == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->patient_name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('patient_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="patientDetails" class="alert alert-info" style="{{ (($isPatientLocked ?? false) && ($selectedPatient ?? null)) ? '' : 'display: none;' }}">
                            <h6><i class="fas fa-info-circle mr-1"></i>Patient Details</h6>
                            <p class="mb-1"><strong>Email:</strong> <span id="patientEmail">{{ (($isPatientLocked ?? false) && ($selectedPatient ?? null)) ? $selectedPatient->email : '-' }}</span></p>
                            <p class="mb-0"><strong>Phone:</strong> <span id="patientPhone">{{ (($isPatientLocked ?? false) && ($selectedPatient ?? null)) ? ($selectedPatient->phone_number ?: '-') : '-' }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

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
                            <label for="medicine_selection_type">Treatment Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medicine_selection_type" id="from_inventory" value="inventory" checked>
                                <label class="form-check-label" for="from_inventory">
                                    <i class="fas fa-boxes mr-1"></i>From Inventory
                                </label>
                                <small class="form-text text-muted ml-3">Select medicine from available stock</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medicine_selection_type" id="custom_medicine" value="custom">
                                <label class="form-check-label" for="custom_medicine">
                                    <i class="fas fa-edit mr-1"></i>Custom Medicine
                                </label>
                                <small class="form-text text-muted ml-3">Enter medicine details manually</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="medicine_selection_type" id="no_medicine" value="no_medicine">
                                <label class="form-check-label" for="no_medicine">
                                    <i class="fas fa-comments mr-1"></i>No Medicine Required
                                </label>
                                <small class="form-text text-muted ml-3">Consultation, advice, or non-medication treatment</small>
                            </div>
                        </div>

                        <div id="inventorySelection">
                            <div class="form-group">
                                <label for="medicine_id">Select Medicine</label>
                                <select name="medicine_id" id="medicine_id" class="form-control select2">
                                    <option value="">Choose from inventory</option>
                                    @if($medicines->count() > 0)
                                        @foreach($medicines as $medicine)
                                            <option value="{{ $medicine->id }}" 
                                                    data-name="{{ $medicine->medicine_name }}"
                                                    data-generic="{{ $medicine->generic_name }}"
                                                    data-brand="{{ $medicine->brand_name }}"
                                                    data-strength="{{ $medicine->strength }}"
                                                    data-form="{{ $medicine->dosage_form }}"
                                                    data-stock="{{ $medicine->stock_quantity }}"
                                                    {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                                {{ $medicine->medicine_name }} 
                                                @if($medicine->brand_name)
                                                    ({{ $medicine->brand_name }})
                                                @endif
                                                - {{ $medicine->strength }} {{ $medicine->dosage_form }}
                                                <small>(Stock: {{ $medicine->stock_quantity }})</small>
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No medicines available in inventory</option>
                                    @endif
                                </select>
                                @error('medicine_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                @if($medicines->count() == 0)
                                    <small class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        No medicines found in inventory. Consider adding medicines to inventory or use "Custom Medicine" option.
                                        <a href="{{ route('medicines.create') }}" target="_blank" class="text-primary">Add Medicine</a>
                                    </small>
                                @endif
                            </div>

                            <div id="medicineInfo" class="alert alert-success" style="display: none;">
                                <h6><i class="fas fa-info-circle mr-1"></i>Medicine Details</h6>
                                <p class="mb-1"><strong>Generic:</strong> <span id="medicineGeneric">-</span></p>
                                <p class="mb-1"><strong>Brand:</strong> <span id="medicineBrand">-</span></p>
                                <p class="mb-1"><strong>Strength:</strong> <span id="medicineStrength">-</span></p>
                                <p class="mb-0"><strong>Available Stock:</strong> <span id="medicineStock">-</span> units</p>
                            </div>
                        </div>

                        <div id="customMedicineFields" style="display: none;">
                            <div class="form-group">
                                <label for="medicine_name_custom">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="medicine_name_custom" id="medicine_name_custom" 
                                       class="form-control" placeholder="Enter medicine name"
                                       value="{{ old('medicine_name_custom') }}" list="common_medicines">
                                <datalist id="common_medicines">
                                    <option value="Paracetamol">Paracetamol</option>
                                    <option value="Ibuprofen">Ibuprofen</option>
                                    <option value="Aspirin">Aspirin</option>
                                    <option value="Amoxicillin">Amoxicillin</option>
                                    <option value="Cetirizine">Cetirizine</option>
                                    <option value="Omeprazole">Omeprazole</option>
                                    <option value="Metformin">Metformin</option>
                                    <option value="Lisinopril">Lisinopril</option>
                                    <option value="Simvastatin">Simvastatin</option>
                                </datalist>
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    Start typing to see common medicine suggestions
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="generic_name_custom">Generic Name</label>
                                <input type="text" name="generic_name_custom" id="generic_name_custom" 
                                       class="form-control" placeholder="Enter generic name"
                                       value="{{ old('generic_name_custom') }}">
                            </div>
                        </div>
                        
                        <div id="noMedicineFields" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle mr-1"></i>Consultation Record</h6>
                                <p class="mb-0">This will create a consultation record without medication. Use the instructions field to document advice, recommendations, or treatment plans.</p>
                            </div>
                            <div class="form-group">
                                <label for="consultation_type">Consultation Type</label>
                                <select name="consultation_type" id="consultation_type" class="form-control">
                                    <option value="">Select consultation type</option>
                                    <option value="general_consultation">General Consultation</option>
                                    <option value="follow_up">Follow-up Visit</option>
                                    <option value="health_advice">Health Advice</option>
                                    <option value="lifestyle_counseling">Lifestyle Counseling</option>
                                    <option value="referral">Referral</option>
                                    <option value="preventive_care">Preventive Care</option>
                                    <option value="health_screening">Health Screening</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hidden fields for medicine data -->
                        <input type="hidden" name="medicine_name" id="medicine_name" value="{{ old('medicine_name') }}">
                        <input type="hidden" name="generic_name" id="generic_name" value="{{ old('generic_name') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-prescription mr-2"></i>Prescription Details
                </h3>
            </div>
            <div class="card-body">
                <div class="row" id="medicineDetailsRow">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dosage">Dosage <span class="text-danger medicine-required">*</span></label>
                            <input type="text" name="dosage" id="dosage" class="form-control" 
                                   placeholder="e.g., 500mg, 2 tablets, 5ml, 10ml with syringe"
                                   value="{{ old('dosage') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-magic mr-1"></i>Select a medicine to see dosage suggestions based on its form
                            </small>
                            @error('dosage')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        {{-- Include Smart Dosage Helper --}}
                        @include('prescriptions.partials.dosage-helper')
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="frequency">Frequency <span class="text-danger medicine-required">*</span></label>
                            <select name="frequency" id="frequency" class="form-control">
                                <option value="">Select Frequency</option>
                                <option value="once_daily" {{ old('frequency') == 'once_daily' ? 'selected' : '' }}>Once Daily</option>
                                <option value="twice_daily" {{ old('frequency') == 'twice_daily' ? 'selected' : '' }}>Twice Daily</option>
                                <option value="three_times_daily" {{ old('frequency') == 'three_times_daily' ? 'selected' : '' }}>Three Times Daily</option>
                                <option value="four_times_daily" {{ old('frequency') == 'four_times_daily' ? 'selected' : '' }}>Four Times Daily</option>
                                <option value="every_4_hours" {{ old('frequency') == 'every_4_hours' ? 'selected' : '' }}>Every 4 Hours</option>
                                <option value="every_6_hours" {{ old('frequency') == 'every_6_hours' ? 'selected' : '' }}>Every 6 Hours</option>
                                <option value="every_8_hours" {{ old('frequency') == 'every_8_hours' ? 'selected' : '' }}>Every 8 Hours</option>
                                <option value="every_12_hours" {{ old('frequency') == 'every_12_hours' ? 'selected' : '' }}>Every 12 Hours</option>
                                <option value="as_needed" {{ old('frequency') == 'as_needed' ? 'selected' : '' }}>As Needed</option>
                                <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="not_applicable" {{ old('frequency') == 'not_applicable' ? 'selected' : '' }}>Not Applicable</option>
                            </select>
                            @error('frequency')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="quantity">Quantity <span class="text-danger medicine-required">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                   min="1" step="1" placeholder="Enter quantity"
                                   value="{{ old('quantity', '1') }}">
                            <small class="form-text text-muted quantity-help">Number of units to dispense</small>
                            @error('quantity')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="prescribed_date">Prescribed Date <span class="text-danger">*</span></label>
                            <input type="date" name="prescribed_date" id="prescribed_date" class="form-control" 
                                   value="{{ old('prescribed_date', date('Y-m-d')) }}" required>
                            @error('prescribed_date')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control" 
                                   value="{{ old('expiry_date') }}">
                            <small class="form-text text-muted">If not specified, will be set to 30 days from prescribed date</small>
                            @error('expiry_date')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="instructions">Instructions <span class="text-danger">*</span></label>
                    <textarea name="instructions" id="instructions" class="form-control" rows="3" 
                              placeholder="Detailed instructions for taking the medication..."
                              required>{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" 
                              placeholder="Any additional notes or comments...">{{ old('notes') }}</textarea>
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
                            <i class="fas fa-save mr-2"></i>Create Prescription
                        </button>
                        <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-info float-right" id="previewBtn">
                            <i class="fas fa-eye mr-2"></i>Preview
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
                    <h4 class="modal-title">Prescription Preview</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="prescriptionPreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmCreate">
                        <i class="fas fa-check mr-2"></i>Confirm & Create
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
    
    .patient-locked {
        background-color: #f8f9fa !important;
        border-color: #17a2b8;
        cursor: not-allowed;
    }
    
    .patient-locked-info {
        background-color: #d1ecf1;
        border-left: 4px solid #17a2b8;
        padding: 10px 15px;
        margin-top: 10px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Wait for jQuery to be available
function initPrescriptionCreate() {
    if (typeof $ === 'undefined') {
        console.log('jQuery not loaded for prescriptions create, waiting...');
        setTimeout(initPrescriptionCreate, 100);
        return;
    }
    
    $(document).ready(function() {
    // Check if patient is locked
    const isPatientLocked = {{ ($isPatientLocked ?? false) ? 'true' : 'false' }};
    
    // Initialize Select2 only if patient is not locked
    if (!isPatientLocked) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Patient selection change (only if not locked)
    $('#patient_id').change(function() {
        // Skip if patient is locked
        if (isPatientLocked) {
            return;
        }
        
        const selectedOption = $(this).find('option:selected');
        const email = selectedOption.data('email');
        const phone = selectedOption.data('phone');
        
        if ($(this).val()) {
            $('#patientEmail').text(email || '-');
            $('#patientPhone').text(phone || '-');
            $('#patientDetails').show();
        } else {
            $('#patientDetails').hide();
        }
    });

    // Treatment type selection
    $('input[name="medicine_selection_type"]').change(function() {
        const selectedType = $(this).val();
        
        // Hide all sections first
        $('#inventorySelection').hide();
        $('#customMedicineFields').hide();
        $('#noMedicineFields').hide();
        $('#medicineInfo').hide();
        
        // Reset required fields
        $('#medicine_id').prop('required', false);
        $('#medicine_name_custom').prop('required', false);
        $('#dosage').prop('required', false);
        $('#frequency').prop('required', false);
        $('#quantity').prop('required', false);
        
        if (selectedType === 'inventory') {
            $('#inventorySelection').show();
            $('#medicine_id').prop('required', true);
            $('#dosage').prop('required', true);
            $('#frequency').prop('required', true);
            $('#quantity').prop('required', true);
            
            // Clear other fields
            $('#medicine_name_custom').val('');
            $('#generic_name_custom').val('');
            $('#consultation_type').val('');
            
            // Show medicine-related required indicators
            $('.medicine-required').show();
            $('.quantity-help').text('Number of units to dispense');
            
        } else if (selectedType === 'custom') {
            $('#customMedicineFields').show();
            $('#medicine_name_custom').prop('required', true);
            $('#dosage').prop('required', true);
            $('#frequency').prop('required', true);
            $('#quantity').prop('required', true);
            
            // Clear other fields
            $('#medicine_id').val('').trigger('change');
            $('#consultation_type').val('');
            
            // Show medicine-related required indicators
            $('.medicine-required').show();
            $('.quantity-help').text('Number of units to dispense');
            
        } else if (selectedType === 'no_medicine') {
            $('#noMedicineFields').show();
            
            // Clear medicine fields
            $('#medicine_id').val('').trigger('change');
            $('#medicine_name_custom').val('');
            $('#generic_name_custom').val('');
            
            // Set default values for no-medicine consultation
            $('#dosage').val('Not Applicable');
            $('#frequency').val('not_applicable');
            $('#quantity').val('1');
            
            // Hide medicine-related required indicators
            $('.medicine-required').hide();
            $('.quantity-help').text('Always 1 for consultations');
        }
    });

    // Medicine selection change
    $('#medicine_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const name = selectedOption.data('name');
        const generic = selectedOption.data('generic');
        const brand = selectedOption.data('brand');
        const strength = selectedOption.data('strength');
        const dosageForm = selectedOption.data('form');
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
            
            // Set max quantity based on stock
            $('#quantity').attr('max', stock);
            
            // Update dosage helper based on medicine form
            if (typeof updateDosageHelper === 'function') {
                updateDosageHelper(dosageForm);
            }
        } else {
            $('#medicineInfo').hide();
            $('#medicine_name').val('');
            $('#generic_name').val('');
            $('#quantity').removeAttr('max');
            
            // Hide dosage helper
            if (typeof updateDosageHelper === 'function') {
                updateDosageHelper(null);
            }
        }
    });

    // Custom medicine fields change
    $('#medicine_name_custom, #generic_name_custom').on('input', function() {
        if ($('input[name="medicine_selection_type"]:checked').val() === 'custom') {
            $('#medicine_name').val($('#medicine_name_custom').val());
            $('#generic_name').val($('#generic_name_custom').val());
        }
    });

    // Quantity validation
    $('#quantity').on('input', function() {
        const max = $(this).attr('max');
        const value = $(this).val();
        
        if (max && parseInt(value) > parseInt(max)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Quantity cannot exceed available stock (' + max + ')</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Auto-set expiry date and validation
    $('#prescribed_date').change(function() {
        const prescribedDate = new Date($(this).val());
        if (prescribedDate && !$('#expiry_date').val()) {
            const expiryDate = new Date(prescribedDate);
            expiryDate.setDate(prescribedDate.getDate() + 30);
            $('#expiry_date').val(expiryDate.toISOString().split('T')[0]);
        }
        
        // Set minimum date for expiry date
        const minExpiryDate = new Date(prescribedDate);
        minExpiryDate.setDate(prescribedDate.getDate() + 1); // At least 1 day after prescribed date
        $('#expiry_date').attr('min', minExpiryDate.toISOString().split('T')[0]);
    });
    
    // Validate expiry date
    $('#expiry_date').change(function() {
        const prescribedDate = new Date($('#prescribed_date').val());
        const expiryDate = new Date($(this).val());
        
        if (prescribedDate && expiryDate && expiryDate <= prescribedDate) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Expiry date must be after prescribed date</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Preview functionality
    $('#previewBtn').click(function(e) {
        e.preventDefault();
        generatePreview();
    });

    // Confirm create
    $('#confirmCreate').click(function() {
        $('#previewModal').modal('hide');
        $('#prescriptionForm').submit();
    });

    // Generate preview
    function generatePreview() {
        const patientName = $('#patient_id option:selected').text();
        const selectionType = $('input[name="medicine_selection_type"]:checked').val();
        
        let medicineName, genericName, dosage, frequency;
        
        if (selectionType === 'no_medicine') {
            const consultationType = $('#consultation_type option:selected').text() || 'General Consultation';
            medicineName = 'Consultation: ' + consultationType;
            genericName = '';
            dosage = $('#dosage').val() || 'Not Applicable';
            frequency = $('#frequency option:selected').text() || 'Not Applicable';
        } else {
            medicineName = $('#medicine_name').val() || $('#medicine_name_custom').val();
            genericName = $('#generic_name').val() || $('#generic_name_custom').val();
            dosage = $('#dosage').val();
            frequency = $('#frequency option:selected').text();
        }
        
        const quantity = $('#quantity').val();
        const instructions = $('#instructions').val();
        const prescribedDate = $('#prescribed_date').val();
        const expiryDate = $('#expiry_date').val();
        const notes = $('#notes').val();

        const preview = `
            <div class="prescription-preview">
                <h5><i class="fas fa-prescription mr-2"></i>Prescription Summary</h5>
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
            </div>
        `;

        $('#prescriptionPreview').html(preview);
        $('#previewModal').modal('show');
    }

    // Form submission validation and protection
    $('#prescriptionForm').on('submit', function(e) {
        let isValid = true;
        const submitBtn = $(this).find('button[type="submit"]');
        
        // Check if form is already being submitted
        if ($(this).hasClass('submitting')) {
            e.preventDefault();
            return false;
        }
        
        // Validate required fields based on medicine selection type
        const selectionType = $('input[name="medicine_selection_type"]:checked').val();
        
        if (selectionType === 'inventory') {
            if (!$('#medicine_id').val()) {
                $('#medicine_id').addClass('is-invalid');
                isValid = false;
            }
        } else if (selectionType === 'custom') {
            if (!$('#medicine_name_custom').val()) {
                $('#medicine_name_custom').addClass('is-invalid');
                isValid = false;
            }
        } else if (selectionType === 'no_medicine') {
            // For consultations, ensure basic fields are filled
            if (!$('#instructions').val()) {
                $('#instructions').addClass('is-invalid');
                isValid = false;
            }
        }
        
        // Check expiry date validation
        if ($('#expiry_date').hasClass('is-invalid')) {
            isValid = false;
        }
        
        // Check quantity validation
        if ($('#quantity').hasClass('is-invalid')) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Show error message
            if (!$('.form-validation-error').length) {
                $('#prescriptionForm').prepend(
                    '<div class="alert alert-danger form-validation-error">' +
                    '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                    'Please fix the validation errors before submitting.' +
                    '</div>'
                );
            }
            // Scroll to top
            $('html, body').animate({scrollTop: 0}, 500);
            return false;
        }
        
        // Mark form as submitting and disable submit button
        $(this).addClass('submitting');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating Prescription...');
        
        // Remove any existing error messages
        $('.form-validation-error').remove();
    });
    
    // Initialize form state
    if ($('#patient_id').val()) {
        $('#patient_id').trigger('change');
    }
    if ($('#medicine_id').val()) {
        $('#medicine_id').trigger('change');
    }
    
    // Trigger initial prescribed date change to set expiry date limits
    if ($('#prescribed_date').val()) {
        $('#prescribed_date').trigger('change');
    }
    }); // End of $(document).ready
} // End of initPrescriptionCreate function

// Initialize when page loads
initPrescriptionCreate();
</script>
@endsection