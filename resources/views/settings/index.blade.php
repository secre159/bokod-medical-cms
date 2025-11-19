@extends('adminlte::page')

@section('title', 'System Settings | ' . config('adminlte.title', 'Bokod CMS'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">System Settings</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- System Information Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>System Information
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>PHP Version:</strong></td>
                            <td>{{ $systemInfo['php_version'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Laravel Version:</strong></td>
                            <td>{{ $systemInfo['laravel_version'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Environment:</strong></td>
                            <td>
                                <span class="badge badge-{{ $systemInfo['app_environment'] === 'production' ? 'success' : 'warning' }}">
                                    {{ ucfirst($systemInfo['app_environment']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Debug Mode:</strong></td>
                            <td>
                                <span class="badge badge-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }}">
                                    {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Server:</strong></td>
                            <td>{{ $systemInfo['server_software'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td>{{ $systemInfo['database_version'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Storage Used:</strong></td>
                            <td>
                                {{ $systemInfo['storage_info']['used'] }} / {{ $systemInfo['storage_info']['total'] }}
                                ({{ $systemInfo['storage_info']['usage_percentage'] }}%)
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Last Backup:</strong></td>
                            <td>{{ $systemInfo['last_backup'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Guide -->
    <div class="card collapsed-card" id="settingsGuide">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-question-circle mr-2"></i>Settings Guide & Documentation
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="guide-general-tab" data-toggle="pill" href="#guide-general" role="tab">
                                    General Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="guide-system-tab" data-toggle="pill" href="#guide-system" role="tab">
                                    System Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="guide-email-tab" data-toggle="pill" href="#guide-email" role="tab">
                                    Email Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="guide-maintenance-tab" data-toggle="pill" href="#guide-maintenance" role="tab">
                                    Maintenance Tools
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="padding: 15px;">
                            
                            <!-- General Settings Guide -->
                            <div class="tab-pane fade show active" id="guide-general" role="tabpanel">
                                <h5><i class="fas fa-cogs mr-2"></i>General Settings</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th width="20%">Setting</th>
                                                <th width="50%">Description</th>
                                                <th width="30%">Tips & Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Application Name</strong></td>
                                                <td>The name of your clinic/hospital that appears throughout the system</td>
                                                <td><span class="badge badge-info">Required</span> Shows in browser title, emails, reports</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Application Description</strong></td>
                                                <td>Brief description of your healthcare facility or services</td>
                                                <td><span class="badge badge-secondary">Optional</span> Used in meta tags and reports</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contact Email</strong></td>
                                                <td>Main email address for your facility</td>
                                                <td><span class="badge badge-info">Required</span> Used for system notifications and patient contact</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contact Phone</strong></td>
                                                <td>Main phone number for your facility</td>
                                                <td><span class="badge badge-secondary">Optional</span> Displayed on patient portal and reports</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Address</strong></td>
                                                <td>Full address of your healthcare facility</td>
                                                <td><span class="badge badge-secondary">Optional</span> Used in prescription headers and patient documents</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Timezone</strong></td>
                                                <td>Your local timezone for appointments and scheduling</td>
                                                <td><span class="badge badge-warning">Important</span> Affects all timestamps and appointment scheduling</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Logo</strong></td>
                                                <td>Your clinic logo (200x200px recommended)</td>
                                                <td><span class="badge badge-secondary">Optional</span> Formats: JPEG, PNG, JPG, SVG. Max: 2MB</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Favicon</strong></td>
                                                <td>Small icon for browser tabs (32x32px)</td>
                                                <td><span class="badge badge-secondary">Optional</span> ICO or PNG format recommended</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- System Settings Guide -->
                            <div class="tab-pane fade" id="guide-system" role="tabpanel">
                                <h5><i class="fas fa-server mr-2"></i>System Settings</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th width="20%">Setting</th>
                                                <th width="50%">Description</th>
                                                <th width="30%">Recommendations</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Maintenance Mode</strong></td>
                                                <td>Temporarily disable access to the system for maintenance</td>
                                                <td><span class="badge badge-warning">Caution</span> Only admins can access when enabled</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Allow User Registration</strong></td>
                                                <td>Allow new users to register accounts themselves</td>
                                                <td><span class="badge badge-danger">Healthcare</span> Usually disabled - admins create accounts</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Require Email Verification</strong></td>
                                                <td>Force users to verify email before accessing system</td>
                                                <td><span class="badge badge-success">Recommended</span> Improves security</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Session Lifetime</strong></td>
                                                <td>How long users stay logged in (minutes)</td>
                                                <td><span class="badge badge-info">Default: 120</span> Healthcare: 60-240 minutes recommended</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Backup Frequency</strong></td>
                                                <td>How often automatic backups are created</td>
                                                <td><span class="badge badge-success">Healthcare</span> Daily recommended for patient data</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Log Level</strong></td>
                                                <td>Amount of system activity to log</td>
                                                <td><span class="badge badge-info">Production: Error/Warning</span> <span class="badge badge-secondary">Development: Info/Debug</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cache Driver</strong></td>
                                                <td>Method for storing temporary data</td>
                                                <td><span class="badge badge-info">File</span> works for most setups. Redis for high traffic</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Max File Upload Size</strong></td>
                                                <td>Maximum size for file uploads (MB)</td>
                                                <td><span class="badge badge-info">Default: 10MB</span> Consider patient documents and images</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Email Settings Guide -->
                            <div class="tab-pane fade" id="guide-email" role="tabpanel">
                                <h5><i class="fas fa-envelope mr-2"></i>Email Settings</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Setting</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Mail Driver</strong></td>
                                                        <td>Method for sending emails</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Mail Host</strong></td>
                                                        <td>SMTP server address (e.g., smtp.gmail.com)</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Mail Port</strong></td>
                                                        <td>SMTP server port (587 for TLS, 465 for SSL)</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Encryption</strong></td>
                                                        <td>Security method (TLS recommended)</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Username/Password</strong></td>
                                                        <td>SMTP authentication credentials</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>From Address</strong></td>
                                                        <td>Email address shown as sender</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>From Name</strong></td>
                                                        <td>Name shown as sender (your clinic name)</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-lightbulb mr-2"></i>Common Email Providers</h6>
                                        <div class="alert alert-info">
                                            <h6>Gmail/G Suite:</h6>
                                            <ul class="mb-2">
                                                <li>Host: smtp.gmail.com</li>
                                                <li>Port: 587</li>
                                                <li>Encryption: TLS</li>
                                                <li>Note: Enable "App Passwords" for Gmail</li>
                                            </ul>
                                        </div>
                                        <div class="alert alert-success">
                                            <h6>Office 365/Outlook:</h6>
                                            <ul class="mb-2">
                                                <li>Host: smtp.office365.com</li>
                                                <li>Port: 587</li>
                                                <li>Encryption: TLS</li>
                                                <li>Note: Use full email as username</li>
                                            </ul>
                                        </div>
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle"></i> Important for Healthcare:</h6>
                                            <p class="mb-0">Ensure your email provider is HIPAA compliant if sending patient information. Consider encrypted email services for sensitive communications.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Maintenance Guide -->
                            <div class="tab-pane fade" id="guide-maintenance" role="tabpanel">
                                <h5><i class="fas fa-tools mr-2"></i>Maintenance Tools</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Cache Management</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Clear All Cache</strong></td>
                                                        <td>Clears application, config, route, and view cache</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Clear Config Cache</strong></td>
                                                        <td>Only clears configuration cache</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Clear View Cache</strong></td>
                                                        <td>Only clears compiled view templates</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <h6>Database Operations</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Create Backup</strong></td>
                                                        <td>Creates a database backup file</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Optimize Database</strong></td>
                                                        <td>Optimizes tables for better performance</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>System Diagnostics</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>System Health Check</strong></td>
                                                        <td>Checks database, storage, cache, and PHP extensions</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Clean Temp Files</strong></td>
                                                        <td>Removes old temporary files and cached data</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <h6><i class="fas fa-clock mr-2"></i>Maintenance Schedule</h6>
                                            <ul class="mb-0">
                                                <li><strong>Daily:</strong> System Health Check</li>
                                                <li><strong>Weekly:</strong> Clear Cache, Clean Temp Files</li>
                                                <li><strong>Monthly:</strong> Database Backup, Optimize Database</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle mr-2"></i>Healthcare Considerations</h6>
                                            <ul class="mb-0">
                                                <li>Schedule maintenance during off-hours</li>
                                                <li>Always backup before major changes</li>
                                                <li>Test in development environment first</li>
                                                <li>Keep audit logs of maintenance activities</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab">
                        <i class="fas fa-cogs mr-1"></i>General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="system-tab" data-toggle="pill" href="#system" role="tab">
                        <i class="fas fa-server mr-1"></i>System
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="email-tab" data-toggle="pill" href="#email" role="tab">
                        <i class="fas fa-envelope mr-1"></i>Email
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="maintenance-tab" data-toggle="pill" href="#maintenance" role="tab">
                        <i class="fas fa-tools mr-1"></i>Maintenance
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="settingsTabsContent">
                
                <!-- General Settings Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <form action="{{ route('settings.updateGeneral') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="app_name">Application Name</label>
                                            <input type="text" class="form-control" id="app_name" name="app_name" 
                                                   value="{{ $settings['general']['app_name'] }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_email">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                   value="{{ $settings['general']['contact_email'] }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="app_description">Application Description</label>
                                    <textarea class="form-control" id="app_description" name="app_description" rows="2">{{ $settings['general']['app_description'] }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_phone">Contact Phone</label>
                                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                                   value="{{ $settings['general']['contact_phone'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select class="form-control" id="timezone" name="timezone">
                                                <option value="UTC" {{ $settings['general']['timezone'] === 'UTC' ? 'selected' : '' }}>UTC - Coordinated Universal Time</option>
                                                <option value="Asia/Manila" {{ $settings['general']['timezone'] === 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila - Philippine Time</option>
                                                <option value="Asia/Hong_Kong" {{ $settings['general']['timezone'] === 'Asia/Hong_Kong' ? 'selected' : '' }}>Asia/Hong_Kong - Hong Kong Time</option>
                                                <option value="Asia/Singapore" {{ $settings['general']['timezone'] === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore - Singapore Time</option>
                                                <option value="Asia/Tokyo" {{ $settings['general']['timezone'] === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo - Japan Standard Time</option>
                                                <option value="America/New_York" {{ $settings['general']['timezone'] === 'America/New_York' ? 'selected' : '' }}>America/New_York - Eastern Time</option>
                                                <option value="America/Los_Angeles" {{ $settings['general']['timezone'] === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles - Pacific Time</option>
                                                <option value="Europe/London" {{ $settings['general']['timezone'] === 'Europe/London' ? 'selected' : '' }}>Europe/London - Greenwich Mean Time</option>
                                                <option value="Europe/Paris" {{ $settings['general']['timezone'] === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris - Central European Time</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2">{{ $settings['general']['address'] }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="logo">Application Logo</label>
                                    <div class="text-center mb-3">
                                        @if($settings['general']['app_logo'])
                                            <img src="{{ asset('storage/' . $settings['general']['app_logo']) }}" 
                                                 alt="Current Logo" class="img-thumbnail" style="max-width: 200px;">
                                        @else
                                            <div class="bg-light p-4 rounded">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                                <p class="text-muted mt-2">No logo uploaded</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 200x200px</small>
                                </div>

                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <div class="text-center mb-3">
                                        @if($settings['general']['app_favicon'])
                                            <img src="{{ asset('storage/' . $settings['general']['app_favicon']) }}" 
                                                 alt="Current Favicon" class="img-thumbnail" style="max-width: 64px;">
                                        @else
                                            <div class="bg-light p-3 rounded d-inline-block">
                                                <i class="fas fa-star fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-file" id="favicon" name="favicon" accept="image/x-icon,image/png">
                                    <small class="form-text text-muted">Recommended: 32x32px ICO or PNG</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Save General Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- System Settings Tab -->
                <div class="tab-pane fade" id="system" role="tabpanel">
                    <form action="{{ route('settings.updateSystem') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">System Status</h6>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" 
                                               id="maintenance_mode" {{ $settings['system']['maintenance_mode'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maintenance_mode">
                                            <strong>Maintenance Mode</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable to take the site offline for maintenance</small>
                                </div>

                                <h6 class="font-weight-bold mb-3 mt-4">User Management</h6>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="user_registration" value="1" 
                                               id="user_registration" {{ $settings['system']['user_registration'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="user_registration">
                                            <strong>Allow User Registration</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Allow new users to register accounts</small>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="email_verification" value="1" 
                                               id="email_verification" {{ $settings['system']['email_verification'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_verification">
                                            <strong>Require Email Verification</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Require email verification for new accounts</small>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Session & Security</h6>
                                <div class="form-group">
                                    <label for="session_lifetime">Session Lifetime (minutes)</label>
                                    <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                                           value="{{ $settings['system']['session_lifetime'] }}" min="30" max="43200" required>
                                    <small class="form-text text-muted">How long users stay logged in (30-43200 minutes)</small>
                                </div>


                                <h6 class="font-weight-bold mb-3 mt-4">File Management</h6>
                                <div class="form-group">
                                    <label for="max_file_size">Max File Upload Size (MB)</label>
                                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" 
                                           value="{{ $settings['system']['max_file_size'] }}" min="1" max="100" required>
                                    <small class="form-text text-muted">Maximum file size for uploads (1-100 MB)</small>
                                </div>

                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Performance & Logging</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="log_level">Log Level</label>
                                    <select class="form-control" id="log_level" name="log_level">
                                        <option value="emergency" {{ ($settings['system']['log_level'] ?? 'info') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                                        <option value="alert" {{ ($settings['system']['log_level'] ?? 'info') === 'alert' ? 'selected' : '' }}>Alert</option>
                                        <option value="critical" {{ ($settings['system']['log_level'] ?? 'info') === 'critical' ? 'selected' : '' }}>Critical</option>
                                        <option value="error" {{ ($settings['system']['log_level'] ?? 'info') === 'error' ? 'selected' : '' }}>Error</option>
                                        <option value="warning" {{ ($settings['system']['log_level'] ?? 'info') === 'warning' ? 'selected' : '' }}>Warning</option>
                                        <option value="notice" {{ ($settings['system']['log_level'] ?? 'info') === 'notice' ? 'selected' : '' }}>Notice</option>
                                        <option value="info" {{ ($settings['system']['log_level'] ?? 'info') === 'info' ? 'selected' : '' }}>Info</option>
                                        <option value="debug" {{ ($settings['system']['log_level'] ?? 'info') === 'debug' ? 'selected' : '' }}>Debug</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cache_driver">Cache Driver</label>
                                    <select class="form-control" id="cache_driver" name="cache_driver">
                                        <option value="file" {{ ($settings['system']['cache_driver'] ?? 'file') === 'file' ? 'selected' : '' }}>File</option>
                                        <option value="redis" {{ ($settings['system']['cache_driver'] ?? 'file') === 'redis' ? 'selected' : '' }}>Redis</option>
                                        <option value="memcached" {{ ($settings['system']['cache_driver'] ?? 'file') === 'memcached' ? 'selected' : '' }}>Memcached</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="backup_frequency">Backup Frequency</label>
                                    <select class="form-control" id="backup_frequency" name="backup_frequency">
                                        <option value="daily" {{ ($settings['system']['backup_frequency'] ?? 'weekly') === 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ ($settings['system']['backup_frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ ($settings['system']['backup_frequency'] ?? 'weekly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Save System Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Email Settings Tab -->
                <div class="tab-pane fade" id="email" role="tabpanel">
                    <form action="{{ route('settings.updateEmail') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_driver">Mail Driver</label>
                                    <select class="form-control" id="mail_driver" name="mail_driver" required>
                                        <option value="smtp" {{ $settings['email']['mail_driver'] === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ $settings['email']['mail_driver'] === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="mailgun" {{ $settings['email']['mail_driver'] === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ $settings['email']['mail_driver'] === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                        <option value="postmark" {{ $settings['email']['mail_driver'] === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_host">Mail Host</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                           value="{{ $settings['email']['mail_host'] }}" placeholder="smtp.gmail.com">
                                    <small class="form-text text-muted">SMTP server hostname</small>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="smtp-settings">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_port">Mail Port</label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                           value="{{ $settings['email']['mail_port'] }}" min="1" max="65535" placeholder="587">
                                    <small class="form-text text-muted">SMTP port (587 for TLS, 465 for SSL)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_encryption">Encryption</label>
                                    <select class="form-control" id="mail_encryption" name="mail_encryption">
                                        <option value="" {{ empty($settings['email']['mail_encryption']) ? 'selected' : '' }}>None</option>
                                        <option value="tls" {{ $settings['email']['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $settings['email']['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="smtp-auth">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_username">Username</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                           value="{{ $settings['email']['mail_username'] }}" autocomplete="username">
                                    <small class="form-text text-muted">SMTP authentication username</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_password">Password</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                           placeholder="{{ !empty($settings['email']['mail_password']) ? '••••••••' : 'Enter password' }}" 
                                           autocomplete="current-password">
                                    <small class="form-text text-muted">Leave empty to keep current password</small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Email Identity</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_address">From Address</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                           value="{{ $settings['email']['mail_from_address'] }}" required>
                                    <small class="form-text text-muted">Email address for outgoing messages</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mail_from_name">From Name</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                           value="{{ $settings['email']['mail_from_name'] }}" required>
                                    <small class="form-text text-muted">Display name for outgoing messages</small>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Save Email Settings
                            </button>
                            <button type="button" class="btn btn-info" id="testEmail">
                                <i class="fas fa-envelope mr-1"></i>Test Email
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Maintenance Tab -->
                <div class="tab-pane fade" id="maintenance" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Cache Management</h3>
                                </div>
                                <div class="card-body">
                                    <p>Clear application cache to improve performance and apply configuration changes.</p>
                                    <div class="btn-group-vertical btn-block">
                                        <button type="button" class="btn btn-warning" id="clearCache">
                                            <i class="fas fa-broom mr-1"></i>Clear All Cache
                                        </button>
                                        <button type="button" class="btn btn-info" id="clearConfigCache">
                                            <i class="fas fa-cog mr-1"></i>Clear Config Cache
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="clearViewCache">
                                            <i class="fas fa-eye mr-1"></i>Clear View Cache
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Database Operations</h3>
                                </div>
                                <div class="card-body">
                                    <p>Database backup and optimization tools.</p>
                                    <div class="btn-group-vertical btn-block">
                                        <button type="button" class="btn btn-success" id="createBackup">
                                            <i class="fas fa-database mr-1"></i>Create Backup
                                        </button>
                                        <button type="button" class="btn btn-info" id="optimizeDatabase">
                                            <i class="fas fa-tools mr-1"></i>Optimize Database
                                        </button>
                                        <button type="button" class="btn btn-success" id="viewBackups">
                                            <i class="fas fa-list mr-1"></i>Manage Backups
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">System Diagnostics</h3>
                                </div>
                                <div class="card-body">
                                    <p>Monitor system health and performance.</p>
                                    <div class="btn-group-vertical btn-block">
                                        <button type="button" class="btn btn-info" id="checkSystemHealth">
                                            <i class="fas fa-heartbeat mr-1"></i>System Health Check
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Storage Management</h3>
                                </div>
                                <div class="card-body">
                                    <p>Monitor and manage file storage usage.</p>
                                    <div class="mb-3">
                                        <strong>Storage Usage:</strong> {{ $systemInfo['storage_info']['used'] }} / {{ $systemInfo['storage_info']['total'] }} 
                                        ({{ $systemInfo['storage_info']['usage_percentage'] }}%)
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar {{ $systemInfo['storage_info']['usage_percentage'] > 80 ? 'bg-danger' : ($systemInfo['storage_info']['usage_percentage'] > 60 ? 'bg-warning' : 'bg-success') }}" 
                                             style="width: {{ $systemInfo['storage_info']['usage_percentage'] }}%"></div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-warning" id="cleanTempFiles">
                                            <i class="fas fa-trash-alt mr-1"></i>Clean Temp Files
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Backup Management Modal -->
    <div class="modal fade" id="backupModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-database mr-2"></i>Database Backup Management</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" id="refreshBackups">
                                <i class="fas fa-sync mr-1"></i>Refresh List
                            </button>
                            <button type="button" class="btn btn-success" id="createBackupFromModal">
                                <i class="fas fa-plus mr-1"></i>Create New Backup
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="backupsList">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                                    <p class="mt-2">Loading backups...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Restore Instructions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle mr-2"></i>How to Restore a Backup</h5>
                                </div>
                                <div class="card-body">
                                    <h6><strong>Method 1: Using phpMyAdmin (Recommended)</strong></h6>
                                    <ol>
                                        <li>Download the backup file using the "Download" button above</li>
                                        <li>Open phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
                                        <li>Select your database (usually same name as your CMS database)</li>
                                        <li>Click the "Import" tab</li>
                                        <li>Click "Choose File" and select your downloaded .sql backup</li>
                                        <li>Click "Go" to restore</li>
                                    </ol>
                                    
                                    <h6 class="mt-3"><strong>Method 2: Using MySQL Command Line (Localhost/VPS Only)</strong></h6>
                                    <ol>
                                        <li>Download the backup file</li>
                                        <li>Open Command Prompt as Administrator</li>
                                        <li>Navigate to XAMPP MySQL bin: <code>cd "C:\Users\Axl Chan\Desktop\XAMPP\mysql\bin"</code></li>
                                        <li>Run restore command: <code>mysql -u root -p database_name < "path\to\backup.sql"</code></li>
                                    </ol>
                                    
                                    <h6 class="mt-3"><strong>Method 3: Automated System Restore (Recommended)</strong></h6>
                                    <ol>
                                        <li>Click the <strong>"Restore"</strong> button next to any backup above</li>
                                        <li>Confirm the critical warning dialog</li>
                                        <li>System automatically creates safety backup and restores data</li>
                                        <li>Works on both localhost and hosted environments</li>
                                    </ol>
                                    
                                    <div class="alert alert-warning mt-3">
                                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Important Backup Notes:</h6>
                                        <ul class="mb-0">
                                            <li><strong>Always test restores</strong> in a development environment first</li>
                                            <li><strong>Stop the web server</strong> during restoration to prevent conflicts</li>
                                            <li><strong>Backup current data</strong> before restoring (create a backup before restore)</li>
                                            <li><strong>Verify data integrity</strong> after restoration is complete</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
<script>
$(document).ready(function() {
    // Test Email
    $('#testEmail').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Sending...');
        
        $.post('{{ route("settings.testEmail") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
            } else {
                showAlert('error', response.message);
            }
        }).fail(function(xhr) {
            showAlert('error', 'Error sending test email');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-envelope mr-1"></i>Test Email');
        });
    });

    // Clear Cache
    $(document).off('click', '#clearCache').on('click', '#clearCache', function(e) {
        e.preventDefault();
        console.log('Clear Cache button clicked');
        showConfirmModal(
            'Clear Cache Confirmation',
            'Are you sure you want to clear all cache? This will clear application, config, route, and view cache.',
            'warning',
            function() {
                console.log('User confirmed clear cache');
                const btn = $('#clearCache');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Clearing...');
                
                $.post('{{ route("settings.clearCache") }}', {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error clearing cache');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-broom mr-1"></i>Clear All Cache');
                });
            },
            function() {
                console.log('User cancelled clear cache');
            }
        );
    });

    // Create Backup
    $(document).off('click', '#createBackup').on('click', '#createBackup', function(e) {
        e.preventDefault();
        console.log('Create Backup button clicked');
        showConfirmModal(
            'Create Database Backup',
            'Are you sure you want to create a database backup? This may take a few moments.',
            'info',
            function() {
                console.log('User confirmed backup creation');
                const btn = $('#createBackup');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
                
                $.post('{{ route("settings.createBackup") }}', {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error creating backup');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-database mr-1"></i>Create Backup');
                });
            },
            function() {
                console.log('User cancelled backup creation');
            }
        );
    });

    // Clear Config Cache
    $('#clearConfigCache').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Clearing...');
        
        $.post('{{ route("settings.clearConfigCache") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
            } else {
                showAlert('error', response.message);
            }
        }).fail(function() {
            showAlert('error', 'Error clearing config cache');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-cog mr-1"></i>Clear Config Cache');
        });
    });

    // Clear View Cache
    $('#clearViewCache').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Clearing...');
        
        $.post('{{ route("settings.clearViewCache") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
            } else {
                showAlert('error', response.message);
            }
        }).fail(function() {
            showAlert('error', 'Error clearing view cache');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-eye mr-1"></i>Clear View Cache');
        });
    });

    // Optimize Database
    $(document).off('click', '#optimizeDatabase').on('click', '#optimizeDatabase', function(e) {
        e.preventDefault();
        console.log('Optimize Database button clicked');
        showConfirmModal(
            'Optimize Database',
            'This will optimize all database tables. This operation may take some time. Continue?',
            'warning',
            function() {
                console.log('User confirmed database optimization');
                const btn = $('#optimizeDatabase');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Optimizing...');
                
                $.post('{{ route("settings.optimizeDatabase") }}', {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error optimizing database');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-tools mr-1"></i>Optimize Database');
                });
            },
            function() {
                console.log('User cancelled database optimization');
            }
        );
    });

    // System Health Check
    $('#checkSystemHealth').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Checking...');
        
        $.post('{{ route("settings.systemHealthCheck") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                showHealthResults(response.results);
            } else {
                showAlert('error', response.message);
            }
        }).fail(function() {
            showAlert('error', 'Error running health check');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-heartbeat mr-1"></i>System Health Check');
        });
    });

    // Clean Temp Files
    $(document).off('click', '#cleanTempFiles').on('click', '#cleanTempFiles', function(e) {
        e.preventDefault();
        console.log('Clean Temp Files button clicked');
        showConfirmModal(
            'Clean Temporary Files',
            'This will delete temporary files and cached data. Files older than 1 hour will be removed. Continue?',
            'warning',
            function() {
                console.log('User confirmed temp files cleaning');
                const btn = $('#cleanTempFiles');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Cleaning...');
                
                $.post('{{ route("settings.cleanTempFiles") }}', {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error cleaning temp files');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i>Clean Temp Files');
                });
            },
            function() {
                console.log('User cancelled temp files cleaning');
            }
        );
    });

    // Email driver change handler
    $('#mail_driver').change(function() {
        const driver = $(this).val();
        if (driver === 'smtp') {
            $('#smtp-settings, #smtp-auth').show();
        } else {
            $('#smtp-settings, #smtp-auth').hide();
        }
    });

    // Initialize email settings visibility
    $('#mail_driver').trigger('change');
    
    // View Backups
    $('#viewBackups').click(function() {
        $('#backupModal').modal('show');
        loadBackups();
    });
    
    // Refresh Backups
    $('#refreshBackups').click(function() {
        loadBackups();
    });
    
    // Create Backup from Modal
    $('#createBackupFromModal').click(function() {
        showConfirmModal(
            'Create New Backup',
            'Are you sure you want to create a new database backup? This may take a few moments.',
            'info',
            function() {
                const btn = $('#createBackupFromModal');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
                
                $.post('{{ route("settings.createBackup") }}', {
                    _token: '{{ csrf_token() }}'
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        loadBackups(); // Refresh the list
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error creating backup');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plus mr-1"></i>Create New Backup');
                });
            }
        );
    });
    
    // Load backups list
    function loadBackups() {
        $('#backupsList').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading backups...</p></div>');
        
        $.get('{{ route("settings.listBackups") }}').done(function(response) {
            if (response.success) {
                displayBackups(response.backups);
            } else {
                $('#backupsList').html('<div class="alert alert-danger">Error loading backups: ' + response.message + '</div>');
            }
        }).fail(function() {
            $('#backupsList').html('<div class="alert alert-danger">Error loading backups</div>');
        });
    }
    
    // Display backups list
    function displayBackups(backups) {
        if (backups.length === 0) {
            $('#backupsList').html('<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>No backups found. Create your first backup!</div>');
            return;
        }
        
        let html = '<div class="table-responsive"><table class="table table-striped table-hover">';
        html += '<thead><tr><th width=\"35%\">Backup File</th><th width=\"15%\">Size</th><th width=\"20%\">Created</th><th width=\"30%\">Actions</th></tr></thead><tbody>';
        
        backups.forEach(function(backup) {
            html += '<tr>';
            const name = backup.display_name ? backup.display_name : backup.filename;
            html += '<td><strong>' + name + '</strong></td>';
            html += '<td>' + backup.size + '</td>';
            html += '<td>' + backup.created_at + '<br><small class="text-muted">' + backup.created_human + '</small></td>';
            html += '<td>';
            html += '<button class="btn btn-sm btn-primary mr-1" onclick="window.downloadBackup(\'' + backup.filename + '\')">';
            html += '<i class="fas fa-download mr-1"></i>Download</button>';
            html += '<button class="btn btn-sm btn-warning mr-1" onclick="window.restoreBackup(\'' + backup.filename + '\')">';
            html += '<i class="fas fa-undo mr-1"></i>Restore</button>';
            html += '<button class="btn btn-sm btn-danger" onclick="window.deleteBackup(\'' + backup.filename + '\')">';
            html += '<i class="fas fa-trash mr-1"></i>Delete</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        $('#backupsList').html(html);
    }
    
    // Expose handlers globally for inline onclick
    window.downloadBackup = function(filename) {
        const url = '{{ route("settings.downloadBackup", "FILENAME") }}'.replace('FILENAME', filename);
        window.location.href = url;
    }
    
    window.restoreBackup = function(filename) {
        showConfirmModal(
            'Restore Database',
            '<div class="alert alert-warning mb-3"><i class="fas fa-exclamation-triangle mr-2"></i><strong>CRITICAL WARNING:</strong> This will replace ALL current data with the backup data!</div>' +
            '<p><strong>What will happen:</strong></p>' +
            '<ul>' +
            '<li>Current database will be backed up as safety backup</li>' +
            '<li>All current data will be replaced with backup</li>' +
            '<li>This action affects patients, appointments, prescriptions, etc.</li>' +
            '</ul>' +
            '<p><strong>Are you absolutely sure you want to restore?</strong></p>',
            'danger',
            function() {
                $('#backupModal').find('.modal-body').prepend('<div id="restoreProgress" class="alert alert-info"><i class="fas fa-spinner fa-spin mr-2"></i>Restoring database... This may take several minutes. Please do not close this window.</div>');
                
                $.ajax({
                    url: '{{ route("settings.restoreBackup", "FILENAME") }}'.replace('FILENAME', filename),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    timeout: 300000
                }).done(function(response) {
                    $('#restoreProgress').remove();
                    if (response.success) {
                        showAlert('success', response.message);
                        loadBackups();
                        showRestoreSuccessModal(response);
                    } else {
                        showAlert('error', response.message || 'Restore failed');
                    }
                }).fail(function(xhr) {
                    $('#restoreProgress').remove();
                    let errorMsg = 'Error restoring backup';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMsg);
                });
            }
        );
    }
    
    window.deleteBackup = function(filename) {
        showConfirmModal(
            'Delete Backup',
            'Are you sure you want to delete this backup? This action cannot be undone.',
            'danger',
            function() {
                $.ajax({
                    url: '{{ route("settings.deleteBackup", "FILENAME") }}'.replace('FILENAME', filename),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        loadBackups();
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error deleting backup');
                });
            }
        );
    }
                                    <li>Check that all data appears correctly</li>
                                    <li>The safety backup is available if you need to revert</li>
                                    <li>Consider testing all system functions</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="window.location.reload()">Refresh Page</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $('#restoreSuccessModal').modal('show');
        $('#restoreSuccessModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
    
    // Delete backup
    function deleteBackup(filename) {
        showConfirmModal(
            'Delete Backup',
            'Are you sure you want to delete the backup "' + filename + '"? This action cannot be undone.',
            'danger',
            function() {
                $.ajax({
                    url: '{{ route("settings.deleteBackup", "FILENAME") }}'.replace('FILENAME', filename),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        loadBackups(); // Refresh the list
                    } else {
                        showAlert('error', response.message);
                    }
                }).fail(function() {
                    showAlert('error', 'Error deleting backup');
                });
            }
        );
    }
    
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas ${icon}"></i> ${message}
            </div>
        `;
        
        $('.content-header').after(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').first().fadeOut();
        }, 5000);
    }
    
    function showConfirmModal(title, message, type, onConfirm, onCancel) {
        const modalId = 'confirmModal_' + Date.now();
        const iconClass = type === 'warning' ? 'fa-exclamation-triangle text-warning' : 
                         type === 'danger' ? 'fa-exclamation-circle text-danger' : 
                         'fa-question-circle text-info';
        
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fas ${iconClass} mr-2"></i>${title}</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="${modalId}_confirm">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $('#' + modalId).modal('show');
        
        // Handle confirm button
        $('#' + modalId + '_confirm').click(function() {
            $('#' + modalId).modal('hide');
            if (onConfirm) onConfirm();
        });
        
        // Handle cancel/close
        $('#' + modalId).on('hidden.bs.modal', function() {
            const wasConfirmed = $(this).data('confirmed');
            $(this).remove();
            if (!wasConfirmed && onCancel) onCancel();
        });
        
        // Mark as confirmed when confirm button is clicked
        $('#' + modalId + '_confirm').click(function() {
            $('#' + modalId).data('confirmed', true);
        });
    }
    
    function showHealthResults(results) {
        let content = '<div class="modal fade" id="healthResultsModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">';
        content += '<div class="modal-header"><h4 class="modal-title">System Health Check Results</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>';
        content += '<div class="modal-body">';
        
        results.forEach(function(check) {
            const statusClass = check.status === 'OK' ? 'text-success' : 'text-danger';
            const icon = check.status === 'OK' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            content += `<div class="row mb-2">`;
            content += `<div class="col-md-6"><strong>${check.name}</strong></div>`;
            content += `<div class="col-md-6 ${statusClass}"><i class="fas ${icon}"></i> ${check.status}</div>`;
            if (check.message) {
                content += `<div class="col-12"><small class="text-muted">${check.message}</small></div>`;
            }
            content += '</div>';
        });
        
        content += '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>';
        content += '</div></div></div>';
        
        $('body').append(content);
        $('#healthResultsModal').modal('show');
        $('#healthResultsModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
});
</script>
@endsection