# Database Seeding Guide

This guide explains how to populate your PostgreSQL database with test data.

## Quick Start

### Option 1: Seed Everything (Recommended for Fresh Install)

```bash
php artisan db:seed
```

This will run all seeders including:
- Department/Course seeder
- Comprehensive data seeder (users, patients, medicines, appointments, prescriptions)

### Option 2: Seed Only Comprehensive Data

```bash
php artisan db:seed --class=ComprehensiveDataSeeder
```

This will populate only the main application data.

---

## What Gets Seeded

### 🔐 Admin Users (3 accounts)

| Name | Email | Password | Role |
|------|-------|----------|------|
| Admin User | admin@bokod.edu.ph | password | admin |
| Dr. Maria Santos | maria.santos@bokod.edu.ph | password | admin |
| Nurse John Reyes | john.reyes@bokod.edu.ph | password | admin |

### 👥 Patient Users (5 accounts with full records)

| Name | Email | Password | Course |
|------|-------|----------|--------|
| Juan Dela Cruz | juan.delacruz@student.bokod.edu.ph | password | BS Computer Science |
| Maria Clara Santos | maria.santos@student.bokod.edu.ph | password | BS Education |
| Pedro Gonzales | pedro.gonzales@student.bokod.edu.ph | password | BS Business Administration |
| Ana Rodriguez | ana.rodriguez@student.bokod.edu.ph | password | BS Nursing |
| Jose Bautista | jose.bautista@student.bokod.edu.ph | password | BS Information Technology |

**All patients include:**
- Complete contact information
- Health data (height, weight, BMI, blood pressure)
- Emergency contact details
- Medical history (for some patients)
- Allergies information (for some patients)

### 💊 Medicines (8 types)

1. **Paracetamol 500mg** - Pain Relief, 500 tablets
2. **Amoxicillin 500mg** - Antibiotic, 300 capsules
3. **Cetirizine 10mg** - Antihistamine, 200 tablets
4. **Multivitamins** - Supplements, 400 tablets
5. **Ibuprofen 400mg** - Anti-inflammatory, 350 tablets
6. **Omeprazole 20mg** - Antacid, 250 capsules
7. **Salbutamol Inhaler** - Bronchodilator, 50 inhalers
8. **Loperamide 2mg** - Antidiarrheal, 180 capsules

**Each medicine includes:**
- Complete drug information
- Stock levels
- Dosage instructions
- Side effects and contraindications
- Prescription requirements

### 📅 Appointments (8 appointments)

- **2 today** - Active and approved
- **2 tomorrow** - 1 approved, 1 pending
- **2 next week** - 1 approved, 1 pending
- **2 past** - Completed with diagnosis and treatment notes

### 💉 Prescriptions (6 prescriptions)

- **4 active prescriptions** - Various medicines
- **1 consultation only** - No medicine prescribed
- **1 completed prescription** - Fully dispensed

---

## Before Seeding

### ⚠️ IMPORTANT: Backup First!

If you have existing data, back it up first:

```bash
# PostgreSQL backup
pg_dump -U your_username -d your_database > backup_$(date +%Y%m%d).sql
```

### Fresh Database (Optional)

If you want to start completely fresh:

```bash
# Drop all tables and re-migrate
php artisan migrate:fresh

# Then seed
php artisan db:seed
```

**WARNING:** `migrate:fresh` will **DELETE ALL DATA** in your database!

---

## After Seeding

### Verify the Data

```bash
# Check users
php artisan tinker
>>> User::count()
>>> User::where('role', 'admin')->count()
>>> User::where('role', 'patient')->count()

# Check patients
>>> Patient::count()

# Check medicines
>>> Medicine::count()

# Check appointments
>>> Appointment::count()

# Check prescriptions
>>> Prescription::count()
```

### Login and Test

1. **Admin Login:**
   - Email: `admin@bokod.edu.ph`
   - Password: `password`

2. **Patient Login:**
   - Email: `juan.delacruz@student.bokod.edu.ph`
   - Password: `password`

3. **Test the Dashboard:**
   - View patients
   - Check appointments
   - Review prescriptions
   - Browse medicine inventory

---

## Seeding Scenarios

### Scenario 1: Fresh Installation

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed all data
php artisan db:seed

# Done! You now have a fully populated database
```

### Scenario 2: Add Test Data to Existing Database

```bash
# Seed only the comprehensive data
php artisan db:seed --class=ComprehensiveDataSeeder
```

**Note:** This may fail if you have duplicate emails. Consider clearing existing test data first.

### Scenario 3: Re-seed Specific Data

If you want to re-seed without dropping tables:

```bash
# Delete existing test data manually, then reseed
php artisan tinker
>>> User::where('email', 'like', '%@bokod.edu.ph')->delete();
>>> Patient::truncate();
>>> Medicine::truncate();
>>> Appointment::truncate();
>>> Prescription::truncate();
>>> exit

# Now reseed
php artisan db:seed --class=ComprehensiveDataSeeder
```

---

## Troubleshooting

### Error: "Duplicate key value violates unique constraint"

**Solution:** You already have data with the same email addresses. Either:
1. Clear existing data first
2. Modify the seeder to use different emails
3. Use `migrate:fresh` to start clean (WARNING: deletes all data)

### Error: "Class ComprehensiveDataSeeder not found"

**Solution:**
```bash
composer dump-autoload
php artisan db:seed --class=ComprehensiveDataSeeder
```

### Error: "SQLSTATE[23503]: Foreign key violation"

**Solution:** Run migrations first to ensure all tables exist:
```bash
php artisan migrate
php artisan db:seed
```

### Error: "No patients found. Skipping appointments."

**Solution:** The patient seeding failed. Check for errors in the patient seeding section and resolve those first.

---

## Customizing the Seeder

The seeder is located at:
```
database/seeders/ComprehensiveDataSeeder.php
```

You can modify:
- Number of users
- Patient details (names, emails, addresses)
- Medicine inventory
- Appointment dates and times
- Prescription details

After modifying, run:
```bash
composer dump-autoload
php artisan db:seed --class=ComprehensiveDataSeeder
```

---

## Production Warning

🚨 **NEVER run seeders on a production database!**

Seeders are for **development and testing only**. Running seeders on production will:
- Add test accounts with default passwords
- Create fake data
- Potentially duplicate or corrupt real data

Always use proper backup and restore procedures for production data.

---

## Summary

✅ **Default password for all accounts:** `password`  
✅ **3 admin accounts** for testing  
✅ **5 patient accounts** with complete records  
✅ **8 medicines** with full details  
✅ **8 appointments** (past, present, future)  
✅ **6 prescriptions** (active, consultation, completed)

**Total execution time:** ~2-5 seconds

Ready to start testing your CMS! 🎉
