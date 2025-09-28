@extends('adminlte::page')

@section('title', 'Documentation | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-book text-primary mr-2"></i>Documentation</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Documentation</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-question-circle mr-2"></i>
                    Welcome to Bokod CMS Documentation
                </h3>
            </div>
            <div class="card-body">
                <p class="lead">
                    Get the most out of your Bokod CMS system with our comprehensive documentation and guides.
                </p>
                <p>
                    Whether you're a new admin getting started or an experienced user looking for specific information, 
                    our documentation has you covered with step-by-step instructions, workflows, and troubleshooting guides.
                </p>
            </div>
        </div>

        <!-- Main Documentation Cards -->
        <div class="row">
            <!-- Admin Usage Guide -->
            <div class="col-lg-6 col-md-12">
                <div class="card card-success card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-cog text-success mr-2"></i>
                            Complete Admin Guide
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Comprehensive system administration manual</strong></p>
                        <p class="text-muted">
                            Complete guide covering all system features including patient management, 
                            medicine inventory, appointments, prescriptions, messaging, and system settings.
                        </p>
                        
                        <h6 class="mt-3">What's Included:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i> Getting Started Checklist</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Step-by-Step Instructions</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Best Practices</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Troubleshooting Guide</li>
                            <li><i class="fas fa-check text-success mr-2"></i> Daily/Weekly/Monthly Tasks</li>
                        </ul>
                        
                        <div class="mt-4">
                            <a href="{{ route('documentation.admin-guide') }}" class="btn btn-success">
                                <i class="fas fa-book-open mr-2"></i>View Complete Guide
                            </a>
                            <a href="{{ route('documentation.export', 'admin-guide') }}" class="btn btn-outline-success ml-2" target="_blank">
                                <i class="fas fa-download mr-2"></i>Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Reference Guide -->
            <div class="col-lg-6 col-md-12">
                <div class="card card-info card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tachometer-alt text-info mr-2"></i>
                            Quick Reference Guide
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Visual workflows and quick reference</strong></p>
                        <p class="text-muted">
                            Streamlined visual guide with flowcharts, decision trees, and quick reference 
                            materials for efficient daily operations.
                        </p>
                        
                        <h6 class="mt-3">What's Included:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-info mr-2"></i> Visual Workflows</li>
                            <li><i class="fas fa-check text-info mr-2"></i> Decision Trees</li>
                            <li><i class="fas fa-check text-info mr-2"></i> Daily Task Checklists</li>
                            <li><i class="fas fa-check text-info mr-2"></i> Keyboard Shortcuts</li>
                            <li><i class="fas fa-check text-info mr-2"></i> Emergency Procedures</li>
                        </ul>
                        
                        <div class="mt-4">
                            <a href="{{ route('documentation.quick-guide') }}" class="btn btn-info">
                                <i class="fas fa-bolt mr-2"></i>View Quick Guide
                            </a>
                            <a href="{{ route('documentation.export', 'quick-guide') }}" class="btn btn-outline-info ml-2" target="_blank">
                                <i class="fas fa-download mr-2"></i>Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module-Specific Help -->
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-puzzle-piece mr-2"></i>
                    Module-Specific Help
                </h3>
            </div>
            <div class="card-body">
                <p>Get help with specific system modules:</p>
                
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'dashboard') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Help
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'patients') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-users mr-2"></i>Patient Management
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'medicines') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-pills mr-2"></i>Medicine Management
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'appointments') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-calendar-alt mr-2"></i>Appointments
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'prescriptions') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-prescription mr-2"></i>Prescriptions
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="{{ route('documentation.module-help', 'messages') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-comments mr-2"></i>Messaging System
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Documentation -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-2"></i>
                    Search Documentation
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="docSearch" placeholder="Search for help topics, features, or procedures...">
                            <div class="input-group-append">
                                <button class="btn btn-warning" type="button" onclick="searchDocs()">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Search across all documentation for specific topics or features</small>
                    </div>
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" class="mt-3" style="display: none;">
                    <h6>Search Results:</h6>
                    <div id="resultsContainer"></div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-box bg-gradient-success">
                    <span class="info-box-icon"><i class="fas fa-rocket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">New to the System?</span>
                        <span class="info-box-number">Start Here</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            <a href="{{ route('documentation.admin-guide') }}#getting-started" class="text-white">
                                <u>View Getting Started Guide →</u>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-box bg-gradient-info">
                    <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Need Quick Help?</span>
                        <span class="info-box-number">Troubleshooting</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            <a href="{{ route('documentation.admin-guide') }}#troubleshooting" class="text-white">
                                <u>View Common Issues →</u>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .card.h-100 {
        height: calc(100% - 1rem);
    }
    
    .info-box .progress-description a {
        font-weight: bold;
    }
    
    #searchResults .list-group-item {
        border-left: 3px solid #ffc107;
    }
</style>
@endsection

@section('js')
<script>
function searchDocs() {
    const query = document.getElementById('docSearch').value.trim();
    const resultsDiv = document.getElementById('searchResults');
    const container = document.getElementById('resultsContainer');
    
    if (query.length < 3) {
        alert('Please enter at least 3 characters to search');
        return;
    }
    
    // Show loading
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
    resultsDiv.style.display = 'block';
    
    // Make AJAX request
    fetch(`{{ route('documentation.search') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.results.length === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>No results found for "' + query + '"</div>';
            } else {
                let html = '<div class="list-group">';
                data.results.forEach(result => {
                    html += `
                        <a href="${result.url}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${result.title}</h6>
                            </div>
                            <p class="mb-1">${result.description}</p>
                        </a>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error searching documentation</div>';
            console.error('Search error:', error);
        });
}

// Enable search on Enter key
document.getElementById('docSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchDocs();
    }
});
</script>
@endsection