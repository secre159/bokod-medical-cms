# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is a **Medical Clinic Management System** built with Laravel 12, designed for managing patients, appointments, prescriptions, medicine inventory, and medical records. The system supports role-based access (admin/patient) with a patient portal for appointment scheduling and messaging.

## Common Development Commands

### Setup & Installation

```powershell
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (if needed)
php artisan db:seed

# Create storage symlink
php artisan storage:link
```

### Development Server

```powershell
# Start all development services (Laravel server + queue + logs + Vite)
composer dev

# Or manually start individual services:
php artisan serve                    # Start Laravel dev server (http://localhost:8000)
php artisan queue:listen --tries=1   # Start queue worker
php artisan pail --timeout=0         # Start log viewer
npm run dev                          # Start Vite dev server for assets
```

### Testing

```powershell
# Run all tests
composer test
# Or: php artisan test

# Run specific test file
php artisan test tests/Feature/DashboardTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter testUserCanLogin
```

### Database Operations

```powershell
# Fresh migration (WARNING: drops all tables)
php artisan migrate:fresh

# Refresh with seeding
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Create new migration
php artisan make:migration create_table_name
```

### Code Quality

```powershell
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Format specific files
./vendor/bin/pint app/Models

# Check without fixing
./vendor/bin/pint --test
```

### Cache Management

```powershell
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Custom Artisan Commands

```powershell
# Send appointment reminders
php artisan appointments:send-reminders

# Send medication reminders
php artisan medications:send-reminders

# Send stock alerts
php artisan stock:send-alerts

# Check database tables
php artisan db:check-tables

# Cleanup orphaned profile pictures
php artisan cleanup:profile-pictures

# Test email configuration
php artisan email:test
```

## Architecture Overview

### Core Domain Models

The system is organized around healthcare domain entities:

- **Patient**: Central entity with medical records, appointments, prescriptions
- **Appointment**: Scheduling with statuses (pending, approved, completed, cancelled, rescheduled)
- **Prescription**: Medicine prescriptions linked to patients and visits
- **Medicine**: Inventory management with stock tracking and low-stock alerts
- **PatientVisit**: Medical visits with notes and vital signs
- **MedicalNote**: Patient medical history and clinical notes
- **User**: Authentication and authorization (admin/patient roles)

### Key Design Patterns

**Service Layer Architecture**
- Services in `app/Services/` handle complex business logic
- `FileUploadService`: Multi-provider file uploads (ImgBB, Cloudinary, local)
- `EnhancedEmailService`: Email sending with templating
- `AppointmentReminderService`: Automated appointment notifications
- Controllers remain thin, delegating to services

**Repository Pattern (Implicit)**
- Models contain query scopes for common filters (e.g., `Patient::active()`)
- Complex queries encapsulated in service methods

**Middleware Chain**
- `AccountStatusMiddleware`: Blocks inactive/suspended accounts
- `RoleMiddleware`: Role-based access control (admin/patient)
- Auth routes use Laravel Breeze

### Directory Structure

```
app/
├── Console/Commands/     # Custom artisan commands (reminders, cleanups, diagnostics)
├── Helpers/             # Utility classes (TimezoneHelper)
├── Http/
│   ├── Controllers/     # Request handlers
│   │   ├── Admin/       # Admin-specific controllers
│   │   ├── Api/         # API endpoints (image storage)
│   │   └── Auth/        # Authentication controllers
│   └── Middleware/      # Custom middleware (role, account status)
├── Livewire/           # Livewire components (minimal usage)
├── Mail/               # Email templates and classes
├── Models/             # Eloquent models
├── Providers/          # Service providers
├── Rules/              # Custom validation rules
├── Services/           # Business logic layer
├── Traits/             # Reusable model traits
└── View/               # View composers

config/
├── adminlte.php        # AdminLTE UI configuration
├── cloudinary.php      # Cloudinary image storage config
├── image_processing.php # Image handling configuration
└── backup.php          # Database backup settings

routes/
├── web.php             # Main web routes (role-based groups)
├── api.php             # API routes (image storage API)
├── auth.php            # Authentication routes (Breeze)
└── console.php         # Artisan command scheduling

database/
├── migrations/         # Database schema
└── seeders/           # Database seeders

