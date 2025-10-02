@extends('adminlte::master')

{{-- Inject custom content --}}
@section('adminlte_css')
    @parent
    
    {{-- Performance optimization CSS --}}
    <link rel="stylesheet" href="{{ asset('css/performance.css') }}">
    
    <style>
        /* Additional inline optimizations */
        .preloader {
            transition: opacity 0.1s !important;
        }
        body {
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Critical CSS for immediate rendering */
        .main-header,
        .content-wrapper {
            display: block !important;
        }
    </style>
@stop

@section('adminlte_js')
    @parent
    @include('components.message-modal')
    
    {{-- Loading optimizer --}}
    <script src="{{ asset('js/loading-optimizer.js') }}"></script>
    
    {{-- Ensure jQuery is available before loading our scripts --}}
    <script>
        // Wait for jQuery to be available before loading custom scripts
        if (typeof $ === 'undefined') {
            console.log('Waiting for jQuery to load...');
            var jQueryInterval = setInterval(function() {
                if (typeof $ !== 'undefined') {
                    clearInterval(jQueryInterval);
                    console.log('jQuery loaded, initializing custom scripts...');
                    loadCustomScripts();
                }
            }, 50);
            
            // Timeout after 10 seconds
            setTimeout(function() {
                if (typeof $ === 'undefined') {
                    console.warn('jQuery not loaded after 10 seconds, loading scripts anyway...');
                    loadCustomScripts();
                }
            }, 10000);
        } else {
            loadCustomScripts();
        }
        
        function loadCustomScripts() {
            // Load numeric validation
            var numericScript = document.createElement('script');
            numericScript.src = '{{ asset('js/numeric-validation.js') }}';
            document.head.appendChild(numericScript);
            
            // Load universal modal (after numeric validation)
            numericScript.onload = function() {
                var modalScript = document.createElement('script');
                modalScript.src = '{{ asset('js/universal-modal.js') }}';
                document.head.appendChild(modalScript);
                
                // Load profile picture refresh system
                var profileRefreshScript = document.createElement('script');
                profileRefreshScript.src = '{{ asset('js/profile-picture-refresh.js') }}';
                document.head.appendChild(profileRefreshScript);
            };
        }
    </script>
    
    {{-- Additional performance optimizations --}}
    <script>
        // Optimize AdminLTE initialization
        $(document).ready(function() {
            // Force hide any remaining preloaders
            $('.preloader').hide();
            $('.overlay').hide();
            
            // Enable fast transitions
            $.fn.modal.Constructor.Default.backdrop = 'static';
            $.fn.modal.Constructor.Default.keyboard = false;
            
            console.log('AdminLTE optimizations applied');
        });
        
        // Universal jQuery ready function for safe initialization
        window.safeJQueryReady = function(callback) {
            function initWhenReady() {
                if (typeof $ === 'undefined') {
                    setTimeout(initWhenReady, 50);
                    return;
                }
                $(document).ready(callback);
            }
            initWhenReady();
        };
    </script>
@stop
