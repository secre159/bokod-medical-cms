-- Clear All Data Except Admin Account
-- This script removes all data but preserves the admin user (ID 1)

-- Start transaction for safety
BEGIN;

-- Delete dependent data first (respecting foreign keys)
DELETE FROM messages;
DELETE FROM conversations;
DELETE FROM medical_notes;
DELETE FROM patient_visits;
DELETE FROM prescriptions;
DELETE FROM appointments;
DELETE FROM patients;
DELETE FROM medicines;
DELETE FROM settings WHERE key NOT IN ('system_name', 'system_version'); -- Keep system settings if any

-- Delete non-admin users (keep admin with ID 1)
DELETE FROM users WHERE id != 1;

-- Reset sequences to start fresh (optional)
-- This makes new IDs start from 2, 1, etc.
ALTER SEQUENCE patients_id_seq RESTART WITH 1;
ALTER SEQUENCE appointments_appointment_id_seq RESTART WITH 1;
ALTER SEQUENCE medicines_id_seq RESTART WITH 1;
ALTER SEQUENCE prescriptions_id_seq RESTART WITH 1;
ALTER SEQUENCE patient_visits_id_seq RESTART WITH 1;
ALTER SEQUENCE medical_notes_id_seq RESTART WITH 1;
ALTER SEQUENCE conversations_id_seq RESTART WITH 1;
ALTER SEQUENCE messages_id_seq RESTART WITH 1;
ALTER SEQUENCE settings_id_seq RESTART WITH 1;

-- Commit the transaction
COMMIT;

-- Verify what's left
SELECT 'Remaining admin user:' as info;
SELECT id, name, email, role FROM users;

SELECT 'Data counts after cleanup:' as info;
SELECT 
    (SELECT COUNT(*) FROM users) as users_count,
    (SELECT COUNT(*) FROM patients) as patients_count,
    (SELECT COUNT(*) FROM appointments) as appointments_count,
    (SELECT COUNT(*) FROM medicines) as medicines_count,
    (SELECT COUNT(*) FROM prescriptions) as prescriptions_count,
    (SELECT COUNT(*) FROM patient_visits) as visits_count,
    (SELECT COUNT(*) FROM medical_notes) as notes_count,
    (SELECT COUNT(*) FROM conversations) as conversations_count,
    (SELECT COUNT(*) FROM messages) as messages_count;
