{{-- Smart Dosage Helper Component --}}
<div id="dosageHelper" class="mt-2" style="display: none;">
    <div class="card bg-light">
        <div class="card-body p-3">
            <h6 class="mb-3"><i class="fas fa-lightbulb text-warning mr-1"></i>Dosage Quick Builder</h6>
            
            {{-- For Solid Forms (Tablet, Capsule, Patch) --}}
            <div id="solidDosageHelper" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Number of Units</label>
                            <input type="number" class="form-control form-control-sm" id="solid_units" min="0.5" step="0.5" placeholder="e.g., 1, 2, 0.5">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Unit Type</label>
                            <select class="form-control form-control-sm" id="solid_unit_type">
                                <option value="tablet">tablet(s)</option>
                                <option value="capsule">capsule(s)</option>
                                <option value="patch">patch(es)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applySolidDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            {{-- For Liquid Forms (Syrup, Suspension, Solution, Drops) --}}
            <div id="liquidDosageHelper" style="display: none;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Volume</label>
                            <input type="number" class="form-control form-control-sm" id="liquid_volume" min="0.1" step="0.1" placeholder="e.g., 5, 10, 15">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Unit</label>
                            <select class="form-control form-control-sm" id="liquid_unit">
                                <option value="ml">ml (milliliter)</option>
                                <option value="tsp">tsp (teaspoon - 5ml)</option>
                                <option value="tbsp">tbsp (tablespoon - 15ml)</option>
                                <option value="drops">drops</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Measuring Tool</label>
                            <select class="form-control form-control-sm" id="liquid_tool">
                                <option value="">Not specified</option>
                                <option value="syringe">with syringe</option>
                                <option value="cup">with measuring cup</option>
                                <option value="spoon">with measuring spoon</option>
                                <option value="dropper">with dropper</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info alert-sm mb-2 p-2 small">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Measurement Reference:</strong> 1 tsp = 5ml, 1 tbsp = 15ml
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyLiquidDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            {{-- For Injections --}}
            <div id="injectionDosageHelper" style="display: none;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Volume</label>
                            <input type="number" class="form-control form-control-sm" id="injection_volume" min="0.1" step="0.1" placeholder="e.g., 1, 2.5">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Unit</label>
                            <select class="form-control form-control-sm" id="injection_unit">
                                <option value="ml">ml</option>
                                <option value="cc">cc</option>
                                <option value="units">units</option>
                                <option value="IU">IU (International Units)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Route</label>
                            <select class="form-control form-control-sm" id="injection_route">
                                <option value="">Not specified</option>
                                <option value="IV">IV (Intravenous)</option>
                                <option value="IM">IM (Intramuscular)</option>
                                <option value="SC">SC (Subcutaneous)</option>
                                <option value="ID">ID (Intradermal)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyInjectionDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            {{-- For Topical (Cream, Ointment) --}}
            <div id="topicalDosageHelper" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Amount</label>
                            <select class="form-control form-control-sm" id="topical_amount">
                                <option value="thin layer">Thin layer</option>
                                <option value="pea-sized amount">Pea-sized amount</option>
                                <option value="fingertip unit">Fingertip unit</option>
                                <option value="apply liberally">Apply liberally</option>
                                <option value="small amount">Small amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Application Area</label>
                            <input type="text" class="form-control form-control-sm" id="topical_area" placeholder="e.g., affected area, rash">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyTopicalDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            {{-- For Inhaler --}}
            <div id="inhalerDosageHelper" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Number of Puffs</label>
                            <input type="number" class="form-control form-control-sm" id="inhaler_puffs" min="1" step="1" placeholder="e.g., 1, 2">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Inhalation Type</label>
                            <select class="form-control form-control-sm" id="inhaler_type">
                                <option value="puff">puff(s)</option>
                                <option value="dose">dose(s)</option>
                                <option value="inhalation">inhalation(s)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyInhalerDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            {{-- For Other/Generic Forms --}}
            <div id="otherDosageHelper" style="display: none;">
                <div class="alert alert-info alert-sm mb-2 p-2 small">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Common Examples:</strong> 1 suppository, 1 sachet, 1 vial, 2 ampules, 1 lozenge
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Amount/Quantity</label>
                            <input type="number" class="form-control form-control-sm" id="other_amount" min="0.5" step="0.5" placeholder="e.g., 1, 2">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Unit/Form</label>
                            <select class="form-control form-control-sm" id="other_unit">
                                <option value="">-- Select or type custom --</option>
                                <option value="suppository">suppository(ies)</option>
                                <option value="sachet">sachet(s)</option>
                                <option value="vial">vial(s)</option>
                                <option value="ampule">ampule(s)</option>
                                <option value="lozenge">lozenge(s)</option>
                                <option value="pessary">pessary(ies)</option>
                                <option value="spray">spray(s)</option>
                                <option value="strip">strip(s)</option>
                                <option value="custom">Custom (type below)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="customUnitField" style="display: none;">
                    <div class="col-12">
                        <div class="form-group mb-2">
                            <label class="mb-1 small">Custom Unit</label>
                            <input type="text" class="form-control form-control-sm" id="other_custom_unit" placeholder="Enter custom unit (e.g., scoop, stick)">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="applyOtherDosage()">
                    <i class="fas fa-check mr-1"></i>Apply
                </button>
            </div>
            
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>You can still manually edit the dosage field above
                </small>
            </div>
        </div>
    </div>
