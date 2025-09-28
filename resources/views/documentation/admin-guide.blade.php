@extends('adminlte::page')

@section('title', 'Admin Usage Guide | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-user-cog text-success mr-2"></i>{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documentation.index') }}">Documentation</a></li>
                <li class="breadcrumb-item active">Admin Guide</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book-open mr-2"></i>
                    Complete Admin Usage Guide
                </h3>
                <div class="card-tools">
                    <a href="{{ route('documentation.export', 'admin-guide') }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-download mr-1"></i>Export PDF
                    </a>
                    <a href="{{ route('documentation.quick-guide') }}" class="btn btn-outline-success btn-sm ml-2">
                        <i class="fas fa-bolt mr-1"></i>Quick Guide
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="lead">
                    Comprehensive system administration manual for Bokod CMS
                </p>
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted">
                            This complete guide covers all aspects of system administration, from getting started 
                            to advanced features and troubleshooting. Use the table of contents to navigate to 
                            specific sections.
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <small class="text-muted">
                            <i class="fas fa-clock mr-1"></i>
                            Last updated: {{ date('M d, Y g:i A', $lastUpdated) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentation Content -->
        <div class="row">
            <!-- Table of Contents Sidebar -->
            <div class="col-md-3">
                <div class="card card-secondary card-outline sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Table of Contents
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <nav class="nav nav-pills flex-column" id="documentation-nav">
                            <a class="nav-link" href="#system-overview">System Overview</a>
                            <a class="nav-link" href="#getting-started">Getting Started</a>
                            <a class="nav-link" href="#dashboard-overview">Dashboard Overview</a>
                            <a class="nav-link" href="#patient-management">Patient Management</a>
                            <a class="nav-link" href="#medicine-management">Medicine Management</a>
                            <a class="nav-link" href="#appointments-management">Appointments Management</a>
                            <a class="nav-link" href="#prescriptions-management">Prescriptions Management</a>
                            <a class="nav-link" href="#messaging-system">Messaging System</a>
                            <a class="nav-link" href="#reports--analytics">Reports & Analytics</a>
                            <a class="nav-link" href="#user-management">User Management</a>
                            <a class="nav-link" href="#system-settings">System Settings</a>
                            <a class="nav-link" href="#troubleshooting">Troubleshooting</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body documentation-content">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .documentation-content {
        font-size: 16px;
        line-height: 1.6;
    }
    
    .documentation-content h1 {
        color: #28a745;
        border-bottom: 3px solid #28a745;
        padding-bottom: 10px;
        margin-top: 30px;
        margin-bottom: 20px;
    }
    
    .documentation-content h2 {
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
        margin-top: 25px;
        margin-bottom: 15px;
    }
    
    .documentation-content h3 {
        color: #6c757d;
        margin-top: 20px;
        margin-bottom: 12px;
    }
    
    .documentation-content pre {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px;
        font-size: 14px;
        overflow-x: auto;
    }
    
    .documentation-content code {
        background-color: #f8f9fa;
        color: #e83e8c;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 90%;
    }
    
    .documentation-content blockquote {
        border-left: 4px solid #28a745;
        background-color: #f8f9fa;
        padding: 15px 20px;
        margin: 20px 0;
        font-style: italic;
    }
    
    .documentation-content ul, .documentation-content ol {
        margin-bottom: 15px;
    }
    
    .documentation-content li {
        margin-bottom: 5px;
    }
    
    .documentation-content table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    
    .documentation-content table th,
    .documentation-content table td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: left;
    }
    
    .documentation-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .documentation-content .nav-link {
        padding: 8px 15px;
        color: #6c757d;
        border-radius: 0;
        border-left: 3px solid transparent;
    }
    
    .documentation-content .nav-link:hover {
        background-color: #f8f9fa;
        border-left-color: #28a745;
        color: #28a745;
    }
    
    .documentation-content .nav-link.active {
        background-color: #e8f5e8;
        border-left-color: #28a745;
        color: #28a745;
        font-weight: 600;
    }
    
    /* Scroll spy active states */
    .nav-link.active {
        background-color: #e8f5e8 !important;
        border-left-color: #28a745 !important;
        color: #28a745 !important;
        font-weight: 600 !important;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Highlight targeted sections */
    .documentation-content :target {
        animation: highlight 2s ease-in-out;
    }
    
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Implement scroll spy
    $('body').scrollspy({
        target: '#documentation-nav',
        offset: 100
    });
    
    // Smooth scrolling for navigation links
    $('#documentation-nav a').on('click', function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
        }
    });
    
    // Update URL hash on scroll
    $(window).on('scroll', function() {
        let current = '';
        $('#documentation-nav a').each(function() {
            const section = $($(this).attr('href'));
            if (section.length && $(window).scrollTop() >= section.offset().top - 100) {
                current = $(this).attr('href');
            }
        });
        
        if (current) {
            history.replaceState(null, null, current);
        }
    });
    
    // Handle initial hash in URL
    if (window.location.hash) {
        setTimeout(function() {
            const target = $(window.location.hash);
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 500);
            }
        }, 100);
    }
});
</script>
@endsection