@extends('adminlte::page')

@section('title', 'Add New Medicine | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Add New Medicine</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('medicines.index') }}">Medicines</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i> <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('medicines.store') }}" method="POST" id="medicineForm" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>Basic Information
                </h3>
                <div class="card-tools">
                    <a href="{{ route('medicines.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Medicine Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="medicine_name" class="required">Medicine Name</label>
                            <input type="text" name="medicine_name" id="medicine_name" 
                                   class="form-control" value="{{ old('medicine_name') }}" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Enter the primary medicine name
                            </small>
                        </div>
                    </div>

                    <!-- Generic Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="generic_name">Generic Name</label>
                            <input type="text" name="generic_name" id="generic_name" 
                                   class="form-control" value="{{ old('generic_name') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Scientific/generic name if different
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Brand Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="brand_name">Brand Name</label>
                            <input type="text" name="brand_name" id="brand_name" 
                                   class="form-control" value="{{ old('brand_name') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Commercial brand name
                            </small>
                        </div>
                    </div>

                    <!-- Manufacturer -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="manufacturer">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer" 
                                   class="form-control" value="{{ old('manufacturer') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Manufacturing company
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Category -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category" class="required">Category</label>
                            <select name="category" id="category" class="form-control select2" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $key => $categoryName)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $categoryName }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Medicine therapeutic category
                            </small>
                        </div>
                    </div>
                    
                    <!-- Therapeutic Class -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="therapeutic_class">Therapeutic Class</label>
                            <select name="therapeutic_class" id="therapeutic_class" class="form-control select2">
                                <option value="">-- Select Therapeutic Class --</option>
                                @foreach($therapeuticClasses as $key => $className)
                                    <option value="{{ $key }}" {{ old('therapeutic_class') == $key ? 'selected' : '' }}>{{ $className }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Pharmacological classification
                            </small>
                        </div>
                    </div>

                    <!-- Requires Prescription -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="requires_prescription">Prescription Requirement</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" id="requires_prescription" 
                                       name="requires_prescription" value="1" {{ old('requires_prescription') ? 'checked' : 'checked' }}>
                                <label class="custom-control-label" for="requires_prescription">Requires Prescription</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Check if prescription is required
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" 
                                      placeholder="Brief description of the medicine...">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Brief description of the medicine's purpose
                            </small>
                        </div>
                    </div>
                    
                    <!-- Indication -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="indication">Indication</label>
                            <textarea name="indication" id="indication" class="form-control" rows="3" 
                                      placeholder="What is this medicine used to treat?">{{ old('indication') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Medical conditions this medicine treats
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dosage & Physical Properties Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-capsules mr-2"></i>Dosage & Physical Properties
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Dosage Form -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dosage_form" class="required">Dosage Form</label>
                            <select name="dosage_form" id="dosage_form" class="form-control select2" required>
                                <option value="">-- Select Form --</option>
                                @foreach($dosageForms as $key => $form)
                                    <option value="{{ $key }}" {{ old('dosage_form') == $key ? 'selected' : '' }}>{{ $form }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Physical form of the medicine
                            </small>
                        </div>
                    </div>

                    <!-- Strength -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="strength" class="required">Strength</label>
                            <input type="text" name="strength" id="strength" 
                                   class="form-control" value="{{ old('strength') }}" required
                                   placeholder="e.g., 500mg, 250ml, 100IU">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Dosage strength per unit
                            </small>
                        </div>
                    </div>

                    <!-- Unit -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="unit" class="required">Unit</label>
                            <select name="unit" id="unit" class="form-control" required>
                                <option value="">-- Select Unit --</option>
                                <option value="pieces" {{ old('unit') == 'pieces' ? 'selected' : 'selected' }}>Pieces</option>
                                <option value="bottles" {{ old('unit') == 'bottles' ? 'selected' : '' }}>Bottles</option>
                                <option value="vials" {{ old('unit') == 'vials' ? 'selected' : '' }}>Vials</option>
                                <option value="boxes" {{ old('unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                <option value="tubes" {{ old('unit') == 'tubes' ? 'selected' : '' }}>Tubes</option>
                                <option value="sachets" {{ old('unit') == 'sachets' ? 'selected' : '' }}>Sachets</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Unit of measurement
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Dosage Instructions & Age Restrictions -->
                <div class="row">
                    <!-- Dosage Instructions -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="dosage_instructions">Dosage Instructions</label>
                            <textarea name="dosage_instructions" id="dosage_instructions" class="form-control" rows="2" 
                                      placeholder="e.g., Take 1 tablet twice daily with food">{{ old('dosage_instructions') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Detailed dosage and administration instructions
                            </small>
                        </div>
                    </div>
                    
                    <!-- Age Restrictions -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="age_restrictions">Age Restrictions</label>
                            <input type="text" name="age_restrictions" id="age_restrictions" 
                                   class="form-control" value="{{ old('age_restrictions') }}"
                                   placeholder="e.g., Adults only, 12+ years">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Age limitations for use
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Storage Conditions -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="storage_conditions">Storage Conditions</label>
                            <input type="text" name="storage_conditions" id="storage_conditions" 
                                   class="form-control" value="{{ old('storage_conditions') }}"
                                   placeholder="e.g., Store in a cool, dry place away from direct sunlight">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Special storage requirements
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Management Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes mr-2"></i>Inventory Management
                </h3>
            </div>
            <div class="card-body">
                <!-- Basic Stock Information -->
                <div class="row">
                    <!-- Stock Number -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="stock_number">Stock Number</label>
                            <input type="text" name="stock_number" id="stock_number" 
                                   class="form-control" value="{{ old('stock_number') }}" 
                                   placeholder="e.g., MED-001">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Internal stock tracking number
                            </small>
                        </div>
                    </div>

                    <!-- Unit Measure -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="unit_measure">Unit Measure</label>
                            <select name="unit_measure" id="unit_measure" class="form-control">
                                <option value="">-- Select Unit --</option>
                                <option value="pc" {{ old('unit_measure') == 'pc' ? 'selected' : '' }}>Piece (pc)</option>
                                <option value="bottle" {{ old('unit_measure') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                <option value="vial" {{ old('unit_measure') == 'vial' ? 'selected' : '' }}>Vial</option>
                                <option value="box" {{ old('unit_measure') == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="tube" {{ old('unit_measure') == 'tube' ? 'selected' : '' }}>Tube</option>
                                <option value="sachet" {{ old('unit_measure') == 'sachet' ? 'selected' : '' }}>Sachet</option>
                                <option value="ml" {{ old('unit_measure') == 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                                <option value="mg" {{ old('unit_measure') == 'mg' ? 'selected' : '' }}>Milligram (mg)</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Unit of measurement for inventory
                            </small>
                        </div>
                    </div>


                    <!-- Minimum Stock -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="minimum_stock" class="required">Minimum Stock Level</label>
                            <input type="number" name="minimum_stock" id="minimum_stock" 
                                   class="form-control" value="{{ old('minimum_stock', 10) }}" required min="0">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Low stock alert threshold
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Inventory Count Information -->
                <div class="row">
                    <!-- Stock Quantity -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="stock_quantity" class="required">Initial Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" 
                                   class="form-control" value="{{ old('stock_quantity', 0) }}" required min="0">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Starting stock quantity
                            </small>
                        </div>
                    </div>

                    <!-- Balance Per Card -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="balance_per_card">Balance Per Card</label>
                            <input type="number" name="balance_per_card" id="balance_per_card" 
                                   class="form-control" value="{{ old('balance_per_card', 0) }}" min="0">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Expected balance according to records
                            </small>
                        </div>
                    </div>

                    <!-- On Hand Per Count -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="on_hand_per_count">On Hand Per Count</label>
                            <input type="number" name="on_hand_per_count" id="on_hand_per_count" 
                                   class="form-control" value="{{ old('on_hand_per_count', 0) }}" min="0">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Actual count in physical inventory
                            </small>
                        </div>
                    </div>

                    <!-- Shortage/Overage -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="shortage_overage">Shortage/Overage</label>
                            <input type="number" name="shortage_overage" id="shortage_overage" 
                                   class="form-control" value="{{ old('shortage_overage', 0) }}" readonly>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Auto-calculated: On Hand - Balance
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="row">
                    <!-- Inventory Remarks -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inventory_remarks">Inventory Remarks</label>
                            <input type="text" name="inventory_remarks" id="inventory_remarks" 
                                   class="form-control" value="{{ old('inventory_remarks') }}"
                                   placeholder="Any inventory notes...">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Special notes for inventory tracking
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="row">
                    <!-- Supplier -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier">Supplier</label>
                            <input type="text" name="supplier" id="supplier" 
                                   class="form-control" value="{{ old('supplier') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Main supplier or distributor
                            </small>
                        </div>
                    </div>

                    <!-- Batch Number -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="batch_number">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" 
                                   class="form-control" value="{{ old('batch_number') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Batch or lot number for tracking
                            </small>
                        </div>
                    </div>
                </div>
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
                    <!-- Manufacturing Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="manufacturing_date">Manufacturing Date</label>
                            <input type="date" name="manufacturing_date" id="manufacturing_date" 
                                   class="form-control" value="{{ old('manufacturing_date') }}"
                                   max="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Date when medicine was manufactured
                            </small>
                        </div>
                    </div>

                    <!-- Expiry Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" 
                                   class="form-control" value="{{ old('expiry_date') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Expiration date of the medicine
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-stethoscope mr-2"></i>Medical Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Side Effects -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="side_effects">Side Effects</label>
                            <textarea name="side_effects" id="side_effects" class="form-control" rows="4" 
                                      placeholder="List common side effects...">{{ old('side_effects') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Known side effects and adverse reactions
                            </small>
                        </div>
                    </div>

                    <!-- Contraindications -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contraindications">Contraindications</label>
                            <textarea name="contraindications" id="contraindications" class="form-control" rows="4" 
                                      placeholder="List contraindications...">{{ old('contraindications') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>When this medicine should not be used
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Drug Interactions & Pregnancy -->
                <div class="row">
                    <!-- Drug Interactions -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="drug_interactions">Drug Interactions</label>
                            <textarea name="drug_interactions" id="drug_interactions" class="form-control" rows="3" 
                                      placeholder="List known drug interactions...">{{ old('drug_interactions') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Known interactions with other medications
                            </small>
                        </div>
                    </div>
                    
                    <!-- Pregnancy Category -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pregnancy_category">Pregnancy Category</label>
                            <select name="pregnancy_category" id="pregnancy_category" class="form-control">
                                <option value="">-- Select Category --</option>
                                @foreach($pregnancyCategories as $key => $categoryName)
                                    <option value="{{ $key }}" {{ old('pregnancy_category') == $key ? 'selected' : '' }}>{{ $categoryName }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Safety category for pregnancy
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Warnings -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="warnings">Warnings</label>
                            <textarea name="warnings" id="warnings" class="form-control" rows="3" 
                                      placeholder="Important warnings and precautions...">{{ old('warnings') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Important safety warnings and precautions
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Medicine Image Upload -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="medicine_image">Medicine Image</label>
                            <div class="custom-file">
                                <input type="file" name="medicine_image" id="medicine_image" class="custom-file-input" 
                                       accept="image/*">
                                <label class="custom-file-label" for="medicine_image">Choose image file...</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Upload an image of the medicine (JPEG, PNG, GIF, WebP - Max 10MB)
                            </small>
                            @error('medicine_image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <div class="border rounded p-2 text-center" style="max-width: 300px;">
                                <img id="previewImage" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                <p class="text-muted mt-2 mb-0">Image Preview</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Any additional information or special notes
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Card -->
        <div class="card">
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Add Medicine
                        </button>
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-info" id="previewBtn" style="display: none;">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .required::after {
        content: " *";
        color: red;
        font-weight: bold;
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

    .form-control:invalid {
        border-color: #dc3545;
    }

        .form-control:valid {
            border-color: #28a745;
        }
        
        .inventory-section {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .auto-calculated {
            background-color: #e9ecef;
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
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true
    });
    
    // Image preview functionality
    $('#medicine_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            const fileName = file.name;
            
            // Update the custom file label
            $(this).next('.custom-file-label').html(fileName);
            
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $(this).next('.custom-file-label').html('Choose image file...');
            $('#imagePreview').hide();
        }
    });

    
    // Calculate shortage/overage automatically
    function calculateShortageOverage() {
        var balancePerCard = parseInt($('#balance_per_card').val()) || 0;
        var onHandPerCount = parseInt($('#on_hand_per_count').val()) || 0;
        var shortageOverage = onHandPerCount - balancePerCard;
        $('#shortage_overage').val(shortageOverage);
    }
    
    
    // Event listeners for auto-calculations
    $('#balance_per_card, #on_hand_per_count').on('input', function() {
        calculateShortageOverage();
    });

    // Date validation
    $('#manufacturing_date').on('change', function() {
        var mfgDate = $(this).val();
        if (mfgDate) {
            $('#expiry_date').attr('min', mfgDate);
        }
    });

    $('#expiry_date').on('change', function() {
        var expDate = $(this).val();
        var mfgDate = $('#manufacturing_date').val();
        
        if (mfgDate && expDate && expDate <= mfgDate) {
            alert('Expiry date must be after manufacturing date');
            $(this).val('');
        }
    });

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
        
        // Add new alert at the top of page
        $('body').find('.content-wrapper .content').prepend(alertHtml);
        
        // Scroll to top to show alert
        $('html, body').animate({ scrollTop: 0 }, 'fast');
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        }
    }
    
    // Form validation and AJAX submission
    $('#medicineForm').on('submit', function(e) {
        e.preventDefault(); // Always prevent default to handle via AJAX
        
        var isValid = true;
        var errors = [];

        // Check required fields
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                errors.push($(this).prev('label').text() + ' is required');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });

        if (!isValid) {
            showAlert('Please fix the following errors:\n' + errors.join('\n'), 'danger');
            return false;
        }

        // Show loading state
        var $submitBtn = $('#submitBtn');
        var originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Adding Medicine...');
        $submitBtn.prop('disabled', true);
        
        // Prepare form data
        var formData = new FormData(this);
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    
                    // Reset form and redirect after delay
                    setTimeout(function() {
                        window.location.href = '{{ route("medicines.index") }}';
                    }, 2000);
                } else {
                    showAlert(response.message || 'An error occurred', 'danger');
                }
            },
            error: function(xhr) {
                var errorMessage = 'An error occurred while adding the medicine.';
                
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    var errorList = [];
                    
                    $.each(errors, function(field, messages) {
                        errorList = errorList.concat(messages);
                        // Highlight field with error
                        $('[name="' + field + '"]').addClass('is-invalid');
                    });
                    
                    errorMessage = 'Validation failed:\n' + errorList.join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert(errorMessage, 'danger');
            },
            complete: function() {
                // Restore button state
                $submitBtn.html(originalText);
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Real-time validation feedback
    $('input, select, textarea').on('blur', function() {
        if ($(this).attr('required') && !$(this).val()) {
            $(this).addClass('is-invalid');
        } else if ($(this).val()) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    // Clear validation styling on focus
    $('input, select, textarea').on('focus', function() {
        $(this).removeClass('is-invalid is-valid');
    });
});
</script>
@endsection