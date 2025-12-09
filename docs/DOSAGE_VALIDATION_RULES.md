# Dosage Validation Rules

## Overview
The Smart Dosage Helper includes comprehensive validation to prevent illogical combinations and dangerous dosages. All validations are designed to catch errors while still allowing flexibility for legitimate edge cases.

## Validation Types

### 1. Required Field Validation
- **All numeric inputs**: Must be filled
- **Unit selections**: Must be selected
- **Custom units**: Required when "Custom" is selected

### 2. Range Validation
Ensures amounts are within safe and logical ranges.

### 3. Illogical Combination Prevention
Prevents combinations that don't make medical or practical sense.

### 4. Warning Confirmations
Allows unusual (but possibly legitimate) dosages with explicit confirmation.

---

## Detailed Validation Rules by Form Type

### Solid Forms (Tablets, Capsules, Patches)

#### Range Validation
```
Minimum: 0.5 units
Maximum: 100 units
```

#### Warning Thresholds
- **> 10 units**: "You entered X tablets. This is higher than typical. Continue?"
  - Typical range: 0.5 - 4 units
  - Allows higher doses with confirmation (e.g., vitamin packs)

#### Illogical Combinations Prevented
- None (straightforward form)

#### Examples
✅ **Valid**: 
- "1 tablet(s)"
- "2 capsule(s)"
- "0.5 tablet(s)" (half tablet)

⚠️ **Warning**: 
- "15 tablet(s)" → Confirms before applying

❌ **Blocked**: 
- "0 tablets" → "Number of units must be greater than 0"
- "-1 tablet" → "Number of units must be greater than 0"
- "200 tablets" → "Number of units cannot exceed 100"

---

### Liquid Forms (Syrups, Solutions, Drops)

#### Range Validation
```
Minimum: 0.1 units
Maximum: 500 units (flexible for large volumes like infusions)
```

#### Warning Thresholds by Unit

**Drops:**
- **> 50 drops**: "X drops seems unusually high. Typical range is 1-20 drops. Continue?"
- Typical: 1-20 drops

**Milliliters:**
- **> 100 ml**: "X ml is a very large dose. Continue?"
- Typical: 2.5-30 ml

**Tablespoons:**
- **> 5 tbsp**: "X tablespoons (Xml) is unusually high. Continue?"
- Note: Shows ml conversion (1 tbsp = 15ml)

#### Illogical Combinations Prevented

**Drops + Measuring Cup:**
```
❌ BLOCKED
Error: "Cannot use measuring cup with drops. Use dropper instead."
Reason: Drops require precision; cups are too imprecise
```

**Teaspoon/Tablespoon + Syringe:**
```
❌ BLOCKED
Error: "Use measuring spoon for teaspoon/tablespoon measurements."
Reason: If measuring in spoons, should use spoon, not syringe
```

#### Examples
✅ **Valid**:
- "5 ml (milliliter) with syringe"
- "10 drops with dropper"
- "2 tsp (teaspoon - 5ml) with measuring spoon"
- "1 tbsp (tablespoon - 15ml) with measuring cup"

⚠️ **Warning**:
- "30 drops with dropper" → Confirms (unusually high)
- "150 ml with measuring cup" → Confirms (very large dose)

❌ **Blocked**:
- "5 drops with measuring cup" → Tool incompatible with unit
- "2 tsp with syringe" → Should use measuring spoon
- "0 ml" → Must be greater than 0
- "600 ml" → Exceeds maximum (unless overridden)

---

### Injections

#### Range Validation
```
For ml/cc: 0.1 - 100 ml
For units/IU: 0.1 - 1000 units
```

#### Warning Thresholds by Unit

**Milliliters/CC:**
- **> 10 ml**: "X ml is a very large injection volume. Typical range is 0.5-5ml. Continue?"
- Typical: 0.5-5 ml

**Units (Insulin, etc.):**
- **> 100 units**: "X units is very high. Verify dosage. Continue?"
- Typical: 2-50 units

#### Illogical Combinations Prevented

**IV Route + Very Small Volume:**
```
⚠️ WARNING
< 0.5 ml IV: "X ml is very small for IV administration. Continue?"
Reason: IV typically requires larger volumes; very small volumes unusual
```

#### Examples
✅ **Valid**:
- "2 ml IM"
- "10 units SC"
- "5 ml IV"
- "1 cc IM"

⚠️ **Warning**:
- "15 ml IV" → Confirms (large volume)
- "0.3 ml IV" → Confirms (very small for IV)
- "150 units SC" → Confirms (high dose)

❌ **Blocked**:
- "0 ml" → Must be greater than 0
- "150 ml" → Exceeds maximum for ml

---

### Topical (Creams, Ointments, Gels)

#### Validation
- **Amount**: Must be selected from dropdown (required)
- **Application Area**: Optional but recommended

#### No Illogical Combinations
- Straightforward - amount + area

#### Examples
✅ **Valid**:
- "Thin layer to affected area"
- "Pea-sized amount to rash"
- "Apply liberally"

❌ **Blocked**:
- Empty amount → "Amount is required"

---

### Inhalers

#### Range Validation
```
Minimum: 1 puff
Maximum: 20 puffs
```

#### Warning Thresholds
- **> 4 puffs**: "X puffs is higher than typical (1-4). Continue?"
- Typical: 1-2 puffs per dose

#### Illogical Combinations Prevented
- None (straightforward form)

#### Examples
✅ **Valid**:
- "1 puff(s)"
- "2 dose(s)"

