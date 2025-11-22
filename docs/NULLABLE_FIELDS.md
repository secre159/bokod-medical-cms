# Nullable/Optional Fields Reference

This document lists all nullable (optional) fields across all database tables to ensure consistency between migrations, models, and validation rules.

## Users Table

### Nullable Fields:
- `email_verified_at` - Email verification timestamp
- `remember_token` - Session token
- `phone` - Phone number
- `date_of_birth` - Date of birth
- `gender` - Gender (Male/Female/Other)
- `address` - Address
- `emergency_contact` - Emergency contact name
- `emergency_phone` - Emergency contact phone
- `medical_history` - Medical history notes
- `allergies` - Allergies list
- `notes` - Additional notes
- `avatar` - Avatar/profile picture path
- `updated_by` - User ID who last updated record
- `approved_at` - Timestamp when registration approved
- `approved_by` - User ID who approved registration
- `rejection_reason` - Reason for registration rejection
- `display_name` - Display name (defaults to name if not provided)
- `profile_picture` - Profile picture URL (ImgBB/Cloudinary)
- `profile_picture_public_id` - Public ID for profile picture (Cloudinary)
- `created_by` - User ID who created this record
- `last_login_at` - Last login timestamp

### Required Fields:
- `name` - Full name
- `email` - Email address (unique)
- `password` - Hashed password
- `role` - User role (admin/patient) - default: 'user'
- `status` - Status (active/inactive) - default: 'active'
- `registration_status` - Registration status (pending/approved/rejected) - default: 'approved'
- `registration_source` - Registration source (admin/self/import) - default: 'self'

---

## Patients Table

### Nullable Fields:
- `address` - Patient address
- `position` - Position/role
- `civil_status` - Civil status (Single/Married/Divorced/Widowed) - default: 'Single'
- `course` - Course/program
- `bmi` - Body Mass Index
- `blood_pressure` - Blood pressure reading (string format)
- `systolic_bp` - Systolic blood pressure (integer)
- `diastolic_bp` - Diastolic blood pressure (integer)
- `contact_person` - Emergency contact person
- `date_of_birth` - Date of birth
- `phone_number` - Phone number
- `phone` - Alternative phone field
- `email` - Email address
- `gender` - Gender (Male/Female/Other) - default: 'Male'
- `user_id` - Associated user ID (foreign key)
- `emergency_contact` - Emergency contact name
- `emergency_phone` - Emergency contact phone
- `emergency_contact_name` - Emergency contact full name
- `emergency_contact_relationship` - Relationship to patient
- `emergency_contact_phone` - Emergency contact phone (duplicate field)
- `emergency_contact_address` - Emergency contact address
- `medical_history` - Medical history
- `allergies` - Known allergies
- `notes` - Additional notes
- `status` - Patient status
- `updated_by` - User ID who last updated record
- `height` - Height (in cm)
- `weight` - Weight (in kg)

### Required Fields:
- `patient_name` - Patient full name
- `archived` - Archive status - default: false

---

## Appointments Table

### Nullable Fields:
- `reason` - Reason for appointment
- `requested_date` - Requested reschedule date
- `requested_time` - Requested reschedule time
- `reschedule_reason` - Reason for reschedule request
- `cancellation_reason` - Reason for cancellation
- `diagnosis` - Diagnosis notes
- `treatment` - Treatment notes (legacy field)
- `treatment_notes` - Treatment notes (new field)
- `vital_signs` - JSONB field for vital signs
- `follow_up_date` - Follow-up appointment date
- `completed_at` - Completion timestamp
- `cancelled_at` - Cancellation timestamp
- `cancelled_by` - User ID who cancelled (foreign key)
- `notes` - Additional notes

### Required Fields:
- `patient_id` - Patient ID (foreign key)
- `appointment_date` - Appointment date
- `appointment_time` - Appointment time
- `status` - Status (active/cancelled/completed/overdue) - default: 'active'
- `approval_status` - Approval status (pending/approved/rejected) - default: 'approved'
- `reschedule_status` - Reschedule status (none/pending) - default: 'none'

---

## Medicines Table

