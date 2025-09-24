@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Registration Successful!')

@section('auth_body')
<div class="card-body text-center">
    <div class="mb-4">
        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
    </div>
            
            @if(session('success'))
                <p class="text-muted mb-4">
                    {{ session('success') }}
                </p>
            @else
                <p class="text-muted mb-4">
                    Your account has been created successfully. You can now access the BSU Health Portal.
                </p>
            @endif

            <div class="alert alert-info mb-4">
                <h5><i class="icon fas fa-info"></i> What's Next?</h5>
                <ul class="text-left mb-0">
                    <li><strong>Login:</strong> Use your registered email and password to login</li>
                    <li><strong>Book Appointments:</strong> Schedule health center visits online</li>
                    <li><strong>View Records:</strong> Access your appointment history and prescriptions</li>
                    <li><strong>Stay Connected:</strong> Receive important health updates</li>
                </ul>
            </div>

            <div class="alert alert-success mb-4">
                <h6><i class="icon fas fa-graduation-cap"></i> BSU Students Benefits</h6>
                <p class="mb-0">As a BSU student, you have free access to basic health services at the campus health center. Book appointments online to skip the walk-in queue!</p>
            </div>

            @if(str_contains(session('success', ''), 'pending admin approval'))
                {{-- Pending approval state --}}
                <div class="alert alert-warning mb-4">
                    <h6><i class="icon fas fa-clock"></i> Pending Admin Review</h6>
                    <p class="mb-0">Your registration is being reviewed by our admin team. You will receive an email notification once your account is approved and ready for use.</p>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('landing') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>
                </div>
            @else
                {{-- Approved/Auto-approved state --}}
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login Now
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('landing') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>
                </div>
            @endif

            <!-- Quick Tips -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="text-muted">Quick Tips:</h6>
                <div class="row text-center">
                    <div class="col-md-4 mb-2">
                        <i class="fas fa-clock text-primary mb-2"></i>
                        <p class="small mb-0"><strong>Office Hours</strong><br>Monday - Friday<br>8:00 AM - 5:00 PM</p>
                    </div>
                    <div class="col-md-4 mb-2">
                        <i class="fas fa-map-marker-alt text-primary mb-2"></i>
                        <p class="small mb-0"><strong>Location</strong><br>BSU Bokod Campus<br>Health Center</p>
                    </div>
                    <div class="col-md-4 mb-2">
                        <i class="fas fa-phone text-primary mb-2"></i>
                        <p class="small mb-0"><strong>Emergency</strong><br>Call: (074) 422-XXXX<br>24/7 Support</p>
                    </div>
                </div>
            </div>
</div>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted">
            <small>
                <i class="fas fa-graduation-cap mr-1"></i>
                BSU Health Portal - Welcome!
            </small>
        </p>
    </div>
@stop
