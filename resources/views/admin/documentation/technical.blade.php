@extends('adminlte::page')

@section('title', 'Technical Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-code"></i> Technical Documentation
            <small class="text-muted">Complete technical guide for developers and system administrators</small>
        </h1>
        <a href="{{ route('admin.documentation.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Documentation
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Table of Contents -->
        <div class="sticky-toc">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Table of Contents</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#system-architecture">System Architecture</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#technology-stack">Technology Stack</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#database-structure">Database Structure</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#api-endpoints">API Endpoints</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#security-implementation">Security Implementation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#deployment-guide">Deployment Guide</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#development-setup">Development Setup</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#troubleshooting">Troubleshooting</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- System Architecture Section -->
        <div class="card" id="system-architecture">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sitemap"></i> System Architecture</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About the Clinic Management System:</strong> 
                    This is a comprehensive healthcare management solution built with modern web technologies for efficient clinic operations.
                </div>

                <h5><i class="fas fa-layer-group text-primary"></i> Architecture Overview</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>MVC Architecture Pattern:</strong></p>
                        <ul>
                            <li><strong>Model:</strong> Eloquent ORM models for data management</li>
                            <li><strong>View:</strong> Blade templating with AdminLTE framework</li>
                            <li><strong>Controller:</strong> Laravel controllers handling business logic</li>
                        </ul>
                        
                        <p><strong>Application Layers:</strong></p>
                        <ol>
                            <li><strong>Presentation Layer:</strong> AdminLTE UI with responsive design</li>
                            <li><strong>Business Logic Layer:</strong> Laravel controllers and services</li>
                            <li><strong>Data Access Layer:</strong> Eloquent ORM with MySQL database</li>
                            <li><strong>Security Layer:</strong> Laravel authentication and authorization</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-puzzle-piece text-success"></i> Core Components</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Patient Management:</strong></p>
                                <ul>
                                    <li>Patient registration and profiles</li>
                                    <li>Medical history tracking</li>
                                    <li>Document management</li>
                                    <li>Search and filtering systems</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Appointment System:</strong></p>
                                <ul>
                                    <li>Scheduling engine</li>
                                    <li>Calendar integration</li>
                                    <li>Approval workflows</li>
                                    <li>Notification system</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Medicine Management:</strong></p>
                                <ul>
                                    <li>Inventory tracking</li>
                                    <li>Prescription management</li>
                                    <li>Stock alerts</li>
                                    <li>Dispensing workflows</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Communication:</strong></p>
                                <ul>
                                    <li>Internal messaging</li>
                                    <li>Email notifications</li>
                                    <li>Report generation</li>
                                    <li>Analytics dashboard</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technology Stack Section -->
        <div class="card" id="technology-stack">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools"></i> Technology Stack</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-server text-primary"></i> Backend Technologies</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fab fa-laravel"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Framework</span>
                                        <span class="info-box-number">Laravel 12.x</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fab fa-php"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Language</span>
                                        <span class="info-box-number">PHP 8.2+</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-database"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Database</span>
                                        <span class="info-box-number">MySQL 8.0+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p><strong>Key Laravel Features Used:</strong></p>
                        <ul>
                            <li><strong>Eloquent ORM:</strong> Database abstraction and relationships</li>
                            <li><strong>Blade Templating:</strong> Dynamic view rendering</li>
                            <li><strong>Middleware:</strong> Request filtering and authentication</li>
                            <li><strong>Form Requests:</strong> Input validation</li>
                            <li><strong>Events & Notifications:</strong> Email and system notifications</li>
                            <li><strong>Queues:</strong> Background job processing</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-desktop text-success"></i> Frontend Technologies</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fab fa-bootstrap"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">UI Framework</span>
                                        <span class="info-box-number">AdminLTE 3</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fab fa-js"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">JavaScript</span>
                                        <span class="info-box-number">jQuery + ES6</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-secondary"><i class="fab fa-css3-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Styling</span>
                                        <span class="info-box-number">Bootstrap 4</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p><strong>Frontend Libraries:</strong></p>
                        <ul>
                            <li><strong>DataTables:</strong> Advanced table functionality</li>
                            <li><strong>Chart.js:</strong> Data visualization and charts</li>
                            <li><strong>Select2:</strong> Enhanced select boxes</li>
                            <li><strong>SweetAlert2:</strong> Beautiful alert dialogs</li>
                            <li><strong>FontAwesome:</strong> Icon library</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-cloud text-info"></i> Development & Deployment Tools</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Development Environment:</strong></p>
                        <ul>
                            <li><strong>Composer:</strong> PHP dependency management</li>
                            <li><strong>NPM/Yarn:</strong> Frontend package management</li>
                            <li><strong>Laravel Mix:</strong> Asset compilation</li>
                            <li><strong>Artisan:</strong> Command-line interface</li>
                        </ul>
                        
                        <p><strong>Deployment Options:</strong></p>
                        <ul>
                            <li><strong>Render.com:</strong> Cloud hosting platform</li>
                            <li><strong>Heroku:</strong> Alternative cloud deployment</li>
                            <li><strong>VPS/Dedicated Server:</strong> Custom deployment</li>
                            <li><strong>Docker:</strong> Containerized deployment</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Structure Section -->
        <div class="card" id="database-structure">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-database"></i> Database Structure</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-table text-primary"></i> Core Tables Overview</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Table Name</th>
                                        <th>Purpose</th>
                                        <th>Key Relationships</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>users</code></td>
                                        <td>System users (admins and patients)</td>
                                        <td>patients, messages, appointments</td>
                                    </tr>
                                    <tr>
                                        <td><code>patients</code></td>
                                        <td>Patient information and medical data</td>
                                        <td>users, appointments, prescriptions</td>
                                    </tr>
                                    <tr>
                                        <td><code>appointments</code></td>
                                        <td>Scheduled appointments and visits</td>
                                        <td>patients, users</td>
                                    </tr>
                                    <tr>
                                        <td><code>medicines</code></td>
                                        <td>Medicine catalog and inventory</td>
                                        <td>prescriptions, stock_movements</td>
                                    </tr>
                                    <tr>
                                        <td><code>prescriptions</code></td>
                                        <td>Medical prescriptions</td>
                                        <td>patients, medicines</td>
                                    </tr>
                                    <tr>
                                        <td><code>conversations</code></td>
                                        <td>Message conversations</td>
                                        <td>patients, users, messages</td>
                                    </tr>
                                    <tr>
                                        <td><code>messages</code></td>
                                        <td>Individual messages</td>
                                        <td>conversations, users</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-link text-success"></i> Key Relationships</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>User-Patient Relationship:</strong></p>
                        <ul>
                            <li>One-to-One: Each patient has one user account</li>
                            <li>Polymorphic: Users can be admins or patients</li>
                            <li>Role-based access control</li>
                        </ul>
                        
                        <p><strong>Appointment Relationships:</strong></p>
                        <ul>
                            <li>Belongs to Patient (foreign key: patient_id)</li>
                            <li>Can be assigned to Admin (foreign key: admin_id)</li>
                            <li>Has status tracking and approval workflow</li>
                        </ul>
                        
                        <p><strong>Medicine-Prescription Relationships:</strong></p>
                        <ul>
                            <li>Many-to-Many: Prescriptions can have multiple medicines</li>
                            <li>Pivot table: prescription_medicine with quantities</li>
                            <li>Stock tracking through medicine inventory</li>
                        </ul>
                        
                        <p><strong>Messaging Relationships:</strong></p>
                        <ul>
                            <li>Conversations between patients and admins</li>
                            <li>One-to-Many: Conversation has many messages</li>
                            <li>Message threading and read status tracking</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-key text-warning"></i> Database Indexes & Performance</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Key Indexes for Performance:</strong></p>
                        <ul>
                            <li><strong>users:</strong> email (unique), role, status</li>
                            <li><strong>patients:</strong> user_id, phone, email</li>
                            <li><strong>appointments:</strong> patient_id, appointment_date, status</li>
                            <li><strong>medicines:</strong> name, generic_name, stock_quantity</li>
                            <li><strong>messages:</strong> conversation_id, sender_id, created_at</li>
                        </ul>
                        
                        <p><strong>Performance Optimizations:</strong></p>
                        <ul>
                            <li>Eager loading for relationships</li>
                            <li>Database query optimization</li>
                            <li>Caching for frequently accessed data</li>
                            <li>Pagination for large datasets</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints Section -->
        <div class="card" id="api-endpoints">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt"></i> API Endpoints</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> 
                    The system primarily uses web routes. API endpoints are available for specific integrations and mobile app support.
                </div>

                <h5><i class="fas fa-heartbeat text-primary"></i> Health Check Endpoints</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Endpoint</th>
                                        <th>Description</th>
                                        <th>Response</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-success">GET</span></td>
                                        <td><code>/health</code></td>
                                        <td>System health check</td>
                                        <td>JSON status response</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <p><strong>Example Response:</strong></p>
                        <pre class="bg-light p-3"><code>{
  "status": "healthy",
  "service": "Clinic Management System",
  "timestamp": "2025-09-28T13:54:34.000000Z",
  "uptime": "running"
}</code></pre>
                    </div>
                </div>

                <h5><i class="fas fa-mobile-alt text-success"></i> Mobile API Routes (Future)</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Authentication Endpoints:</strong></p>
                        <ul>
                            <li><code>POST /api/auth/login</code> - User authentication</li>
                            <li><code>POST /api/auth/logout</code> - User logout</li>
                            <li><code>GET /api/auth/user</code> - Get authenticated user</li>
                        </ul>
                        
                        <p><strong>Patient Endpoints:</strong></p>
                        <ul>
                            <li><code>GET /api/patients/profile</code> - Get patient profile</li>
                            <li><code>PUT /api/patients/profile</code> - Update patient profile</li>
                            <li><code>GET /api/patients/appointments</code> - Get patient appointments</li>
                            <li><code>GET /api/patients/prescriptions</code> - Get patient prescriptions</li>
                        </ul>
                        
                        <p><strong>Messaging Endpoints:</strong></p>
                        <ul>
                            <li><code>GET /api/messages</code> - Get message conversations</li>
                            <li><code>POST /api/messages</code> - Send new message</li>
                            <li><code>GET /api/messages/{conversation}</code> - Get conversation messages</li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                            These API endpoints are planned for future mobile app integration.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Implementation Section -->
        <div class="card" id="security-implementation">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt"></i> Security Implementation</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-lock text-danger"></i> Authentication & Authorization</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Laravel Authentication Features:</strong></p>
                        <ul>
                            <li><strong>Session-based Authentication:</strong> Secure session management</li>
                            <li><strong>CSRF Protection:</strong> Cross-site request forgery prevention</li>
                            <li><strong>Password Hashing:</strong> Bcrypt encryption for passwords</li>
                            <li><strong>Rate Limiting:</strong> Brute force protection</li>
                        </ul>
                        
                        <p><strong>Role-based Access Control:</strong></p>
                        <ul>
                            <li><strong>Admin Role:</strong> Full system access</li>
                            <li><strong>Patient Role:</strong> Limited to personal data</li>
                            <li><strong>Middleware Protection:</strong> Route-level authorization</li>
                            <li><strong>Permission Gates:</strong> Fine-grained access control</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-database text-info"></i> Data Protection</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>HIPAA Compliance Measures:</strong></p>
                        <ul>
                            <li><strong>Data Encryption:</strong> Sensitive data encrypted at rest</li>
                            <li><strong>Access Logging:</strong> All data access tracked</li>
                            <li><strong>Audit Trails:</strong> Complete activity logging</li>
                            <li><strong>Data Backup:</strong> Regular encrypted backups</li>
                        </ul>
                        
                        <p><strong>Input Validation & Sanitization:</strong></p>
                        <ul>
                            <li><strong>Form Request Validation:</strong> Server-side validation</li>
                            <li><strong>XSS Protection:</strong> Cross-site scripting prevention</li>
                            <li><strong>SQL Injection Prevention:</strong> Prepared statements</li>
                            <li><strong>File Upload Security:</strong> Type and size validation</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-network-wired text-success"></i> Network Security</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>HTTPS Enforcement:</strong></p>
                        <ul>
                            <li>SSL/TLS encryption for all connections</li>
                            <li>Secure cookie transmission</li>
                            <li>HSTS headers for enhanced security</li>
                        </ul>
                        
                        <p><strong>Security Headers:</strong></p>
                        <ul>
                            <li><strong>Content-Security-Policy:</strong> XSS protection</li>
                            <li><strong>X-Frame-Options:</strong> Clickjacking prevention</li>
                            <li><strong>X-Content-Type-Options:</strong> MIME sniffing protection</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deployment Guide Section -->
        <div class="card" id="deployment-guide">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-rocket"></i> Deployment Guide</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-cloud text-primary"></i> Render.com Deployment</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Prerequisites:</strong></p>
                        <ol>
                            <li>GitHub repository with your code</li>
                            <li>Render.com account</li>
                            <li>Environment variables configured</li>
                        </ol>
                        
                        <p><strong>Deployment Steps:</strong></p>
                        <ol>
                            <li><strong>Create Web Service:</strong> Connect GitHub repository</li>
                            <li><strong>Configure Build:</strong> Set PHP version and build commands</li>
                            <li><strong>Set Environment Variables:</strong> Database, mail, app settings</li>
                            <li><strong>Deploy Database:</strong> PostgreSQL or external MySQL</li>
                            <li><strong>Run Migrations:</strong> Set up database tables</li>
                        </ol>
                        
                        <p><strong>Required Environment Variables:</strong></p>
                        <ul>
                            <li><code>APP_KEY</code> - Laravel application key</li>
                            <li><code>DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD</code> - Database config</li>
                            <li><code>MAIL_*</code> - Email configuration</li>
                            <li><code>APP_URL</code> - Application URL</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-server text-success"></i> VPS/Server Deployment</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Server Requirements:</strong></p>
                        <ul>
                            <li><strong>OS:</strong> Ubuntu 20.04+ / CentOS 8+ / Debian 11+</li>
                            <li><strong>Web Server:</strong> Nginx or Apache</li>
                            <li><strong>PHP:</strong> 8.2+ with required extensions</li>
                            <li><strong>Database:</strong> MySQL 8.0+ or PostgreSQL 13+</li>
                            <li><strong>SSL Certificate:</strong> Let's Encrypt or commercial</li>
                        </ul>
                        
                        <p><strong>PHP Extensions Required:</strong></p>
                        <ul>
                            <li>BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL</li>
                            <li>PDO, Tokenizer, XML, GD, Zip, Curl</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-docker text-info"></i> Docker Deployment</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Docker Configuration:</strong></p>
                        <ul>
                            <li><strong>Dockerfile:</strong> PHP-FPM with Nginx</li>
                            <li><strong>Docker Compose:</strong> Multi-container setup</li>
                            <li><strong>Volume Mapping:</strong> Persistent data storage</li>
                            <li><strong>Environment Config:</strong> .env file management</li>
                        </ul>
                        
                        <p><strong>Container Services:</strong></p>
                        <ul>
                            <li><strong>Web:</strong> PHP-FPM + Nginx</li>
                            <li><strong>Database:</strong> MySQL or PostgreSQL</li>
                            <li><strong>Redis:</strong> Session and cache storage</li>
                            <li><strong>Queue Worker:</strong> Background job processing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Development Setup Section -->
        <div class="card" id="development-setup">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-laptop-code"></i> Development Setup</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-download text-primary"></i> Local Development Environment</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Prerequisites:</strong></p>
                        <ul>
                            <li><strong>PHP 8.2+:</strong> With required extensions</li>
                            <li><strong>Composer:</strong> PHP dependency manager</li>
                            <li><strong>Node.js & NPM:</strong> Frontend asset compilation</li>
                            <li><strong>MySQL:</strong> Local database server</li>
                            <li><strong>Git:</strong> Version control</li>
                        </ul>
                        
                        <p><strong>Setup Steps:</strong></p>
                        <ol>
                            <li><strong>Clone Repository:</strong> <code>git clone &lt;repository-url&gt;</code></li>
                            <li><strong>Install Dependencies:</strong> <code>composer install</code></li>
                            <li><strong>Environment Setup:</strong> Copy <code>.env.example</code> to <code>.env</code></li>
                            <li><strong>Generate App Key:</strong> <code>php artisan key:generate</code></li>
                            <li><strong>Database Setup:</strong> Create database and run migrations</li>
                            <li><strong>Seed Data:</strong> <code>php artisan db:seed</code></li>
                            <li><strong>Frontend Assets:</strong> <code>npm install && npm run dev</code></li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-database text-success"></i> Database Setup</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Database Configuration:</strong></p>
                        <pre class="bg-light p-3"><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinic_cms
DB_USERNAME=root
DB_PASSWORD=your_password</code></pre>
                        
                        <p><strong>Migration Commands:</strong></p>
                        <ul>
                            <li><code>php artisan migrate</code> - Run all migrations</li>
                            <li><code>php artisan migrate:fresh --seed</code> - Fresh install with sample data</li>
                            <li><code>php artisan migrate:status</code> - Check migration status</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-tools text-warning"></i> Development Tools</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Artisan Commands:</strong></p>
                        <ul>
                            <li><code>php artisan serve</code> - Start development server</li>
                            <li><code>php artisan tinker</code> - Interactive PHP shell</li>
                            <li><code>php artisan make:model</code> - Generate model files</li>
                            <li><code>php artisan make:controller</code> - Generate controllers</li>
                            <li><code>php artisan make:migration</code> - Create database migrations</li>
                        </ul>
                        
                        <p><strong>Asset Compilation:</strong></p>
                        <ul>
                            <li><code>npm run dev</code> - Development build</li>
                            <li><code>npm run watch</code> - Watch for changes</li>
                            <li><code>npm run prod</code> - Production build</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Troubleshooting Section -->
        <div class="card" id="troubleshooting">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-wrench"></i> Troubleshooting</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-bug text-danger"></i> Common Issues</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Database Connection Issues:</strong></p>
                        <ul>
                            <li><strong>Check .env file:</strong> Verify database credentials</li>
                            <li><strong>Test connection:</strong> <code>php artisan migrate:status</code></li>
                            <li><strong>Clear config cache:</strong> <code>php artisan config:clear</code></li>
                        </ul>
                        
                        <p><strong>Permission Issues:</strong></p>
                        <ul>
                            <li><strong>Storage permissions:</strong> <code>chmod -R 775 storage bootstrap/cache</code></li>
                            <li><strong>Web server user:</strong> Ensure proper ownership</li>
                        </ul>
                        
                        <p><strong>Composer Issues:</strong></p>
                        <ul>
                            <li><strong>Clear composer cache:</strong> <code>composer clear-cache</code></li>
                            <li><strong>Update dependencies:</strong> <code>composer update</code></li>
                            <li><strong>Autoload dump:</strong> <code>composer dump-autoload</code></li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-first-aid text-success"></i> Performance Optimization</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Laravel Optimization Commands:</strong></p>
                        <ul>
                            <li><code>php artisan config:cache</code> - Cache configuration</li>
                            <li><code>php artisan route:cache</code> - Cache routes</li>
                            <li><code>php artisan view:cache</code> - Cache views</li>
                            <li><code>php artisan optimize</code> - Run all optimizations</li>
                        </ul>
                        
                        <p><strong>Database Optimization:</strong></p>
                        <ul>
                            <li><strong>Query optimization:</strong> Use eager loading</li>
                            <li><strong>Index optimization:</strong> Add database indexes</li>
                            <li><strong>Cache implementation:</strong> Redis or Memcached</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-life-ring text-info"></i> Debug Mode</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Enable Debug Mode:</strong></p>
                        <ul>
                            <li>Set <code>APP_DEBUG=true</code> in .env file</li>
                            <li>Check <code>storage/logs/laravel.log</code> for errors</li>
                            <li>Use <code>dd()</code> and <code>dump()</code> for debugging</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Security Warning:</strong> 
                            Never enable debug mode in production environment!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Sticky Table of Contents */
.sticky-toc {
    position: -webkit-sticky;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    z-index: 10;
}

.sticky-toc .card {
    margin-bottom: 0;
}

.nav-pills .nav-link {
    color: #495057;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
    color: white;
}

.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.info-box {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    border-radius: 0.25rem;
    margin-bottom: 1rem;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-success {
    border-left-color: #28a745;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-danger {
    border-left-color: #dc3545;
}

h5 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.card .card-body ul {
    padding-left: 1.5rem;
}

.card .card-body li {
    margin-bottom: 0.5rem;
}

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }
.text-secondary { color: #6c757d !important; }

pre {
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

code {
    color: #e83e8c;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sticky-toc {
        position: relative;
        top: auto;
        max-height: none;
        overflow-y: visible;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Smooth scroll for table of contents links (only within sticky-toc)
    $('.sticky-toc .nav-link').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target.startsWith('#')) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    // Highlight active section in table of contents (only within sticky-toc)
    $(window).scroll(function() {
        var scrollPos = $(window).scrollTop() + 150;
        $('.sticky-toc .nav-link').removeClass('active');
        
        $('div[id]').each(function() {
            var currLink = $('.sticky-toc a[href="#' + $(this).attr('id') + '"]');
            if ($(this).offset().top <= scrollPos && $(this).offset().top + $(this).height() > scrollPos) {
                currLink.addClass('active');
            }
        });
    });
});
</script>
@stop
