# 🌱 Database Seeding - Quick Start

## Run the Seeder

```bash
php artisan db:seed --class=ComprehensiveDataSeeder
```

## What You Get

### 👤 3 Admin Accounts
- **admin@bokod.edu.ph** / password
- **maria.santos@bokod.edu.ph** / password  
- **john.reyes@bokod.edu.ph** / password

### 🏥 5 Patient Accounts
- **juan.delacruz@student.bokod.edu.ph** / password
- **maria.santos@student.bokod.edu.ph** / password
- **pedro.gonzales@student.bokod.edu.ph** / password
- **ana.rodriguez@student.bokod.edu.ph** / password
- **jose.bautista@student.bokod.edu.ph** / password

### 💊 8 Medicines
- Paracetamol, Amoxicillin, Cetirizine, Multivitamins, Ibuprofen, Omeprazole, Salbutamol, Loperamide

### 📅 8 Appointments
- 2 today, 2 tomorrow, 2 next week, 2 completed

### 💉 6 Prescriptions
- 4 active, 1 consultation, 1 completed

---

## Fresh Start (Deletes All Data!)

```bash
php artisan migrate:fresh --seed
```

⚠️ **WARNING:** This will delete ALL existing data!

---

## Need Help?

See full documentation: `docs/DATABASE_SEEDING.md`
