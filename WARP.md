# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**Bokod CMS** is a comprehensive healthcare management system built with Laravel 12. It manages patients, appointments, medicines, prescriptions, and secure messaging between healthcare staff and patients. The system includes role-based access control, real-time notifications, inventory management, and reporting capabilities.

**Key Technologies:**
- **Backend**: Laravel 12 (PHP 8.2), PostgreSQL
- **Frontend**: Laravel Blade, TailwindCSS, Alpine.js, AdminLTE
- **Image Processing**: Intervention Image, Cloudinary integration
- **PDF Generation**: DomPDF
- **Email**: Resend service integration
- **Queue System**: Database-based queues
- **Deployment**: Docker, Render.com

## Development Commands

### Initial Setup
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies 
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed

# Create storage symlink
php artisan storage:link
```

### Development Workflow
```bash
# Start complete development environment (recommended)
composer run dev
# This runs: server, queue worker, logs monitor, and Vite in parallel

# Alternative: Start individual services
php artisan serve                    # Development server
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0        # Log monitoring
npm run dev                          # Vite asset compilation
```

### Testing
```bash
# Run all tests
php artisan test
# OR
composer run test

# Run specific test file
php artisan test --filter=PatientTest

# Run tests with coverage
php artisan test --coverage
```

### Code Quality & Maintenance
```bash
# Code formatting (Laravel Pint)
./vendor/bin/pint

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Database operations
php artisan migrate:status           # Check migration status
php artisan migrate:fresh --seed    # Fresh database with seeders
php artisan migrate:rollback         # Rollback last migration batch

# Queue management
php artisan queue:work              # Process queue jobs
php artisan queue:failed            # List failed jobs
php artisan queue:retry all         # Retry failed jobs
```

### Asset Management
```bash
# Development asset compilation
npm run dev

# Production asset build
npm run build

# Watch for changes during development
npm run dev -- --watch
```

## Architecture Overview

### Core Domain Models
- **User**: Authentication and role-based access (`admin`, `staff`, `patient`)
- **Patient**: Medical profiles with demographics, medical history, and vital signs
- **Medicine**: Inventory management with stock tracking and medical properties
- **Appointment**: Scheduling system with approval workflow and calendar integration
- **Prescription**: Medication orders with dispensing tracking and financial records
- **Conversation/Message**: Secure patient-staff communication with file attachments

### Key Architectural Patterns

#### Service Layer Architecture
Services handle complex business logic and external integrations:
- `CloudinaryService`: Image upload and optimization
- `AppointmentReminderService`: Automated appointment notifications
- `EnhancedEmailService`: Email templating and delivery
- `FileUploadService`: File handling with multiple storage providers

#### Role-Based Access Control (RBAC)
- **Admin**: Full system access, user management, system settings
- **Staff**: Patient care, appointment management, prescription handling
- **Patient**: Portal access, appointment booking, messaging

#### Philippine Timezone Management
The system uses `TimezoneHelper` for consistent timezone handling across all date operations, ensuring proper scheduling for Philippine-based healthcare operations.

#### Multi-Storage Image Handling
Supports multiple image storage providers:
- **Cloudinary**: Primary cloud storage with optimization
- **ImgBB**: Alternative cloud storage
- **Local**: Fallback local storage
- **Custom API**: Extensible for additional providers

### Database Architecture

#### Core Tables Structure
```
users ──┬── patients ──┬── appointments ──┬── prescriptions
        │               │                 │
        └── conversations ──── messages   │
                                          │
medicines ────────────── prescription_details
    │
    └── stock_movements
```

#### Key Relationships
- Users have roles and can be linked to Patient profiles
- Patients have medical history, appointments, and prescriptions
- Conversations facilitate secure messaging between staff and patients
- Medicines track inventory with detailed stock movement history
- Appointments integrate with prescriptions for complete visit tracking

### Frontend Architecture

#### AdminLTE Integration
- Dashboard with real-time statistics and async loading
- Responsive navigation with role-based menu items
- DataTables for efficient data management
- Calendar integration for appointment scheduling

#### Alpine.js Components
- Real-time messaging with WebSocket-like updates
- Dynamic form validation and user feedback
- Interactive inventory management interfaces
- Patient portal self-service features

## Database Management

### Migration System
Migrations are organized chronologically with comprehensive field additions. Key migration patterns:
- Emergency fixes for constraint issues (PostgreSQL-specific)
- Comprehensive field additions for medical data
- Profile picture storage consolidation
- Status enum value updates

### Seeding Strategy
```bash
# Core seeders
php artisan db:seed --class=AdminUserSeeder      # Default admin account
php artisan db:seed --class=MedicineSeeder       # Common medicines
php artisan db:seed --class=PatientHistorySeeder # Sample patient data
```

### Constraint Management
The system includes special handling for PostgreSQL constraints, particularly for appointment status enums. Use the constraint fixing utilities when deploying to PostgreSQL:

```bash
# Check constraint status
php artisan db:show

