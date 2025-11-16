# Migration Consolidation Deployment Guide

This guide walks you through replacing the 56+ problematic migrations with a single comprehensive migration.

## ⚠️ IMPORTANT: Backup Your Database First!

Before proceeding, ensure you have a recent backup of your database.

## Step 1: Archive Old Migration Files

Run the PowerShell script to move old migrations to an archive folder:

```powershell
.\archive-old-migrations.ps1
```

This will:
- Keep the new comprehensive migration
- Keep Laravel default migrations (cache, jobs tables)
- Move all other migrations to `database/migrations_archive_[timestamp]/`

## Step 2: Connect to Your Database

You have two options:

### Option A: Using PostgreSQL Command Line

```powershell
& "C:\Users\Axl Chan\Downloads\pgsql17\pgsql\bin\psql.exe" -h dpg-d4bd45qli9vc73dd5jfg-a.singapore-postgres.render.com -U bokod_user -d bokod_cms_a1wv
```

### Option B: Using Laravel Tinker

```bash
php artisan tinker
```

Then in Tinker:
```php
DB::table('migrations')->delete();
exit
```

## Step 3: Reset Migrations Table

If using Option A (psql), run the SQL script:

```sql
DELETE FROM migrations;
```

Or simply:

```bash
php artisan db:wipe --force
```

**WARNING:** `db:wipe` will drop ALL tables. Only use this if you want a completely fresh start.

## Step 4: Run the New Comprehensive Migration

```bash
php artisan migrate
```

This will:
- Create all tables with proper schema
- Set up all foreign keys and indexes
- Record the migration in the migrations table

## Step 5: Verify the Migration

Check that all tables exist:

```bash
php artisan tinker
```

Then:
```php
Schema::hasTable('users')
Schema::hasTable('patients')
Schema::hasTable('appointments')
Schema::hasTable('medicines')
Schema::hasTable('prescriptions')
Schema::hasTable('patient_visits')
Schema::hasTable('medical_notes')
Schema::hasTable('conversations')
Schema::hasTable('messages')
Schema::hasTable('settings')
```

All should return `true`.

## Step 6: Test Your Application

```bash
php artisan serve
```

Visit your application and verify:
- ✅ Dashboard loads without errors
- ✅ Patient records are accessible
- ✅ Appointments display correctly
- ✅ No "column not found" errors in logs

## Alternative: Fresh Database Setup

If you want to start completely fresh:

1. **Drop all tables:**
   ```bash
   php artisan db:wipe --force
   ```

2. **Run migrations:**
   ```bash
   php artisan migrate
   ```

3. **Seed default data (if you have seeders):**
   ```bash
   php artisan db:seed
   ```

## Rollback Instructions

If something goes wrong:

1. **Restore archived migrations:**
   ```powershell
   Copy-Item "database\migrations_archive_*\*" "database\migrations\"
   ```

2. **Restore your database backup**

3. **Contact support or review logs**

## Production Deployment (Render)

When deploying to Render:

1. Commit and push the changes:
   ```bash
   git add .
   git commit -m "Consolidate migrations into single comprehensive schema"
   git push
   ```

2. Render will automatically deploy

3. **IMPORTANT:** Before the new deployment goes live, you may need to:
   - Manually reset the migrations table via Render dashboard
   - Or run: `php artisan migrate:fresh` (WARNING: destroys all data)

## Troubleshooting

### "Table already exists" Error

Your tables already exist from the backup restore. The migration has `if (!Schema::hasTable())` checks, so it should skip existing tables. If you still get errors:

```bash
php artisan migrate --pretend
```

This shows what would run without actually running it.

### Foreign Key Constraint Errors

If foreign keys fail:
1. Check that parent tables (users, patients) exist first
2. Verify data integrity (no orphaned records)

### Need to Re-run Migration

```bash
# Remove the migration record
php artisan tinker
DB::table('migrations')->where('migration', '2025_11_16_000000_create_comprehensive_database_schema')->delete();

# Run again
php artisan migrate
```

## Success Indicators

✅ `php artisan migrate` completes without errors
✅ `php artisan migrate:status` shows the new migration
✅ Application loads without "column not found" errors
✅ All CRUD operations work correctly

---

**Created:** 2025-11-16
**Purpose:** Consolidate 56+ problematic migrations into one comprehensive schema
