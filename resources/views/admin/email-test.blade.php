<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Testing Dashboard - BOKOD CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h1><i class="fas fa-envelope-open"></i> Email Testing Dashboard</h1>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                
                <!-- Configuration Status -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-cog"></i> Email Configuration Status</h5>
                            </div>
                            <div class="card-body">
                                <div id="config-status">
                                    @if($configStatus['configured'])
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> {{ $configStatus['message'] }}
                                        </div>
                                    @else
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle"></i> {{ $configStatus['message'] }}
                                        </div>
                                    @endif
                                    
                                    <div class="row">
                                        @foreach($configStatus['checks'] as $check => $status)
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $status ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                                <span>{{ ucwords(str_replace('_', ' ', $check)) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Tests -->
                <div class="row">
                    <!-- Patient Welcome Email -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Patient Welcome Email</h5>
                            </div>
                            <div class="card-body">
                                @if($samplePatient)
                                    <p>Test with: {{ $samplePatient->patient_name }} ({{ $samplePatient->email }})</p>
                                    <form class="email-test-form" data-endpoint="{{ route('email-test.patient-welcome') }}">
                                        <input type="hidden" name="patient_id" value="{{ $samplePatient->id }}">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                                <label class="form-check-label">Test Mode (don't send actual email)</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Test Welcome Email
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">No patients with email addresses found.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Notifications -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar"></i> Appointment Notifications</h5>
                            </div>
                            <div class="card-body">
                                @if($sampleAppointment)
                                    <p>Test with: {{ $sampleAppointment->patient->patient_name ?? 'Unknown Patient' }} ({{ $sampleAppointment->patient->email ?? 'No Email' }})</p>
                                    <form class="email-test-form" data-endpoint="{{ route('email-test.appointment-notification') }}">
                                        <input type="hidden" name="appointment_id" value="{{ $sampleAppointment->appointment_id }}">
                                        <div class="mb-3">
                                            <label class="form-label">Notification Type</label>
                                            <select name="notification_type" class="form-select" required>
                                                <option value="approved">Approved</option>
                                                <option value="cancelled">Cancelled</option>
                                                <option value="completed">Completed</option>
                                                <option value="reminder">Reminder</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                                <label class="form-check-label">Test Mode</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-paper-plane"></i> Test Notification
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">No appointments with patient emails found.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Notifications -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-pills"></i> Prescription Notifications</h5>
                            </div>
                            <div class="card-body">
                                @if($samplePrescription)
                                    <p>Test with: {{ $samplePrescription->patient->patient_name ?? 'Unknown Patient' }} ({{ $samplePrescription->patient->email ?? 'No Email' }})</p>
                                    <form class="email-test-form" data-endpoint="{{ route('email-test.prescription-notification') }}">
                                        <input type="hidden" name="prescription_id" value="{{ $samplePrescription->prescription_id }}">
                                        <div class="mb-3">
                                            <label class="form-label">Notification Type</label>
                                            <select name="notification_type" class="form-select" required>
                                                <option value="new">New Prescription</option>
                                                <option value="updated">Updated</option>
                                                <option value="reminder">Medication Reminder</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                                <label class="form-check-label">Test Mode</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-paper-plane"></i> Test Prescription Email
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">No prescriptions with patient emails found.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Health Tips -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-heartbeat"></i> Health Tips</h5>
                            </div>
                            <div class="card-body">
                                <form class="email-test-form" data-endpoint="{{ route('email-test.health-tips') }}">
                                    @if($samplePatient)
                                        <div class="mb-3">
                                            <label class="form-label">Send to specific patient or all?</label>
                                            <select name="patient_id" class="form-select">
                                                <option value="">Send to all patients</option>
                                                <option value="{{ $samplePatient->id }}">{{ $samplePatient->patient_name }} ({{ $samplePatient->email }})</option>
                                            </select>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">Season</label>
                                        <select name="season" class="form-select" required>
                                            <option value="rainy">Rainy Season</option>
                                            <option value="dry">Dry Season</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                            <label class="form-check-label">Test Mode</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-paper-plane"></i> Test Health Tips
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Alerts -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Stock Alerts</h5>
                            </div>
                            <div class="card-body">
                                <form class="email-test-form" data-endpoint="{{ route('email-test.stock-alert') }}">
                                    <div class="mb-3">
                                        <label class="form-label">Alert Type</label>
                                        <select name="alert_type" class="form-select" required>
                                            <option value="low">Low Stock</option>
                                            <option value="critical">Critical Stock</option>
                                            <option value="out_of_stock">Out of Stock</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                            <label class="form-check-label">Test Mode</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-paper-plane"></i> Test Stock Alert
                                    </button>
                                </form>
                                <p class="text-muted mt-2"><small>Will send to all admin users</small></p>
                            </div>
                        </div>
                    </div>

                    <!-- Medication Reminders -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-clock"></i> Medication Reminders Batch</h5>
                            </div>
                            <div class="card-body">
                                <form class="email-test-form" data-endpoint="{{ route('email-test.medication-reminders') }}">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="test_mode" value="1" checked>
                                            <label class="form-check-label">Test Mode</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark">
                                        <i class="fas fa-paper-plane"></i> Test Batch Reminders
                                    </button>
                                </form>
                                <p class="text-muted mt-2"><small>Will process all patients with active prescriptions</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Area -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Test Results</h5>
                            </div>
                            <div class="card-body">
                                <div id="test-results">
                                    <p class="text-muted">Test results will appear here...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Handle all email test forms
            document.querySelectorAll('.email-test-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const endpoint = this.dataset.endpoint;
                    const formData = new FormData(this);
                    const resultsDiv = document.getElementById('test-results');
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
                    
                    // Show loading in results
                    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing test...</div>';
                    
                    fetch(endpoint, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let alertClass = data.success ? 'alert-success' : 'alert-danger';
                        let icon = data.success ? 'fas fa-check-circle' : 'fas fa-times-circle';
                        
                        let html = `<div class="alert ${alertClass}">
                            <h6><i class="${icon}"></i> ${data.success ? 'Success' : 'Error'}</h6>
                            <p>${data.message}</p>`;
                        
                        if (data.recipient) {
                            html += `<p><strong>Recipient:</strong> ${data.recipient}</p>`;
                        }
                        
                        if (data.recipients && Array.isArray(data.recipients)) {
                            html += `<p><strong>Recipients:</strong> ${data.recipients.join(', ')}</p>`;
                        }
                        
                        if (data.subject) {
                            html += `<p><strong>Subject:</strong> ${data.subject}</p>`;
                        }
                        
                        if (data.test_mode) {
                            html += '<p class="mb-0"><small class="text-muted"><i class="fas fa-info-circle"></i> This was a test - no actual email was sent</small></p>';
                        }
                        
                        html += '</div>';
                        
                        resultsDiv.innerHTML = html;
                    })
                    .catch(error => {
                        resultsDiv.innerHTML = `<div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle"></i> Error</h6>
                            <p>An error occurred while testing the email: ${error.message}</p>
                        </div>`;
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.innerHTML.replace('<i class="fas fa-spinner fa-spin"></i> Testing...', '<i class="fas fa-paper-plane"></i> Test');
                    });
                });
            });
        });
    </script>
</body>
</html>