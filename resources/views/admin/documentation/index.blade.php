@extends('adminlte::page')

@section('title', 'Documentation')

@section('content_header')
    <h1>
        <i class="fas fa-book"></i> System Documentation
        <small class="text-muted">Comprehensive guides and references</small>
    </h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Welcome to the Documentation Center</h3>
            </div>
            <div class="card-body">
                <p class="lead">
                    Welcome to the comprehensive documentation for your CMS system. Here you'll find detailed guides, 
                    tutorials, and references for all system features.
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> 
                    Use the sections below to navigate to specific documentation areas. 
                    Each section contains detailed information and step-by-step guides.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach($sections as $index => $section)
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="card h-100 documentation-card">
            <div class="card-body d-flex flex-column">
                <div class="text-center mb-3">
                    <i class="{{ $section['icon'] }} fa-3x text-primary"></i>
                </div>
                <h5 class="card-title text-center">{{ $section['title'] }}</h5>
                <p class="card-text text-muted flex-grow-1">{{ $section['description'] }}</p>
                <div class="mt-auto">
                    <a href="{{ route($section['route']) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-arrow-right"></i> View Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if(($index + 1) % 3 == 0)
        </div><div class="row">
    @endif
    @endforeach
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-question-circle"></i> Need Help?</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-search"></i> Quick Search Tips</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-chevron-right text-success"></i> Use the browser's find function (Ctrl+F) to search within pages</li>
                            <li><i class="fas fa-chevron-right text-success"></i> Check the table of contents for specific topics</li>
                            <li><i class="fas fa-chevron-right text-success"></i> Look for code examples in relevant sections</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-star"></i> Documentation Features</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-chevron-right text-info"></i> Step-by-step tutorials and guides</li>
                            <li><i class="fas fa-chevron-right text-info"></i> Screenshots and visual examples</li>
                            <li><i class="fas fa-chevron-right text-info"></i> Best practices and tips</li>
                            <li><i class="fas fa-chevron-right text-info"></i> Troubleshooting sections</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.documentation-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e3e6f0;
}

.documentation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: #007bff;
}

.documentation-card .card-body {
    padding: 1.5rem;
}

.documentation-card .fa-3x {
    margin-bottom: 1rem;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.card-header.bg-success {
    background-color: #28a745 !important;
    color: white;
}

.card-header.bg-success .card-title {
    color: white;
}

.list-unstyled li {
    padding: 0.25rem 0;
}

.text-success {
    color: #28a745 !important;
}

.text-info {
    color: #17a2b8 !important;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Add smooth scroll effect for better UX
    $('.documentation-card').on('click', function(e) {
        if (!$(e.target).hasClass('btn')) {
            $(this).find('.btn').click();
        }
    });
    
    // Add loading state to buttons
    $('.btn').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        // Reset after a short delay (in case navigation is slow)
        setTimeout(function() {
            $btn.html(originalText);
        }, 3000);
    });
});
</script>
@stop