### Nullable Fields:
- `description` - Medicine description
- `strength` - Strength/dosage strength
- `manufacturer` - Manufacturer name
- `batch_number` - Batch/lot number
- `expiry_date` - Expiration date
- `supplier` - Supplier name
- `location` - Storage location
- `notes` - Additional notes
- `side_effects` - Side effects
- `contraindications` - Contraindications
- `generic_name` - Generic name
- `brand_name` - Brand name
- `therapeutic_class` - Therapeutic classification
- `indication` - Indications for use
- `dosage_instructions` - Dosage instructions
- `age_restrictions` - Age restrictions
- `unit_measure` - Unit of measurement
- `balance_per_card` - Balance per card (inventory)
- `on_hand_per_count` - On-hand count (inventory)
- `inventory_remarks` - Inventory remarks
- `manufacturing_date` - Manufacturing date
- `storage_conditions` - Storage conditions
- `drug_interactions` - Drug interactions
- `pregnancy_category` - Pregnancy category (A/B/C/D/X)
- `warnings` - Warnings
- `medicine_image` - Medicine image URL

### Required Fields:
- `medicine_name` - Medicine name (unique)
- `stock_quantity` - Stock quantity - default: 0
- `status` - Status (active/inactive/expired/discontinued) - default: 'active'
- `minimum_stock` - Minimum stock level - default: 10
- `maximum_stock` - Maximum stock level - default: 1000
- `unit` - Unit type - default: 'pieces'
- `category` - Category - default: 'General'
- `dosage_form` - Dosage form - default: 'Tablet'
- `price_per_unit` - Price per unit - default: 0
- `requires_prescription` - Requires prescription - default: false
- `shortage_overage` - Shortage/overage - default: 0

---

## Prescriptions Table

### Nullable Fields:
- `medicine_id` - Medicine ID (foreign key) - null for custom medicines
- `appointment_id` - Appointment ID (foreign key)
- `instructions` - Administration instructions
- `expiry_date` - Prescription expiry date
- `notes` - Additional notes
- `consultation_type` - Type of consultation (for consultations without medicine)
- `unit_price` - Unit price
- `total_amount` - Total amount
- `dispensed_date` - Date dispensed
- `dispensed_by` - User ID who dispensed (foreign key)
- `prescription_number` - Prescription number
- `refills_remaining` - Number of refills remaining
- `frequency` - Dosage frequency
- `duration_days` - Duration in days
- `generic_name` - Generic name (for custom medicines)
- `prescribed_by` - User ID who prescribed (foreign key)
- `remaining_quantity` - Remaining quantity to dispense

### Required Fields:
- `patient_id` - Patient ID (foreign key)
- `medicine_name` - Medicine name (can be "Consultation: Type" for consultations)
- `quantity` - Quantity prescribed
- `dosage` - Dosage information
- `status` - Status (active/completed/cancelled/expired) - default: 'active'
- `prescribed_date` - Date prescribed
- `dispensed_quantity` - Quantity dispensed - default: 0

---

## Validation Rules Summary

All controllers should use `'nullable'` validation for optional fields. Example:

```php
$validated = $request->validate([
    // Required fields
    'patient_name' => 'required|string|max:255',
    'email' => 'required|email|unique:patients,email',
    
    // Nullable/Optional fields
    'phone_number' => 'nullable|string|max:20',
    'date_of_birth' => 'nullable|date|before:today',
    'address' => 'nullable|string',
    'emergency_contact_name' => 'nullable|string|max:255',
    'height' => 'nullable|numeric|between:50,250',
    'weight' => 'nullable|numeric|between:10,300',
    // ... etc
]);
```

## Database Migration Guidelines

1. All optional fields MUST have `->nullable()` in migrations
2. Use `->default(value)` for fields with default values
3. For PostgreSQL, use `ALTER TABLE ... ALTER COLUMN ... DROP NOT NULL` to make existing columns nullable
4. Always check if column exists before adding: `if (!Schema::hasColumn('table', 'column'))`

## Testing Checklist

- [ ] Can create records with only required fields
- [ ] Can create records with all fields
- [ ] Can create records with mixed required and optional fields
- [ ] Null values don't cause query errors
- [ ] Default values are applied correctly
- [ ] Validation passes for nullable fields when empty
- [ ] Validation fails for required fields when empty
