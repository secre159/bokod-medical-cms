@extends('adminlte::page')

@section('title', 'Quick Reference Guide | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-tachometer-alt text-info mr-2"></i>{{ $title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documentation.index') }}">Documentation</a></li>
                <li class="breadcrumb-item active">Quick Guide</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Reference Guide
                </h3>
                <div class="card-tools">
                    <a href="{{ route('documentation.export', 'quick-guide') }}" class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-download mr-1"></i>Export PDF
                    </a>
                    <a href="{{ route('documentation.admin-guide') }}" class="btn btn-outline-info btn-sm ml-2">
                        <i class="fas fa-book mr-1"></i>Complete Guide
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="lead">
                    Visual workflows and quick reference for efficient daily operations
                </p>
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted">
                            This quick guide provides flowcharts, decision trees, and checklists for rapid reference 
                            during daily CMS operations. Perfect for experienced users who need quick reminders.
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
            <!-- Quick Navigation Sidebar -->
            <div class="col-md-3">
                <div class="card card-secondary card-outline sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Quick Navigation
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <nav class="nav nav-pills flex-column" id="quick-nav">
                            <a class="nav-link" href="#-getting-started-flow">Getting Started</a>
                            <a class="nav-link" href="#-patient-management-workflow">Patient Workflow</a>
                            <a class="nav-link" href="#-medicine-management-flow">Medicine Management</a>
                            <a class="nav-link" href="#-appointment-system-flow">Appointments</a>
                            <a class="nav-link" href="#-prescription-management-flow">Prescriptions</a>
                            <a class="nav-link" href="#-messaging-system-flow">Messaging</a>
                            <a class="nav-link" href="#-reports--analytics-flow">Reports</a>
                            <a class="nav-link" href="#-system-administration-flow">Administration</a>
                            <a class="nav-link" href="#-troubleshooting-decision-tree">Troubleshooting</a>
                            <a class="nav-link" href="#-daily-admin-tasks-checklist">Daily Tasks</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body documentation-content quick-guide">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .quick-guide {
        font-size: 16px;
        line-height: 1.6;
    }
    
    .quick-guide h1 {
        color: #17a2b8;
        border-bottom: 3px solid #17a2b8;
        padding-bottom: 10px;
        margin-top: 30px;
        margin-bottom: 20px;
    }
    
    .quick-guide h2 {
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
        margin-top: 25px;
        margin-bottom: 15px;
        position: relative;
    }
    
    .quick-guide h2:before {
        content: "🔄";
        margin-right: 8px;
    }
    
    .quick-guide h3 {
        color: #6c757d;
        margin-top: 20px;
        margin-bottom: 12px;
    }
    
    .quick-guide pre {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-left: 4px solid #17a2b8;
        border-radius: 6px;
        padding: 15px;
        font-size: 14px;
        overflow-x: auto;
        font-family: 'Monaco', 'Consolas', monospace;
    }
    
    .quick-guide code {
        background-color: #e8f6f8;
        color: #17a2b8;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 90%;
        font-family: 'Monaco', 'Consolas', monospace;
    }
    
    .quick-guide blockquote {
        border-left: 4px solid #17a2b8;
        background-color: #e8f6f8;
        padding: 15px 20px;
        margin: 20px 0;
        font-style: italic;
    }
    
    .quick-guide ul, .quick-guide ol {
        margin-bottom: 15px;
    }
    
    .quick-guide li {
        margin-bottom: 5px;
    }
    
    /* Special styling for workflow boxes */
    .quick-guide pre {
        position: relative;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .quick-guide pre:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }
    
    /* Navigation styling */
    .nav-link {
        padding: 8px 15px;
        color: #6c757d;
        border-radius: 0;
        border-left: 3px solid transparent;
        font-size: 14px;
    }
    
    .nav-link:hover {
        background-color: #e8f6f8;
        border-left-color: #17a2b8;
        color: #17a2b8;
    }
    
    .nav-link.active {
        background-color: #e8f6f8 !important;
        border-left-color: #17a2b8 !important;
        color: #17a2b8 !important;
        font-weight: 600 !important;
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Highlight targeted sections */
    .quick-guide :target {
        animation: highlight 2s ease-in-out;
    }
    
    @keyframes highlight {
        0% { background-color: #b8daff; }
        100% { background-color: transparent; }
    }
    
    /* Checklist styling */
    .quick-guide ul li {
        position: relative;
        padding-left: 25px;
    }
    
    .quick-guide ul li:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #28a745;
        font-weight: bold;
    }
    
    /* Color-coded sections */
    .quick-guide h2:nth-of-type(1) { border-bottom-color: #28a745; }
    .quick-guide h2:nth-of-type(2) { border-bottom-color: #17a2b8; }
    .quick-guide h2:nth-of-type(3) { border-bottom-color: #ffc107; }
    .quick-guide h2:nth-of-type(4) { border-bottom-color: #dc3545; }
    .quick-guide h2:nth-of-type(5) { border-bottom-color: #6f42c1; }
    
    /* Status indicators in text */
    .quick-guide strong {
        color: #495057;
    }
    
    /* Table styling for better readability */
    .quick-guide table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .quick-guide table th,
    .quick-guide table td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: left;
    }
    
    .quick-guide table th {
        background-color: #17a2b8;
        color: white;
        font-weight: 600;
    }
    
    .quick-guide table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Implement scroll spy
    $('body').scrollspy({
        target: '#quick-nav',
        offset: 100
    });
    
    // Smooth scrolling for navigation links
    $('#quick-nav a').on('click', function(e) {
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
        $('#quick-nav a').each(function() {
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
    
    // Add interactive hover effects to workflow boxes
    $('.quick-guide pre').hover(
        function() {
            $(this).css('transform', 'scale(1.02)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );
});
</script>
@endsection