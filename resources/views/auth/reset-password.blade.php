@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', __('Reset Password'))

@section('auth_body')
    <div class="card-body">
        <p class="login-box-msg">
            {{ __('Enter your new password below. Make sure to choose a strong, secure password.') }}
        </p>

        <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email field --}}
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}" placeholder="{{ __('Email') }}" 
                       required autofocus autocomplete="username">
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

            {{-- New Password field --}}
            <div class="input-group mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('New Password') }}" required autocomplete="new-password" minlength="8" id="password">
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

            {{-- Confirm Password field --}}
            <div class="input-group mb-3">
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="{{ __('Confirm New Password') }}" required autocomplete="new-password" minlength="8" id="password_confirmation">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="invalid-feedback" id="password-mismatch" style="display: none;">
                    Passwords do not match
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-1">
                            <li>At least 8 characters long</li>
                            <li>Include uppercase and lowercase letters</li>
                            <li>Include numbers and symbols</li>
                            <li>Must be different from your previous password</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Reset password button --}}
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-key mr-2"></i>
                        {{ __('Reset Password') }}
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
                <i class="fas fa-shield-alt mr-1"></i>
                {{ __('After resetting your password, you will be automatically logged in.') }}
            </small>
        </p>
    </div>
@stop

@section('adminlte_js')
    <script>
        $(document).ready(function() {
            // Real-time password confirmation validation
            $('#password_confirmation').on('keyup', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                
                if (confirmPassword && password !== confirmPassword) {
                    $(this).addClass('is-invalid');
                    $('#password-mismatch').show();
                } else if (confirmPassword && password === confirmPassword) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $('#password-mismatch').hide();
                } else {
                    $(this).removeClass('is-invalid is-valid');
                    $('#password-mismatch').hide();
                }
            });

            // Form validation
            $('#resetPasswordForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();
                
                if (!password || !confirmPassword) {
                    e.preventDefault();
                    modalError('Please fill in both password fields.', 'Missing Information');
                    return false;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    modalError('Passwords do not match. Please ensure both password fields have the same value.', 'Password Mismatch');
                    $('#password_confirmation').focus();
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    modalWarning('Password must be at least 8 characters long.', 'Password Too Short');
                    $('#password').focus();
                    return false;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Resetting Password...').prop('disabled', true);
                
                // Re-enable button after 10 seconds as failsafe
                setTimeout(function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }, 10000);
            });
        });
    </script>
@stop