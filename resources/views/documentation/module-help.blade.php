@extends('adminlte::page')

@section('title', $module . ' Help | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-question-circle text-warning mr-2"></i>{{ $module }} Help</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documentation.index') }}">Documentation</a></li>
                <li class="breadcrumb-item active">{{ $module }} Help</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ $content['title'] }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('documentation.admin-guide') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-book mr-1"></i>Complete Guide
                    </a>
                    <a href="{{ route('documentation.quick-guide') }}" class="btn btn-outline-warning btn-sm ml-2">
                        <i class="fas fa-bolt mr-1"></i>Quick Guide
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="lead">
                    Quick help and guidance for the {{ $module }} module
                </p>
            </div>
        </div>

        <!-- Help Content -->
        <div class="row">
            @foreach($content['sections'] as $sectionTitle => $sectionContent)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chevron-right text-warning mr-2"></i>
                            {{ $sectionTitle }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $sectionContent }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Related Links -->
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Related Documentation
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Complete Guides:</h6>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('documentation.admin-guide') }}#{{ strtolower($module) }}-management">
                                    <i class="fas fa-book text-success mr-2"></i>
                                    {{ $module }} in Admin Guide
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('documentation.quick-guide') }}#{{ strtolower($module) }}-workflow">
                                    <i class="fas fa-bolt text-info mr-2"></i>
                                    {{ $module }} Quick Reference
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Other Modules:</h6>
                        <div class="btn-group-vertical btn-group-sm w-100">
                            <a href="{{ route('documentation.module-help', 'dashboard') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Help
                            </a>
                            <a href="{{ route('documentation.module-help', 'patients') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-users mr-2"></i>Patient Management
                            </a>
                            <a href="{{ route('documentation.module-help', 'medicines') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-pills mr-2"></i>Medicine Management
                            </a>
                            <a href="{{ route('documentation.module-help', 'appointments') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-calendar-alt mr-2"></i>Appointments
                            </a>
                            <a href="{{ route('documentation.module-help', 'prescriptions') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-prescription mr-2"></i>Prescriptions
                            </a>
                            <a href="{{ route('documentation.module-help', 'messages') }}" class="btn btn-outline-secondary text-left">
                                <i class="fas fa-comments mr-2"></i>Messaging System
                            </a>
                        </div>
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
    
    .card-header {
        border-bottom: 2px solid #ffc107;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }
    
    .list-unstyled a {
        color: #495057;
        text-decoration: none;
        padding: 5px 0;
        display: block;
    }
    
    .list-unstyled a:hover {
        color: #007bff;
        text-decoration: none;
    }
</style>
@endsection