# Run constraint fixes if needed
php artisan migrate --force
```

## File Structure & Key Directories

### Application Structure
```
app/
├── Http/Controllers/          # Request handling and business logic
│   ├── Admin/                # Admin-specific controllers
│   ├── Api/                  # API endpoints
│   └── Auth/                 # Authentication controllers
├── Models/                   # Eloquent models with relationships
├── Services/                 # Business logic and external integrations
├── Mail/                     # Email templates and notifications
├── Rules/                    # Custom validation rules
└── Traits/                   # Reusable model behaviors
```

### Configuration
```
config/
├── adminlte.php             # AdminLTE dashboard configuration
├── cloudinary.php           # Image storage settings
├── image_processing.php     # Image optimization settings
└── resend.php              # Email service configuration
```

### Frontend Resources
```
resources/
├── views/                   # Blade templates
├── css/app.css             # TailwindCSS styles
└── js/app.js               # Alpine.js and frontend logic
```

## Development Guidelines

### Code Organization Principles
- **Controllers**: Keep slim, delegate business logic to Services
- **Models**: Use Eloquent relationships and accessors for data presentation
- **Services**: Handle complex operations, external API calls, and cross-cutting concerns
- **Migrations**: Always include rollback logic, use descriptive naming

### Philippine Healthcare Compliance
- All date/time operations use Philippine timezone (`TimezoneHelper`)
- Medical data follows local healthcare record requirements
- Patient privacy controls comply with data protection standards
- Audit trails for all medical record modifications

### Security Considerations
- Role-based access control throughout the application
- File upload restrictions and validation
- SQL injection protection via Eloquent ORM
- CSRF protection on all forms
- Email verification for user accounts

### Performance Optimization
- Eager loading for relationships to prevent N+1 queries
- Database indexing on frequently queried fields
- Async statistics loading on dashboard
- Image optimization through Cloudinary
- Queue-based email processing

## Testing Strategy

### Test Structure
```
tests/
├── Feature/                 # Integration tests
│   ├── ExampleTest.php
│   └── ProfileTest.php
└── Unit/                   # Unit tests
    └── ExampleTest.php
```

### Testing Database
- Uses in-memory SQLite for fast test execution
- Separate test environment configuration
- Database transactions for test isolation

### Running Specific Tests
```bash
# Test specific functionality
php artisan test --filter=AppointmentTest
php artisan test --filter=PatientManagement
php artisan test tests/Feature/ProfileTest.php

# Test with specific database
php artisan test --env=testing
```

## Deployment & Production

### Docker Deployment
The application includes a complete Docker setup optimized for Render.com deployment:

```dockerfile
# Key features in Dockerfile:
- PHP 8.2 with all required extensions
- PostgreSQL support
- Image processing capabilities
- Optimized for production deployment
```

### Environment Configuration
- **Development**: SQLite database, local file storage
- **Production**: PostgreSQL, Cloudinary for images, email service integration
- **Staging**: Production-like setup with debug enabled

### Deployment Process
```bash
# Production deployment steps are automated in start.sh:
1. Database connection verification
2. Migration execution with force flag
3. Cache clearing and optimization
4. Application server startup
```

### Health Monitoring
- `/health` endpoint for service monitoring
- Automatic database connection retries
- Comprehensive logging with different log levels per environment

## Common Tasks

### Patient Management
```bash
# Create new patient with medical history
# Access via: /patients/create

# Import patient data in bulk
php artisan db:seed --class=PatientHistorySeeder

# Reset patient passwords
# Through admin interface or via UserController methods
```

### Inventory Management
```bash
# Update medicine stock levels
# Access via: /medicines/stock-management

# Generate stock reports
# Built-in export functionality in MedicineController

# Set up low stock alerts
# Configured through medicine inventory settings
```

### Appointment System
```bash
# Access calendar interface
# Navigate to: /appointments/calendar

# Process pending approvals
# Dashboard shows pending count with direct links

# Generate appointment reports
# Available in: /reports/appointments
```

### Messaging System
```bash
# Monitor unread messages
# Dashboard shows real-time counts

# Archive conversations
# Built-in archiving system for message organization

# File attachment handling
# Automatic file type validation and storage
```

This WARP.md provides comprehensive guidance for working with the Bokod CMS healthcare management system, covering everything from development setup to production deployment and common administrative tasks.