tests/
├── Feature/           # Integration tests
└── Unit/              # Unit tests
```

### Frontend Stack

- **Vite**: Asset bundling (configured in `vite.config.js`)
- **Tailwind CSS**: Utility-first styling
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **AdminLTE**: Admin dashboard theme

Build frontend assets:
```powershell
npm run build        # Production build
npm run dev          # Development with hot reload
```

### Database

**Default**: SQLite for development (see `.env.example`)
**Production**: PostgreSQL (configured via `DATABASE_URL`)

Critical database features:
- Appointment status transitions with validation
- Stock movement tracking for medicines
- Patient archiving (soft-delete alternative)
- Message/conversation system for patient-admin communication

### Image Upload Strategy

The system supports multiple image storage providers:

1. **ImgBB** (preferred for development): Configure in `.env`
   ```
   IMGBB_API_KEY=your_key
   ```

2. **Cloudinary**: Configure in `config/cloudinary.php`
   ```
   CLOUDINARY_URL=cloudinary://...
   ```

3. **Local Storage**: Fallback to `storage/app/public`

Service selection is automatic based on configuration availability (see `FileUploadService`).

### Queue System

Background jobs use database-backed queues:
- Appointment reminder emails
- Medication reminder notifications
- Stock alert emails

Start queue worker: `php artisan queue:listen --tries=1`

### Deployment

**Render.com** (Production):
- Uses `render-build.sh` for build process
- Uses `start.sh` for runtime
- PostgreSQL database via `DATABASE_URL`
- Health check endpoint: `/health`

**Local Development (XAMPP)**:
- SQLite database: `database/database.sqlite`
- File storage: `storage/app/public` linked to `public/storage`
- Mail: Log driver (check `storage/logs/laravel.log`)

## Important Conventions

### Model Relationships
- Always use type hints on relationship methods
- Use `HasMany`, `BelongsTo`, `HasOne` return types
- Eager load relationships to avoid N+1 queries: `Patient::with('appointments')->get()`

### Controllers
- Keep controllers thin - delegate to services
- Return views for web routes, JSON for API routes
- Use Laravel's `authorize()` method for permission checks

### Validation
- Use Form Request classes for complex validation (in `app/Http/Requests/`)
- Custom validation rules in `app/Rules/`

### Naming Conventions
- Controllers: Singular noun + "Controller" (e.g., `PatientController`)
- Models: Singular (e.g., `Patient`, not `Patients`)
- Tables: Plural snake_case (e.g., `patients`, `patient_visits`)
- Routes: Resource naming (`patients.index`, `patients.show`)

### Security
- All routes require authentication by default (via middleware groups)
- Role-based access: Admin routes wrapped in `role:admin` middleware
- CSRF protection enabled (except for API routes)
- Patient data access restricted by user_id

### Testing
- Feature tests for HTTP workflows (in `tests/Feature/`)
- Unit tests for service logic (in `tests/Unit/`)
- Use in-memory SQLite for tests (configured in `phpunit.xml`)
- Tests automatically clear caches before running

## Key Workflows

### Patient Registration Flow
1. Admin creates user account (`UserController@store`)
2. Patient record auto-created with `user_id` link
3. Credentials emailed to patient (via `EnhancedEmailService`)
4. Patient logs in and completes profile

### Appointment Management Flow
1. Patient requests appointment (pending status)
2. Admin reviews and approves/rejects
3. Automated reminders sent 24h before (`SendAppointmentReminders`)
4. Admin marks as completed after visit
5. Can be rescheduled with approval workflow

### Medicine Stock Management
1. Admin adds medicine with initial stock
2. Stock decrements when prescription dispensed
3. Low stock alerts generated automatically
4. `StockMovement` table tracks all changes
5. Physical count reconciliation available

### Messaging System
1. Patient initiates conversation from portal
2. Admin responds from dashboard
3. File attachments supported (images/documents via `FileUploadService`)
4. Real-time message count on dashboard

## Environment-Specific Behavior

### Local Development (`APP_ENV=local`)
- Debug routes enabled (in `routes/debug.php`)
- Verbose error messages
- Mail logged to `storage/logs/laravel.log`

### Production (`APP_ENV=production`)
- Debug routes disabled
- Error messages hidden
- Database backups automated
- Queue workers required for email delivery

## Troubleshooting

### Authentication Issues
```powershell
php artisan config:clear
php artisan cache:clear
php artisan session:table  # If using database sessions
```

### Migration Errors
```powershell
# Check migration status
php artisan migrate:status

# Force run specific migration
php artisan migrate --path=/database/migrations/YYYY_MM_DD_HHMMSS_migration_name.php --force
```

### Queue Not Processing
```powershell
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### File Upload Issues
1. Check storage symlink exists: `php artisan storage:link`
2. Verify storage permissions: `storage/` and `bootstrap/cache/` must be writable
3. Check `.env` for ImgBB/Cloudinary credentials
4. Review logs: `storage/logs/laravel.log`

### Database Constraint Errors
The system includes fix commands for PostgreSQL constraint issues:
```powershell
php artisan db:fix-postgresql-constraint
php artisan db:check-tables
```
