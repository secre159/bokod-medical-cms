@extends('adminlte::page')

@section('title', 'Database Fixes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Database Fixes</h1>
        <small class="text-muted">Fix database schema issues</small>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Database Fix Utility</h5>
            This tool helps fix database schema issues that may occur during deployment. 
            <strong>Only use this if you're experiencing database-related errors.</strong>
        </div>
    </div>
</div>

<div class="row">
    <!-- Messaging System Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments mr-2"></i>
                    Messaging System
                </h3>
                <div class="card-tools">
                    @if($checks['messaging']['status'] === 'ok')
                        <span class="badge badge-success">OK</span>
                    @else
                        <span class="badge badge-warning">Needs Fix</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h5>Status Check:</h5>
                @if($checks['messaging']['status'] === 'ok')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        Messaging system is working correctly!
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h6>Issues Found:</h6>
                        <ul class="mb-0">
                            @foreach($checks['messaging']['issues'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                    
                    @if(!empty($checks['messaging']['fixes']))
                        <div class="mt-3">
                            <h6>Proposed Fixes:</h6>
                            <ul class="list-unstyled">
                                @foreach($checks['messaging']['fixes'] as $fix)
                                    <li class="mb-1">
                                        <small class="text-muted">
                                            Conversation {{ $fix['conversation_id'] }}: 
                                            Fix relationship to {{ $fix['patient_name'] }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <button type="button" class="btn btn-warning" onclick="fixMessaging()">
                        <i class="fas fa-tools mr-1"></i> Fix Messaging Issues
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Prescriptions System Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-pills mr-2"></i>
                    Prescriptions System
                </h3>
                <div class="card-tools">
                    @if($checks['prescriptions']['status'] === 'ok')
                        <span class="badge badge-success">OK</span>
                    @else
                        <span class="badge badge-warning">Needs Fix</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h5>Status Check:</h5>
                @if($checks['prescriptions']['status'] === 'ok')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        Prescriptions system is working correctly!
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h6>Issues Found:</h6>
                        <ul class="mb-0">
                            @foreach($checks['prescriptions']['issues'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                    
                    @if(!empty($checks['prescriptions']['fixes']))
                        <div class="mt-3">
                            <h6>Missing Columns:</h6>
                            <ul class="list-unstyled">
                                @foreach($checks['prescriptions']['fixes'] as $column)
                                    <li class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-minus-circle text-danger mr-1"></i>
                                            {{ $column }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <button type="button" class="btn btn-warning" onclick="fixPrescriptions()">
                        <i class="fas fa-tools mr-1"></i> Fix Prescriptions Issues
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Fix Results
                </h3>
            </div>
            <div class="card-body">
                <div id="fix-results">
                    <p class="text-muted">Click a fix button above to see results here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('adminlte_js')
<script>
function fixMessaging() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Fixing...';
    
    fetch('{{ route("database-fixes.messaging") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('fix-results');
        
        if (data.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle mr-2"></i>Messaging System Fixed!</h5>
                    <p>${data.message}</p>
                    <small class="text-muted">Refresh the page to see updated status.</small>
                </div>
            `;
            
            // Refresh page after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle mr-2"></i>Fix Failed</h5>
                    <p>Error: ${data.error}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('fix-results').innerHTML = `
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle mr-2"></i>Network Error</h5>
                <p>Failed to communicate with server. Please try again.</p>
            </div>
        `;
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function fixPrescriptions() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Fixing...';
    
    fetch('{{ route("database-fixes.prescriptions") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('fix-results');
        
        if (data.success) {
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle mr-2"></i>Prescriptions System Fixed!</h5>
                    <p>${data.message}</p>
                    ${data.columns_added.length > 0 ? 
                        '<p><strong>Added columns:</strong> ' + data.columns_added.join(', ') + '</p>' : 
                        ''}
                    <small class="text-muted">Refresh the page to see updated status.</small>
                </div>
            `;
            
            // Refresh page after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle mr-2"></i>Fix Failed</h5>
                    <p>Error: ${data.error}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('fix-results').innerHTML = `
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle mr-2"></i>Network Error</h5>
                <p>Failed to communicate with server. Please try again.</p>
            </div>
        `;
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}
</script>
@stop