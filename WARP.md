# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**Bokod CMS** is a Laravel-based medical content management system designed for patient management, appointment scheduling, prescription handling, and real-time messaging between healthcare providers and patients. The system features dual user roles (admin/patient) with comprehensive healthcare workflow management.

## Development Setup

### Build and Serve Commands
```powershell
# Initial setup
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed

# Development server (uses concurrently)
composer run dev
# This runs: php artisan serve + php artisan queue:listen + php artisan pail + npm run dev

# Individual services
php artisan serve                    # Laravel server (http://localhost:8000)
npm run dev                         # Vite dev server for assets
php artisan queue:work              # Process background jobs
php artisan pail                    # Laravel log viewer
```

### Testing Commands
```powershell
# Run all tests
composer run test
# Equivalent to: php artisan config:clear && php artisan test

# Individual test commands
php artisan test                    # PHPUnit tests
php artisan test --filter=TestName # Run specific test
```

### Database Management
```powershell
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed    # Fresh migration with seed data
php artisan db:seed --class=AdminUserSeeder  # Create admin user
php artisan tinker                  # Laravel REPL for database operations
```

### Asset Management
```powershell
npm run build                       # Production asset build
npm run dev                        # Development build with hot reload
```

## Core Architecture

### Authentication & Authorization
- **Multi-role system**: Admin and Patient roles with distinct interfaces
- **Authentication**: Laravel Breeze with role-based middleware
- **Role middleware**: `role:admin` and `role:patient` protect routes
- **No email verification**: Users receive credentials directly

### Key Domain Models
- **User**: Base authentication model with role field (`admin`/`patient`)
- **Patient**: Extended patient profile linked to User via `user_id`
- **Appointment**: Complex scheduling system with approval workflow
- **Medicine**: Inventory management with stock tracking
- **Prescription**: Links appointments to medicines with dispensing workflow
- **Conversation/Message**: Real-time messaging between admin and patients

### Database Design
- **Primary Keys**: Mixed approach - some tables use default `id`, others use custom keys (e.g., `appointment_id`)
- **Soft Deletes**: Uses `archived` boolean field instead of Laravel's soft deletes
- **Status Fields**: Extensive use of status enums for workflow management

### Frontend Stack
- **CSS Framework**: TailwindCSS with custom AdminLTE integration
- **JavaScript**: Vanilla JS with Alpine.js for interactivity
- **Build Tool**: Vite (replaces Laravel Mix)
- **Real-time**: Server-Sent Events (SSE) for messaging

## Key Features & Workflows

### Appointment Management
- **Calendar Interface**: FullCalendar.js with drag-and-drop scheduling
- **Approval Workflow**: Three-step process (pending → approved → completed)
- **Status Tracking**: `status` (active/cancelled/completed) and `approval_status` (pending/approved/rejected)
- **Reschedule System**: Patients can request reschedules, admins approve/reject

### Prescription System
- **Three Types**: 
  1. Inventory medicines (stock deduction)
  2. Custom medicines (external prescriptions)
  3. No medicine required (consultation only)
- **Dispensing Workflow**: Created → Dispensed → Complete
- **Stock Management**: Automatic inventory tracking with low-stock alerts

### Real-time Messaging
- **Dual Interface**: Admin messaging dashboard and patient portal
- **File Attachments**: Support for medical documents/images
- **Smart Polling**: Flicker-free message updates with SSE fallback
- **Archive System**: Conversation archiving with swipe-to-archive on mobile

### Patient Portal Features
- **Dashboard**: Health statistics, appointment overview, prescription history
- **Profile Management**: Complete medical profile with emergency contacts
- **Health Tracking**: BMI calculation, blood pressure monitoring
- **Notification System**: Smart notifications for appointments, medications, health reminders

## Development Guidelines

### Code Organization
- **Controllers**: Separate controllers for admin vs patient functionality
- **Models**: Rich models with business logic methods and relationship definitions
- **Services**: Email services, appointment reminders in `app/Services/`
- **Rules**: Custom validation rules in `app/Rules/`

### API Conventions
- **Admin Routes**: Prefix `/admin/` or use `role:admin` middleware
- **Patient Routes**: Prefix `/patient/` or use `role:patient` middleware
- **AJAX Endpoints**: Many routes have corresponding AJAX endpoints for dynamic functionality
- **Route Naming**: Consistent naming with resource controllers (`resource.action`)

### Database Patterns
- **Status Enums**: Use class constants for status values (see `User::STATUS_*`, `Appointment::STATUS_*`)
- **Relationships**: Extensive use of Eloquent relationships - always use these instead of manual queries
- **Scopes**: Common queries implemented as scopes (e.g., `Patient::active()`)

### Frontend Patterns
- **AdminLTE Integration**: Custom service provider for UI components
- **Modal System**: SweetAlert2 for confirmations, custom modals for forms
- **AJAX Handling**: Consistent pattern with loading states and error handling
- **Mobile Responsive**: All interfaces work on mobile devices

## Testing Strategy

### Test Structure
- **Feature Tests**: Focus on HTTP endpoints and user workflows
- **Unit Tests**: Model methods, services, and business logic
- **Database**: Uses SQLite for testing (configured in phpunit.xml)

### Key Test Areas
- Authentication flows for both user types
- Appointment booking and approval workflow
- Prescription creation and dispensing
- Real-time messaging functionality
- Patient portal features

## Environment Configuration

### Local Development
- **Database**: SQLite by default (can switch to MySQL)
- **Queue**: Database driver for simplicity
- **Mail**: Log driver for development (check storage/logs)
- **Cache**: Database cache driver

### Production Considerations
- **XAMPP Deployment**: System designed for XAMPP environments
- **File Storage**: Uses local storage for profile pictures and attachments
- **Queue Processing**: Requires `php artisan queue:work` for background jobs
- **Asset Building**: Run `npm run build` for production assets

## Common Development Tasks

### Creating New Features
1. **Models**: Use Eloquent relationships extensively
2. **Controllers**: Follow existing admin/patient separation pattern  
3. **Routes**: Use route groups with appropriate middleware
4. **Views**: Extend AdminLTE layouts, use existing component patterns
5. **Migrations**: Include proper foreign key constraints

### Debugging Tools
- **Laravel Pail**: Real-time log monitoring (`php artisan pail`)
- **Tinker**: Database REPL for quick queries
- **Debug Routes**: Several `/debug/*` routes available in development
- **Browser DevTools**: Extensive JavaScript console logging for frontend debugging

### User Management
- **Default Credentials**: 
  - Admin: `admin@bokodcms.com` / `admin123`
  - Patient: `patient@bokodcms.com` / `patient123`
- **User Creation**: Use `AdminUserSeeder` or create via Tinker
- **Password Resets**: Available through admin interface or Tinker

## Security Considerations

- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Custom validation rules for medical data
- **File Uploads**: Secure handling with type/size restrictions
- **Role-based Access**: Strict middleware enforcement
- **SQL Injection Prevention**: Eloquent ORM prevents most issues

## Performance Notes

- **Eager Loading**: Most controllers use `with()` to prevent N+1 queries
- **Pagination**: Implemented on all list views
- **Asset Optimization**: Vite handles CSS/JS bundling and minification
- **Database Indexing**: Foreign keys and frequently queried fields are indexed
- **Queue Processing**: Background jobs for email notifications

## Integration Points

- **Email System**: Multiple mail classes for different notification types
- **PDF Generation**: DomPDF integration for prescription exports
- **Image Processing**: Intervention Image for profile picture handling
- **Calendar Events**: FullCalendar.js with backend integration
- **Real-time Updates**: SSE implementation with polling fallback