⚠️ **Warning**:
- "6 puff(s)" → Confirms (higher than typical)

❌ **Blocked**:
- "0 puffs" → Must be greater than 0
- "25 puffs" → Exceeds maximum

---

### Other Forms (Suppositories, Sachets, Vials, etc.)

#### Range Validation
```
Minimum: 0.5 units
Maximum: 100 units
```

#### Custom Unit Validation
When "Custom" is selected:

**Required**: Custom unit text field must be filled
**Max Length**: 30 characters
**No Numbers**: Unit names cannot contain digits
**Trimmed**: Leading/trailing spaces removed

#### Examples
✅ **Valid**:
- "1 suppository(ies)"
- "2 sachet(s)"
- "1 vial(s)"
- "3 scoop" (custom unit)
- "1 stick" (custom unit)

❌ **Blocked**:
- Empty unit selection → "Please select a unit type"
- Custom selected but field empty → "Custom unit is required"
- "2 scoop123" → "Unit name should not contain numbers"
- "1 verylongunitnamethatexceedsthirtychars" → "Unit name too long (max 30 characters)"

---

## General Validation Patterns

### Visual Feedback

**Error State (Red Border + Message):**
```javascript
Field turns red
Error message appears below field
Auto-clears after 3 seconds
```

**Success State (Green Flash):**
```javascript
Dosage field briefly flashes green
Confirms successful application
```

### Confirmation Dialogs

**Warning Pattern:**
```javascript
if (condition_unusual_but_valid) {
    if (!confirm("Warning message. Continue?")) {
        return; // Cancel action
    }
}
// Proceed with unusual value
```

**User can:**
- Click "OK" to proceed with unusual value
- Click "Cancel" to go back and modify

---

## Validation Philosophy

### Three-Tier Approach

**1. Hard Blocks (❌)**
- Illogical combinations (drops + cup)
- Invalid formats (text in number field)
- Out of absolute range (> 500ml for liquid)
- Missing required fields

**2. Soft Warnings (⚠️)**
- Unusual but possible values
- Higher than typical dosages
- Edge cases that might be intentional
- Requires explicit user confirmation

**3. Automatic Pass (✅)**
- Values within normal range
- Logical combinations
- Complete required information

### Design Principles

1. **Safety First**: Block dangerous combinations
2. **Flexibility**: Allow legitimate edge cases with confirmation
3. **Education**: Show typical ranges in warnings
4. **User Control**: Manual entry always available as override
5. **Clear Feedback**: Immediate visual/text feedback

---

## Common Scenarios

### Pediatric Dosing
```
Scenario: Very small liquid volumes for infants
Example: "0.5 ml with syringe"
Validation: ✅ Passes (within 0.1-500ml range)
```

### High-Dose Vitamins
```
Scenario: Multiple tablets per dose
Example: "12 tablet(s)"
Validation: ⚠️ Warning → User confirms → ✅ Applies
```

### Eye/Ear Drops
```
Scenario: Multiple drops per dose
Example: "4 drops with dropper"
Validation: ✅ Passes (within 1-50 range)

Scenario: Trying to use wrong tool
Example: "4 drops with measuring cup"
Validation: ❌ Blocked → "Cannot use measuring cup with drops"
```

### Injectable Insulin
```
Scenario: Standard insulin dose
Example: "10 units SC"
Validation: ✅ Passes

Scenario: Very high insulin dose
Example: "150 units SC"
Validation: ⚠️ Warning → User confirms dosage → ✅ Applies
```

### Large Volume Infusions
```
Scenario: IV fluid bolus
Example: "100 ml IV"
Validation: ⚠️ Warning (large volume) → User confirms → ✅ Applies
```

---

## Override Mechanism

**Manual Entry Always Available:**
- User can type directly in dosage field
- No validation on manual entry
- Helper is assistance, not restriction
- Complex/unusual dosages? Type manually

**Example:**
```
Helper doesn't support: "5ml morning, 10ml evening"
Solution: Type manually in dosage field
```

---

## Error Messages Reference

### Common Errors

| Error Message | Cause | Solution |
|---------------|-------|----------|
| "Volume is required" | Empty field | Enter a number |
| "Volume must be greater than 0" | Zero or negative | Enter positive number |
| "Volume cannot exceed 500" | Too large | Reduce amount or use manual entry |
| "Please select a unit type" | No unit selected | Choose from dropdown |
| "Cannot use measuring cup with drops" | Wrong tool | Use dropper instead |
| "Custom unit is required" | Custom selected but empty | Fill in custom unit field |
| "Unit name should not contain numbers" | "scoop5" | Use "scoop" only |

---

## Future Enhancements

Potential validation additions:
1. Patient weight-based dosage calculations
2. Age-specific range adjustments
3. Drug-specific maximum doses
4. Interaction with FEFO batch warnings
5. Cross-reference with frequency (e.g., warn if total daily dose too high)

---

## Testing Validation

### Test Cases to Verify

**Solid Forms:**
- [ ] 0.5 tablets → Pass
- [ ] 15 tablets → Warning
- [ ] -1 tablet → Block

**Liquids:**
- [ ] 5ml + syringe → Pass
- [ ] 30 drops + cup → Block
- [ ] 2 tsp + syringe → Block
- [ ] 150ml + cup → Warning

**Injections:**
- [ ] 2ml IM → Pass
- [ ] 0.3ml IV → Warning
- [ ] 20ml IV → Warning

**Other:**
- [ ] 1 suppository → Pass
- [ ] Custom "scoop5" → Block
- [ ] Custom "" → Block