</div>

<script>
// Show appropriate dosage helper based on medicine form
function updateDosageHelper(dosageForm) {
    // Hide all helpers first
    $('#solidDosageHelper, #liquidDosageHelper, #injectionDosageHelper, #topicalDosageHelper, #inhalerDosageHelper, #otherDosageHelper').hide();
    
    if (!dosageForm) {
        $('#dosageHelper').hide();
        return;
    }
    
    $('#dosageHelper').show();
    
    const form = dosageForm.toLowerCase();
    
    if (['tablet', 'capsule', 'patch'].includes(form)) {
        $('#solidDosageHelper').show();
        $('#solid_unit_type').val(form);
    } else if (['syrup', 'suspension', 'solution', 'drops'].includes(form)) {
        $('#liquidDosageHelper').show();
        if (form === 'drops') {
            $('#liquid_unit').val('drops');
        }
    } else if (form === 'injection') {
        $('#injectionDosageHelper').show();
    } else if (['cream', 'ointment', 'gel'].includes(form)) {
        $('#topicalDosageHelper').show();
    } else if (form === 'inhaler') {
        $('#inhalerDosageHelper').show();
    } else if (form === 'other' || form) {
        // Show Other helper for unrecognized or "Other" forms
        $('#otherDosageHelper').show();
    } else {
        $('#dosageHelper').hide();
    }
}

// Custom unit field toggle
$(document).ready(function() {
    $('#other_unit').change(function() {
        if ($(this).val() === 'custom') {
            $('#customUnitField').show();
            $('#other_custom_unit').attr('required', true);
        } else {
            $('#customUnitField').hide();
            $('#other_custom_unit').attr('required', false);
            $('#other_custom_unit').val('');
        }
    });
});

// Validation helper
function showValidationError(fieldId, message) {
    const $field = $(`#${fieldId}`);
    $field.addClass('is-invalid');
    
    // Remove existing error
    $field.siblings('.invalid-feedback').remove();
    
    // Add new error
    $field.after(`<div class="invalid-feedback d-block">${message}</div>`);
    
    // Remove error after 3 seconds
    setTimeout(() => {
        $field.removeClass('is-invalid');
        $field.siblings('.invalid-feedback').remove();
    }, 3000);
}

// Validation: Check for reasonable ranges
function validateAmount(value, min, max, fieldName) {
    if (!value || value === '') {
        return { valid: false, error: `${fieldName} is required` };
    }
    
    const num = parseFloat(value);
    
    if (isNaN(num)) {
        return { valid: false, error: `${fieldName} must be a number` };
    }
    
    if (num <= 0) {
        return { valid: false, error: `${fieldName} must be greater than 0` };
    }
    
    if (max && num > max) {
        return { valid: false, error: `${fieldName} cannot exceed ${max}` };
    }
    
    if (min && num < min) {
        return { valid: false, error: `${fieldName} cannot be less than ${min}` };
    }
    
    return { valid: true };
}

// Apply functions for each type with validation
function applySolidDosage() {
    const units = $('#solid_units').val();
    const unitType = $('#solid_unit_type option:selected').text();
    
    // Validate
    const validation = validateAmount(units, 0.5, 100, 'Number of units');
    if (!validation.valid) {
        showValidationError('solid_units', validation.error);
        return;
    }
    
    // Warn for unusual amounts
    if (parseFloat(units) > 10) {
        if (!confirm(`You entered ${units} ${unitType}. This is higher than typical. Continue?`)) {
            return;
        }
    }
    
    const dosage = `${units} ${unitType}`;
    $('#dosage').val(dosage);
    showDosageFeedback('success');
}

