# 🔐 Bokod CMS - Login Credentials

## 🚀 **SYSTEM ACCESS INFORMATION**

---

## 👨‍💼 **ADMINISTRATOR ACCESS**

### **Admin User:**
- **Email:** `admin@bokodcms.com`
- **Password:** `admin123`
- **Role:** Administrator
- **Permissions:** Full system access

**Access URL:** `http://localhost/cms/login` (or your configured URL)

---

## 👤 **PATIENT ACCESS**

### **Sample Patient User:**
- **Email:** `patient@bokodcms.com`
- **Password:** `patient123`
- **Role:** Patient
- **Name:** John Doe

**Access URL:** `http://localhost/cms/login` (same login page)

---

## 🧪 **TEST USER** (If Database Seeder was run)

### **Test User:**
- **Email:** `test@example.com`
- **Password:** `password` (Laravel default)
- **Role:** May vary

---

## 🔧 **TO CREATE ADDITIONAL USERS:**

### **Run Admin Seeder (if not already run):**
```bash
php artisan db:seed --class=AdminUserSeeder
```

### **Create New Admin User via Tinker:**
```bash
php artisan tinker
```
```php
App\Models\User::create([
    'name' => 'Your Name',
    'email' => 'your.email@domain.com',
    'password' => bcrypt('yourpassword'),
    'role' => 'admin',
    'status' => 'active',
    'email_verified_at' => now(),
]);
```

### **Create New Patient User:**
```php
$user = App\Models\User::create([
    'name' => 'Patient Name',
    'email' => 'patient.email@domain.com',
    'password' => bcrypt('patientpassword'),
    'role' => 'patient',
    'status' => 'active',
    'email_verified_at' => now(),
]);

// Also create patient record
App\Models\Patient::create([
    'user_id' => $user->id,
    'patient_name' => 'Patient Name',
    'email' => 'patient.email@domain.com',
    'gender' => 'male', // or 'female'
    'archived' => false,
]);
```

---

## 🔑 **PASSWORD RESET** (If needed)

### **Reset Admin Password:**
```bash
php artisan tinker
```
```php
$user = App\Models\User::where('email', 'admin@bokodcms.com')->first();
$user->password = bcrypt('newpassword');
$user->save();
```

---

## 🌐 **SYSTEM URLS**

- **Main Application:** `http://localhost/cms/`
- **Login Page:** `http://localhost/cms/login`
- **Admin Dashboard:** `http://localhost/cms/dashboard` (after login as admin)
- **Patient Dashboard:** `http://localhost/cms/patient/dashboard` (after login as patient)

---

## 📋 **WHAT YOU CAN DO WITH THESE ACCOUNTS**

### **👨‍💼 Admin Account (`admin@bokodcms.com`):**
- ✅ Manage all patients
- ✅ Create/edit/view all appointments  
- ✅ Manage medicine inventory
- ✅ Create prescriptions (including no-medicine consultations!)
- ✅ View all reports and analytics
- ✅ Manage system users
- ✅ Access all system features

### **👤 Patient Account (`patient@bokodcms.com`):**
- ✅ View personal medical history
- ✅ Book appointments
- ✅ View prescriptions
- ✅ Update personal information
- ✅ Patient-specific dashboard

---

## 🎯 **QUICK START GUIDE**

1. **Start XAMPP** (Apache + MySQL)
2. **Access:** `http://localhost/cms/login`
3. **Login with Admin:** `admin@bokodcms.com` / `admin123`
4. **Test New Features:**
   - Go to Prescriptions → Create New Prescription
   - Try all three options: Inventory, Custom, No Medicine Required!
   - Test the collapsible filter cards
   - Explore the enhanced user interface

---

## 🚨 **SECURITY NOTES**

⚠️ **For Production Use:**
- Change all default passwords
- Use strong, unique passwords
- Enable proper SSL/HTTPS
- Configure proper email verification
- Set up proper backup procedures
- Review user permissions regularly

---

**Document Created:** September 18, 2025
**System:** Bokod CMS v2.0 Enhanced
**Status:** ✅ READY FOR USE