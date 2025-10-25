@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <style>
        /* Simple, clean styling */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            padding: 20px 0;
        }
        
        .login-box {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .card {
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            background: white;
        }
        
        .card-header {
            background: #198754;
            color: white;
            text-align: center;
            border-radius: 8px 8px 0 0;
            padding: 20px;
        }
        
        .card-header h4 {
            margin: 0;
            font-weight: 500;
            font-size: 1.25rem;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .login-box-msg {
            text-align: center;
            color: #495057;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .form-control {
            padding: 12px 16px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: #198754;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .input-group-text {
            background: #e9ecef;
            border: 1px solid #ced4da;
            color: #495057;
            padding: 12px 16px;
        }
        
        .btn-primary {
            background-color: #198754;
            border-color: #198754;
            color: white;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 4px;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #157347;
            border-color: #146c43;
        }
        
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
            border-radius: 4px;
        }
        
        .card-footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            text-align: center;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        a {
            color: #198754;
            text-decoration: none;
        }
        
        a:hover {
            color: #146c43;
            text-decoration: underline;
        }
        
        /* Responsive design */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .login-box {
                max-width: 100%;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .card-header {
                padding: 15px;
            }
            
            .card-header h4 {
                font-size: 1.1rem;
            }
            
            .card-footer {
                padding: 15px;
            }
            
            .btn-primary {
                width: 100%;
            }
            
            .row > .col-7,
            .row > .col-5 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            
            .row > .col-5 {
                margin-bottom: 0;
            }
        }
    </style>
@stop

@section('auth_header')
    <div class="card-header">
        <h4>BSU Student Login</h4>
        <p class="mb-0" style="opacity: 0.9; font-size: 0.9rem;">Sign in to access your health portal</p>
    </div>
@stop

@section('auth_body')
    <div class="card-body">
        <p class="login-box-msg">
            Welcome back! Please sign in to continue
        </p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email field --}}
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="Email Address" required autofocus autocomplete="username">
                <div class="input-group-append">
                    <div class="input-group-text">
                        @
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
                       placeholder="Password" required autocomplete="current-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        🔒
                    </div>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Login controls --}}
            <div class="row">
                <div class="col-7">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                </div>
                <div class="col-5">
                    <button type="submit" class="btn btn-primary btn-block">
                        Sign In
                    </button>
                </div>
            </div>

        </form>

        {{-- Forgot Password Link --}}
        @if (Route::has('password.request'))
            <p class="mb-0 mt-3 text-center">
                <a href="{{ route('password.request') }}">
                    I forgot my password
                </a>
            </p>
        @endif
    </div>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted mb-2">
            <small>
                BSU Health Portal - Student Health Management System
            </small>
        </p>
        <p class="mb-0">
            <small>
                <a href="{{ route('register') }}">
                    Don't have an account? Register here
                </a>
            </small>
        </p>
    </div>
@stop
