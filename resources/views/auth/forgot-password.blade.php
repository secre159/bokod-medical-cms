@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('Reset Password'))

@section('auth_body')
    <div class="card-body">
        <p class="login-box-msg">
            {{ __('Forgot your password? No problem. Just enter your email address and we will send you a password reset link.') }}
        </p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            {{-- Email field --}}
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autofocus>
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

            {{-- Send reset link button --}}
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </div>

        </form>

        <p class="mt-3 mb-1">
            <a href="{{ route('login') }}" class="text-center">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('Back to Login') }}
            </a>
        </p>
    </div>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted">
            <small>
                <i class="fas fa-info-circle mr-1"></i>
                {{ __('If you don\'t receive an email, please check your spam folder or contact the administrator.') }}
            </small>
        </p>
    </div>
@stop