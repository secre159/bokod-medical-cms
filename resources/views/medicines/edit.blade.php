@extends('adminlte::page')

@section('title', 'Edit Medicine | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Medicine</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('medicines.index') }}">Medicines</a></li>
                <li class="breadcrumb-item"><a href="{{ route('medicines.show', $medicine) }}">Details</a></li>
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

    <form action="{{ route('medicines.update', $medicine) }}" method="POST" id="medicineEditForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Medicine Information -->
            <div class="col-md-8">
                <!-- Current Medicine Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Current Medicine Information
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $medicine->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($medicine->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">ID:</th>
                                        <td>#{{ $medicine->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $medicine->created_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Current Stock:</th>
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
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Category:</th>
                                        <td>{{ ucwords(str_replace('_', ' ', $medicine->category)) }}</td>
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

                <!-- Basic Medicine Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pills mr-2"></i>Basic Medicine Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="medicine_name">Medicine Name <span class="text-danger">*</span></label>
                                    <input type="text" name="medicine_name" id="medicine_name" class="form-control" 
                                           placeholder="Enter medicine name" 
                                           value="{{ old('medicine_name', $medicine->medicine_name) }}" required>
                                    @error('medicine_name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="generic_name">Generic Name <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="generic_name" id="generic_name" class="form-control"
                                           placeholder="Enter generic name" 
                                           value="{{ old('generic_name', $medicine->generic_name) }}">
                                    @error('generic_name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_name">Brand Name <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="brand_name" id="brand_name" class="form-control"
                                           placeholder="Enter brand name" 
                                           value="{{ old('brand_name', $medicine->brand_name) }}">
                                    @error('brand_name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturer">Manufacturer <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="manufacturer" id="manufacturer" class="form-control"
                                           placeholder="Enter manufacturer name" 
                                           value="{{ old('manufacturer', $medicine->manufacturer) }}">
                                    @error('manufacturer')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $key => $categoryName)
                                            <option value="{{ $key }}" {{ old('category', $medicine->category) == $key ? 'selected' : '' }}>{{ $categoryName }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Therapeutic Class -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="therapeutic_class">Therapeutic Class <span class="badge badge-secondary">Optional</span></label>
                                    <select name="therapeutic_class" id="therapeutic_class" class="form-control">
                                        <option value="">Select Therapeutic Class</option>
                                        @foreach($therapeuticClasses as $key => $className)
                                            <option value="{{ $key }}" {{ old('therapeutic_class', $medicine->therapeutic_class) == $key ? 'selected' : '' }}>{{ $className }}</option>
                                        @endforeach
                                    </select>
                                    @error('therapeutic_class')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dosage_form">Dosage Form <span class="text-danger">*</span></label>
                                    <select name="dosage_form" id="dosage_form" class="form-control" required>
                                        <option value="">Select Form</option>
                                        @foreach($dosageForms as $key => $form)
                                            <option value="{{ $key }}" {{ old('dosage_form', $medicine->dosage_form) == $key ? 'selected' : '' }}>{{ $form }}</option>
                                        @endforeach
                                    </select>
                                    @error('dosage_form')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="strength">Strength <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="strength" id="strength" class="form-control" 
                                           placeholder="e.g., 500mg, 5ml" 
                                           value="{{ old('strength', $medicine->strength) }}">
                                    @error('strength')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dosage Instructions & Age Restrictions -->
                        <div class="row">
                            <!-- Dosage Instructions -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="dosage_instructions">Dosage Instructions <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="dosage_instructions" id="dosage_instructions" class="form-control" rows="2"
                                              placeholder="e.g., Take 1 tablet twice daily with food">{{ old('dosage_instructions', $medicine->dosage_instructions) }}</textarea>
                                    @error('dosage_instructions')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Age Restrictions -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age_restrictions">Age Restrictions <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="age_restrictions" id="age_restrictions"
                                           class="form-control" value="{{ old('age_restrictions', $medicine->age_restrictions) }}"
                                           placeholder="e.g., Adults only, 12+ years">
                                    @error('age_restrictions')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description & Indication -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="description" id="description" class="form-control" rows="3"
                                              placeholder="Brief description of the medicine...">{{ old('description', $medicine->description) }}</textarea>
                                    @error('description')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Indication -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="indication">Indication <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="indication" id="indication" class="form-control" rows="3"
                                              placeholder="What is this medicine used to treat?">{{ old('indication', $medicine->indication) }}</textarea>
                                    @error('indication')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes mr-2"></i>Inventory Management
                        </h3>
                        <div class="card-tools">
                            <small class="text-muted">Note: Use stock management for quantity updates</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Basic Stock Information -->
                        <div class="row">
                            <!-- Stock Number -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="stock_number">Stock Number <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="stock_number" id="stock_number"
                                           class="form-control" value="{{ old('stock_number', $medicine->stock_number) }}" 
                                           placeholder="e.g., MED-001">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Internal stock tracking number
                                    </small>
                                    @error('stock_number')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Unit Measure -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="unit_measure">Unit Measure <span class="badge badge-secondary">Optional</span></label>
                                    <select name="unit_measure" id="unit_measure" class="form-control">
                                        <option value="">-- Select Unit --</option>
                                        <option value="pc" {{ old('unit_measure', $medicine->unit_measure) == 'pc' ? 'selected' : '' }}>Piece (pc)</option>
                                        <option value="bottle" {{ old('unit_measure', $medicine->unit_measure) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                        <option value="vial" {{ old('unit_measure', $medicine->unit_measure) == 'vial' ? 'selected' : '' }}>Vial</option>
                                        <option value="box" {{ old('unit_measure', $medicine->unit_measure) == 'box' ? 'selected' : '' }}>Box</option>
                                        <option value="tube" {{ old('unit_measure', $medicine->unit_measure) == 'tube' ? 'selected' : '' }}>Tube</option>
                                        <option value="sachet" {{ old('unit_measure', $medicine->unit_measure) == 'sachet' ? 'selected' : '' }}>Sachet</option>
                                        <option value="ml" {{ old('unit_measure', $medicine->unit_measure) == 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                                        <option value="mg" {{ old('unit_measure', $medicine->unit_measure) == 'mg' ? 'selected' : '' }}>Milligram (mg)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Unit of measurement for inventory
                                    </small>
                                    @error('unit_measure')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <!-- Minimum Stock -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="minimum_stock">Minimum Stock Level <span class="badge badge-secondary">Optional</span></label>
                                    <input type="number" name="minimum_stock" id="minimum_stock"
                                           class="form-control" value="{{ old('minimum_stock', $medicine->minimum_stock) }}" min="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Low stock alert threshold
                                    </small>
                                    @error('minimum_stock')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Count Information -->
                        <div class="row">
                            <!-- Stock Quantity -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="stock_quantity">Current Stock Quantity</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" 
                                           value="{{ $medicine->stock_quantity }}" readonly>
                                    <small class="form-text text-info">
                                        <i class="fas fa-info-circle mr-1"></i>Use "Update Stock" button to change quantity
                                    </small>
                                </div>
                            </div>

                            <!-- Balance Per Card -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="balance_per_card">Balance Per Card <span class="badge badge-secondary">Optional</span></label>
                                    <input type="number" name="balance_per_card" id="balance_per_card"
                                           class="form-control" value="{{ old('balance_per_card', $medicine->balance_per_card) }}" min="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Expected balance according to records
                                    </small>
                                    @error('balance_per_card')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- On Hand Per Count -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="on_hand_per_count">On Hand Per Count <span class="badge badge-secondary">Optional</span></label>
                                    <input type="number" name="on_hand_per_count" id="on_hand_per_count"
                                           class="form-control" value="{{ old('on_hand_per_count', $medicine->on_hand_per_count) }}" min="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Actual count in physical inventory
                                    </small>
                                    @error('on_hand_per_count')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Shortage/Overage -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shortage_overage">Shortage/Overage</label>
                                    <input type="number" name="shortage_overage" id="shortage_overage" 
                                           class="form-control auto-calculated" value="{{ old('shortage_overage', $medicine->calculated_shortage_overage) }}" readonly>
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
                                    <label for="inventory_remarks">Inventory Remarks <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="inventory_remarks" id="inventory_remarks"
                                           class="form-control" value="{{ old('inventory_remarks', $medicine->inventory_remarks) }}"
                                           placeholder="Any inventory notes...">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>Special notes for inventory tracking
                                    </small>
                                    @error('inventory_remarks')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier">Supplier <span class="badge badge-secondary">Optional</span></label>
                                    <input type="text" name="supplier" id="supplier" class="form-control"
                                           placeholder="Enter supplier name" 
                                           value="{{ old('supplier', $medicine->supplier) }}">
                                    @error('supplier')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="batch_number" class="required">Batch Number</label>
                                    <input type="text" name="batch_number" id="batch_number" class="form-control" 
                                           placeholder="Enter batch number" 
                                           value="{{ old('batch_number', $medicine->batch_number) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-exclamation-triangle text-warning"></i> <strong>Warning:</strong> Changing batch number creates a new batch identity
                                    </small>
                                    @error('batch_number')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Related Batches Info -->
                        @php
                            $otherBatches = $medicine->getOtherBatches();
                        @endphp
                        @if($otherBatches->count() > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-layer-group"></i> Other Batches of "{{ $medicine->medicine_name }}"</h5>
                                    <ul class="mb-0 pl-3">
                                        @foreach($otherBatches as $batch)
                                        <li>
                                            <strong>Batch:</strong> {{ $batch->batch_number }} | 
                                            <strong>Stock:</strong> {{ $batch->stock_quantity }} | 
                                            <strong>Expiry:</strong> {{ $batch->expiry_date ? $batch->expiry_date->format('M d, Y') : 'N/A' }}
                                            @if($batch->is_expired)
                                                <span class="badge badge-danger ml-2">Expired</span>
                                            @elseif($batch->is_expiring_soon)
                                                <span class="badge badge-warning ml-2">Expiring Soon</span>
                                            @else
                                                <span class="badge badge-success ml-2">Active</span>
                                            @endif
                                            <a href="{{ route('medicines.edit', $batch) }}" class="btn btn-xs btn-outline-primary ml-2">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <small class="text-muted mt-2 d-block">Total {{ $otherBatches->count() + 1 }} batch(es) for this medicine</small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Additional Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturing_date">Manufacturing Date <span class="badge badge-secondary">Optional</span></label>
                                    <input type="date" name="manufacturing_date" id="manufacturing_date" class="form-control"
                                           value="{{ old('manufacturing_date', $medicine->manufacturing_date ? $medicine->manufacturing_date->format('Y-m-d') : '') }}">
                                    @error('manufacturing_date')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date <span class="badge badge-secondary">Optional</span></label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                                           value="{{ old('expiry_date', $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '') }}">
                                    @error('expiry_date')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="storage_conditions">Storage Conditions <span class="badge badge-secondary">Optional</span></label>
                            <textarea name="storage_conditions" id="storage_conditions" class="form-control" rows="2"
                                      placeholder="e.g., Store in a cool, dry place below 30°C">{{ old('storage_conditions', $medicine->storage_conditions) }}</textarea>
                            @error('storage_conditions')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Side Effects & Contraindications -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="side_effects">Side Effects <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="side_effects" id="side_effects" class="form-control" rows="3"
                                              placeholder="List common side effects...">{{ old('side_effects', $medicine->side_effects) }}</textarea>
                                    @error('side_effects')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contraindications">Contraindications <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="contraindications" id="contraindications" class="form-control" rows="3"
                                              placeholder="List contraindications...">{{ old('contraindications', $medicine->contraindications) }}</textarea>
                                    @error('contraindications')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Drug Interactions & Pregnancy Category -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="drug_interactions">Drug Interactions <span class="badge badge-secondary">Optional</span></label>
                                    <textarea name="drug_interactions" id="drug_interactions" class="form-control" rows="3"
                                              placeholder="List known drug interactions...">{{ old('drug_interactions', $medicine->drug_interactions) }}</textarea>
                                    @error('drug_interactions')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pregnancy_category">Pregnancy Category <span class="badge badge-secondary">Optional</span></label>
                                    <select name="pregnancy_category" id="pregnancy_category" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($pregnancyCategories as $key => $categoryName)
                                            <option value="{{ $key }}" {{ old('pregnancy_category', $medicine->pregnancy_category) == $key ? 'selected' : '' }}>{{ $categoryName }}</option>
                                        @endforeach
                                    </select>
                                    @error('pregnancy_category')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warnings -->
                        <div class="form-group">
                            <label for="warnings">Warnings <span class="badge badge-secondary">Optional</span></label>
                            <textarea name="warnings" id="warnings" class="form-control" rows="3"
                                      placeholder="Important warnings and precautions...">{{ old('warnings', $medicine->warnings) }}</textarea>
                            @error('warnings')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Current Medicine Image -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-image mr-2"></i>Medicine Image
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div id="currentImage" class="mb-3">
                            @if($medicine->medicine_image)
                                <img src="{{ $medicine->medicine_image }}" 
                                     alt="{{ $medicine->medicine_name }}" class="img-fluid rounded medicine-image" data-fallback="true">
                            @else
                                <div class="medicine-placeholder">
                                    <i class="fas fa-pills fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">No current image</p>
                                </div>
                            @endif
                        </div>
                        
                        <div id="imagePreview" class="mb-3" style="display: none;">
                            <i class="fas fa-pills fa-4x text-muted"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="medicine_image">Update Image</label>
                            <input type="file" name="medicine_image" id="medicine_image" class="form-control-file" 
                                   accept="image/*">
                            <small class="form-text text-muted">Upload a new image (JPEG, PNG, GIF, WebP - Max 10MB). Leave empty to keep current image. Images are stored securely via ImgBB.</small>
                            @error('medicine_image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Medicine Settings -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs mr-2"></i>Settings
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="prescription_required">Prescription Required</label>
                            <select name="prescription_required" id="prescription_required" class="form-control">
                                <option value="0" {{ old('prescription_required', $medicine->prescription_required) == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('prescription_required', $medicine->prescription_required) == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('prescription_required')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="active" {{ old('status', $medicine->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $medicine->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="discontinued" {{ old('status', $medicine->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                            @error('status')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt mr-2"></i>Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success btn-sm btn-block mb-2" data-toggle="modal" data-target="#stockModal">
                            <i class="fas fa-plus mr-2"></i>Update Stock
                        </button>
                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-info btn-sm btn-block mb-2">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('prescriptions.create', ['medicine_id' => $medicine->id]) }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-prescription mr-2"></i>Create Prescription
                        </a>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sticky-note mr-2"></i>Notes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <textarea name="notes" id="notes" class="form-control" rows="4" 
                                      placeholder="Any additional notes or comments...">{{ old('notes', $medicine->notes) }}</textarea>
                            @error('notes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card">
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i>Update Medicine
                        </button>
                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-secondary">
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
                            <input type="number" name="quantity" id="stock_quantity_modal" class="form-control" min="0" required>
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

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Medicine Update Preview</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="medicinePreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmUpdate">
                        <i class="fas fa-check mr-2"></i>Confirm & Update Medicine
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('css')
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .text-danger {
        font-weight: 500;
    }
    
    .medicine-placeholder {
        padding: 40px 20px;
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
    }
    
    #imagePreview {
        border: 2px dashed #dee2e6;
        padding: 20px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    #imagePreview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 5px;
    }
    
    .input-group-text {
        background-color: #e9ecef;
        border-color: #ced4da;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .auto-calculated {
        background-color: #e9ecef;
        border-style: dashed;
    }
    
    .inventory-section {
        background-color: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 15px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Handle image loading errors
    $('.medicine-image[data-fallback="true"]').on('error', function() {
        $(this).replaceWith('<div class="medicine-placeholder"><i class="fas fa-pills fa-4x text-muted mb-3"></i><p class="text-muted">Image not available</p></div>');
    });
    
    // Image preview
    $('#medicine_image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#currentImage').hide();
                $('#imagePreview').html(`<img src="${e.target.result}" alt="New Medicine Image" class="img-fluid">`).show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#currentImage').show();
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
    
    // Initialize calculations on page load
    calculateShortageOverage();

    // Validate expiry date
    $('#expiry_date').change(function() {
        const manufacturingDate = new Date($('#manufacturing_date').val());
        const expiryDate = new Date($(this).val());
        
        if (manufacturingDate && expiryDate && expiryDate <= manufacturingDate) {
            alert('Expiry date must be after manufacturing date');
            $(this).val('');
        }
    });

    // Manufacturing date validation
    $('#manufacturing_date').change(function() {
        const selectedDate = new Date($(this).val());
        const today = new Date();
        
        if (selectedDate > today) {
            alert('Manufacturing date cannot be in the future');
            $(this).val('');
        }
    });

    // Preview functionality
    $('#previewBtn').click(function(e) {
        e.preventDefault();
        generatePreview();
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
    
    // Submit form via AJAX
    function submitFormViaAjax() {
        var $submitBtn = $('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Updating Medicine...');
        $submitBtn.prop('disabled', true);
        
        // Prepare form data
        var formData = new FormData(document.getElementById('medicineEditForm'));
        formData.append('_method', 'PUT'); // Laravel method spoofing for PUT request
        
        // Submit via AJAX
        $.ajax({
            url: $('#medicineEditForm').attr('action'),
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
                    
                    // Redirect after delay
                    setTimeout(function() {
                        window.location.href = '{{ route("medicines.index") }}';
                    }, 2000);
                } else {
                    showAlert(response.message || 'An error occurred', 'danger');
                }
            },
            error: function(xhr) {
                var errorMessage = 'An error occurred while updating the medicine.';
                
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
    }
    
    // Handle form submission
    $('#medicineEditForm').on('submit', function(e) {
        e.preventDefault();
        submitFormViaAjax();
    });
    
    // Confirm update from preview modal
    $('#confirmUpdate').click(function() {
        $('#previewModal').modal('hide');
        submitFormViaAjax();
    });

    // Generate preview
    function generatePreview() {
        const data = {
            name: $('#medicine_name').val(),
            generic: $('#generic_name').val(),
            brand: $('#brand_name').val(),
            manufacturer: $('#manufacturer').val(),
            category: $('#category option:selected').text(),
            dosageForm: $('#dosage_form option:selected').text(),
            strength: $('#strength').val(),
            description: $('#description').val(),
            stockQuantity: $('#stock_quantity').val(),
            supplier: $('#supplier').val(),
            batchNumber: $('#batch_number').val(),
            manufacturingDate: $('#manufacturing_date').val(),
            expiryDate: $('#expiry_date').val(),
            storageConditions: $('#storage_conditions').val(),
            sideEffects: $('#side_effects').val(),
            contraindications: $('#contraindications').val(),
            prescriptionRequired: $('#prescription_required option:selected').text(),
            minimumStock: $('#minimum_stock').val(),
            status: $('#status option:selected').text(),
            notes: $('#notes').val()
        };

        const preview = `
            <div class="medicine-preview">
                <div class="row">
                    <div class="col-md-8">
                        <h5><i class="fas fa-pills mr-2"></i>${data.name || 'Medicine Name'}</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Generic Name:</strong> ${data.generic || 'N/A'}</p>
                                <p><strong>Brand Name:</strong> ${data.brand || 'N/A'}</p>
                                <p><strong>Manufacturer:</strong> ${data.manufacturer || 'N/A'}</p>
                                <p><strong>Category:</strong> ${data.category || 'N/A'}</p>
                                <p><strong>Dosage Form:</strong> ${data.dosageForm || 'N/A'}</p>
                                <p><strong>Strength:</strong> ${data.strength || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Stock Quantity:</strong> ${data.stockQuantity || '0'}</p>
                                <p><strong>Supplier:</strong> ${data.supplier || 'N/A'}</p>
                                <p><strong>Batch Number:</strong> ${data.batchNumber || 'N/A'}</p>
                                <p><strong>Status:</strong> <span class="badge badge-success">${data.status || 'Active'}</span></p>
                            </div>
                        </div>
                        ${data.description ? `
                            <div class="mt-3">
                                <p><strong>Description:</strong></p>
                                <p class="border p-2">${data.description}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="border p-3 mb-3">
                                <i class="fas fa-pills fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Medicine Image</p>
                            </div>
                            <p><strong>Prescription Required:</strong> ${data.prescriptionRequired}</p>
                            <p><strong>Minimum Stock:</strong> ${data.minimumStock || '10'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Changes will be applied to Medicine #{{ $medicine->id }}</strong>
                </div>
            </div>
        `;

        $('#medicinePreview').html(preview);
        $('#previewModal').modal('show');
    }
});
</script>
@endsection