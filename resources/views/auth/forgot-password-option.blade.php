@extends('adminlte::page')

@section('title', 'Forgot Password | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-unlock-alt mr-2"></i>Forgot Your Password?
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patient.profile.show') }}">My Profile</a></li>
                <li class="breadcrumb-item active">Forgot Password</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Password Reset Options
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>You are currently logged in.</strong> 
                        Choose one of the options below to change or reset your password.
                    </div>

                    <div class="row">
                        <!-- Change Password Option -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-key mr-2"></i>Change Password
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>If you remember your current password, you can change it directly.</p>
                                    <ul>
                                        <li>Faster and more secure</li>
                                        <li>No need to check email</li>
                                        <li>Stay logged in</li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('patient.profile.edit') }}#password-section" class="btn btn-primary btn-block">
                                        <i class="fas fa-edit mr-2"></i>Change Password Now
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Password Option -->
                        <div class="col-md-6">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-envelope mr-2"></i>Reset via Email
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>If you forgot your current password, we can send you a reset link.</p>
                                    <ul>
                                        <li>Reset link sent to your email</li>
                                        <li>You will be logged out</li>
                                        <li>Follow the email instructions</li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#resetConfirmModal">
                                        <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('patient.profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="resetConfirmModal" tabindex="-1" role="dialog" aria-labelledby="resetConfirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('password.request.reset') }}">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title" id="resetConfirmModalLabel">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Password Reset
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Warning:</strong> This action will log you out of your account.
                        </div>
                        
                        <p>We will send a password reset link to your email address:</p>
                        <p class="text-center"><strong>{{ Auth::user()->email }}</strong></p>
                        
                        <p>After clicking "Send Reset Link":</p>
                        <ol>
                            <li>You will be logged out</li>
                            <li>Check your email for the reset link</li>
                            <li>Click the link to set a new password</li>
                            <li>Log in with your new password</li>
                        </ol>

                        <div class="form-group mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmation" name="confirmation" required>
                                <label class="custom-control-label" for="confirmation">
                                    I understand that I will be logged out and confirm that I want to reset my password via email.
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .card-outline {
        border-top: 3px solid;
    }
    .card-outline.card-primary {
        border-top-color: #007bff;
    }
    .card-outline.card-warning {
        border-top-color: #ffc107;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Enable submit button only when checkbox is checked
    $('#confirmation').on('change', function() {
        const submitBtn = $('#resetConfirmModal').find('button[type="submit"]');
        if ($(this).is(':checked')) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });
    
    // Initially disable submit button
    $('#resetConfirmModal').find('button[type="submit"]').prop('disabled', true);
    
    // Reset checkbox when modal is hidden
    $('#resetConfirmModal').on('hidden.bs.modal', function() {
        $('#confirmation').prop('checked', false);
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
});
</script>
@endsection