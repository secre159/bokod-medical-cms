@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* BSU Official Color Palette */
        :root {
            --bsu-primary-green: #0F5132;
            --bsu-secondary-green: #198754;
            --bsu-light-green: #20c997;
            --bsu-accent-yellow: #FFD60A;
            --bsu-golden-yellow: #FFC107;
            --text-dark: #1a202c;
            --text-light: #6c757d;
            --success-color: #198754;
            --warning-color: #FFC107;
            --error-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bsu-primary-green) 0%, var(--bsu-secondary-green) 50%, var(--bsu-light-green) 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background decoration - BSU themed */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(255, 214, 10, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(25, 135, 84, 0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
            z-index: -2;
        }
        
        /* Floating shapes - BSU themed */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(25, 135, 84, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 214, 10, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            z-index: -1;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-30px, -50px) rotate(120deg); }
            66% { transform: translate(30px, -20px) rotate(240deg); }
        }
        
        .card {
            border: none;
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.25),
                0 10px 25px rgba(0,0,0,0.15),
                inset 0 1px 0 rgba(255,255,255,0.1);
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
            position: relative;
            z-index: 1;
            max-width: 450px;
            margin: 0 auto;
        }
        
        .login-box {
            width: 100%;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--bsu-primary-green) 0%, var(--bsu-secondary-green) 100%);
            color: white;
            text-align: center;
            border-radius: 1rem 1rem 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255, 214, 10, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 80%, rgba(255, 214, 10, 0.05) 0%, transparent 30%);
            z-index: 0;
        }
        
        .card-header h4 {
            position: relative;
            z-index: 1;
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .login-box-msg {
            text-align: center;
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 2px solid #ced4da;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--bsu-secondary-green);
            box-shadow: 0 0 0 0.125rem rgba(25, 135, 84, 0.25);
            transform: translateY(-1px);
        }
        
        .input-group-text {
            background: rgba(25, 135, 84, 0.1);
            border: 2px solid #ced4da;
            border-left: none;
            color: var(--bsu-secondary-green);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-light-green) 100%);
            border: none;
            color: white;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 214, 10, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(25, 135, 84, 0.4);
        }
        
        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: var(--bsu-primary-green);
        }
        
        .icheck-primary > input:checked + label::before {
            background-color: var(--bsu-secondary-green);
            border-color: var(--bsu-secondary-green);
        }
        
        .icheck-primary > label::before {
            border: 2px solid #ced4da;
        }
        
        .card-footer {
            background: rgba(25, 135, 84, 0.05);
            border-top: 1px solid rgba(25, 135, 84, 0.1);
            border-radius: 0 0 1rem 1rem;
            padding: 1rem;
        }
        
        .text-muted {
            color: var(--text-light) !important;
        }
        
        a {
            color: var(--bsu-secondary-green);
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: var(--bsu-primary-green);
            transform: translateY(-1px);
        }
        
        /* Welcome badge */
        .welcome-badge {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 214, 10, 0.9);
            color: var(--bsu-primary-green);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.875rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(15, 81, 50, 0.3);
            z-index: 10;
            box-shadow: 0 4px 15px rgba(255, 214, 10, 0.3);
            transition: all 0.3s ease;
        }
        
        .welcome-badge:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 214, 10, 0.4);
        }
        
        /* Page decorations */
        .page-decoration {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }
        
        .decoration-top-right {
            top: 15%;
            right: 10%;
            font-size: 80px;
            color: rgba(255, 214, 10, 0.1);
            animation: pulse 4s ease-in-out infinite;
        }
        
        .decoration-bottom-left {
            bottom: 15%;
            left: 10%;
            font-size: 60px;
            color: rgba(25, 135, 84, 0.1);
            animation: pulse 3s ease-in-out infinite reverse;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.1; transform: scale(1.1); }
        }
        
        @media (max-width: 768px) {
            .card {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .decoration-top-right,
            .decoration-bottom-left {
                display: none;
            }
            
            .welcome-badge {
                position: relative;
                top: auto;
                left: auto;
                margin: 1rem auto;
                display: block;
                text-align: center;
                width: fit-content;
            }
        }
    </style>
@stop

@section('auth_header')
    <!-- Page Decorations -->
    <div class="welcome-badge">
        <i class="fas fa-university"></i> BSU Health Portal
    </div>
    
    <div class="page-decoration decoration-top-right">
        <i class="fas fa-user-graduate"></i>
    </div>
    <div class="page-decoration decoration-bottom-left">
        <i class="fas fa-stethoscope"></i>
    </div>
    
    <div class="card-header">
        <h4>🎓 BSU Student Login</h4>
        <p class="mb-0" style="opacity: 0.9; position: relative; z-index: 1;">Sign in to access your health portal</p>
    </div>
@stop

@section('auth_body')
    <div class="card-body">
        <p class="login-box-msg">
            <i class="fas fa-sign-in-alt mr-2" style="color: var(--bsu-secondary-green);"></i>
            Welcome back! Please sign in to continue
        </p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email field --}}
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autofocus autocomplete="username">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Password field --}}
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('Password') }}" required autocomplete="current-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Login field --}}
            <div class="row">
                <div class="col-7">
                    <div class="icheck-primary" title="{{ __('Remember me') }}">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">{{ __('Remember me') }}</label>
                    </div>
                </div>
                <div class="col-5">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        {{ __('Sign In') }}
                    </button>
                </div>
            </div>

        </form>

        {{-- Forgot Password Link --}}
        @if (Route::has('password.request'))
            <p class="mb-1">
                <a href="{{ route('password.request') }}" class="text-center">
                    <i class="fas fa-key mr-1"></i>
                    {{ __('I forgot my password') }}
                </a>
            </p>
        @endif
    </div>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted">
            <small>
                <i class="fas fa-graduation-cap mr-1" style="color: var(--bsu-secondary-green);"></i>
                BSU Health Portal - Student Health Management System
            </small>
        </p>
        <p class="text-center mb-0">
            <small>
                <a href="{{ route('register') }}" style="text-decoration: none;">
                    <i class="fas fa-user-plus mr-1"></i>
                    Don't have an account? Register here
                </a>
            </small>
        </p>
    </div>
@stop