function applyLiquidDosage() {
    const volume = $('#liquid_volume').val();
    const unit = $('#liquid_unit').val();
    const unitText = $('#liquid_unit option:selected').text();
    const tool = $('#liquid_tool').val();
    
    // Validate volume
    const validation = validateAmount(volume, 0.1, 500, 'Volume');
    if (!validation.valid) {
        showValidationError('liquid_volume', validation.error);
        return;
    }
    
    const volumeNum = parseFloat(volume);
    
    // Check for illogical combinations
    if (unit === 'drops' && volumeNum > 50) {
        if (!confirm(`${volumeNum} drops seems unusually high. Typical range is 1-20 drops. Continue?`)) {
            return;
        }
    }
    
    if (unit === 'ml' && volumeNum > 100) {
        if (!confirm(`${volumeNum} ml is a very large dose. Continue?`)) {
            return;
        }
    }
    
    if (unit === 'tbsp' && volumeNum > 5) {
        if (!confirm(`${volumeNum} tablespoons (${volumeNum * 15}ml) is unusually high. Continue?`)) {
            return;
        }
    }
    
    // Prevent illogical tool combinations
    if (unit === 'drops' && tool === 'cup') {
        showValidationError('liquid_tool', 'Cannot use measuring cup with drops. Use dropper instead.');
        return;
    }
    
    if ((unit === 'tsp' || unit === 'tbsp') && tool === 'syringe') {
        showValidationError('liquid_tool', 'Use measuring spoon for teaspoon/tablespoon measurements.');
        return;
    }
    
    let dosage = `${volume} ${unitText}`;
    if (tool) {
        dosage += ` ${tool}`;
    }
    $('#dosage').val(dosage);
    showDosageFeedback('success');
}

function applyInjectionDosage() {
    const volume = $('#injection_volume').val();
    const unit = $('#injection_unit').val();
    const route = $('#injection_route').val();
    
    // Validate volume
    const maxVolume = unit === 'units' || unit === 'IU' ? 1000 : 100;
    const validation = validateAmount(volume, 0.1, maxVolume, 'Volume');
    if (!validation.valid) {
        showValidationError('injection_volume', validation.error);
        return;
    }
    
    const volumeNum = parseFloat(volume);
    
    // Check for unsafe injection volumes
    if (unit === 'ml' || unit === 'cc') {
        if (volumeNum > 10) {
            if (!confirm(`${volumeNum} ${unit} is a very large injection volume. Typical range is 0.5-5ml. Continue?`)) {
                return;
            }
        }
    }
    
    if (unit === 'units' && volumeNum > 100) {
        if (!confirm(`${volumeNum} units is very high. Verify dosage. Continue?`)) {
            return;
        }
    }
    
    // Check illogical route combinations
    if (route === 'IV' && volumeNum < 0.5 && (unit === 'ml' || unit === 'cc')) {
        if (!confirm(`${volumeNum} ${unit} is very small for IV administration. Continue?`)) {
            return;
        }
    }
    
    let dosage = `${volume} ${unit}`;
    if (route) {
        dosage += ` ${route}`;
    }
    $('#dosage').val(dosage);
    showDosageFeedback('success');
}

function applyTopicalDosage() {
    const amount = $('#topical_amount').val();
    const area = $('#topical_area').val();
    
    if (!amount) {
        showValidationError('topical_amount', 'Amount is required');
        return;
    }
    
    let dosage = amount;
    if (area && area.trim()) {
        dosage += ` to ${area.trim()}`;
    }
    $('#dosage').val(dosage);
    showDosageFeedback('success');
}

function applyInhalerDosage() {
    const puffs = $('#inhaler_puffs').val();
    const type = $('#inhaler_type option:selected').text();
    
    // Validate
    const validation = validateAmount(puffs, 1, 20, 'Number of puffs');
    if (!validation.valid) {
        showValidationError('inhaler_puffs', validation.error);
        return;
    }
    
    // Warn for high puff counts
    if (parseInt(puffs) > 4) {
        if (!confirm(`${puffs} ${type} is higher than typical (1-4). Continue?`)) {
            return;
        }
    }
    
    const dosage = `${puffs} ${type}`;
    $('#dosage').val(dosage);
    showDosageFeedback('success');
}

function applyOtherDosage() {
    const amount = $('#other_amount').val();
    const unit = $('#other_unit').val();
    const customUnit = $('#other_custom_unit').val();
    
    // Validate amount
    const validation = validateAmount(amount, 0.5, 100, 'Amount');
    if (!validation.valid) {
        showValidationError('other_amount', validation.error);
        return;
    }
    
    // Check if unit is selected
    if (!unit) {
        showValidationError('other_unit', 'Please select a unit type');
        return;
    }
    
    // If custom, check custom unit field
    if (unit === 'custom') {
        if (!customUnit || customUnit.trim() === '') {
            showValidationError('other_custom_unit', 'Custom unit is required');
            return;
        }
        
        // Validate custom unit format (no numbers, reasonable length)
        if (customUnit.trim().length > 30) {
            showValidationError('other_custom_unit', 'Unit name too long (max 30 characters)');
            return;
        }
        
        if (/\d/.test(customUnit)) {
            showValidationError('other_custom_unit', 'Unit name should not contain numbers');
            return;
        }
        
        const dosage = `${amount} ${customUnit.trim()}`;
        $('#dosage').val(dosage);
    } else {
        const unitText = $('#other_unit option:selected').text();
        const dosage = `${amount} ${unitText}`;
        $('#dosage').val(dosage);
    }
    
    showDosageFeedback('success');
}

function showDosageFeedback(type) {
    const $dosageField = $('#dosage');
    $dosageField.addClass('border-success');
    setTimeout(() => {
        $dosageField.removeClass('border-success');
    }, 2000);
}
</script>
