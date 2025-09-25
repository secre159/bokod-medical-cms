@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', 'Register as BSU Student')

@section('auth_body')
<p class="login-box-msg">Create your account to book health center appointments online</p>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('patient.register.submit') }}" method="post" id="registrationForm">
                @csrf

                <!-- Personal Information Section -->
                <div class="form-section mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-user"></i> Personal Information
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Full Name" value="{{ old('name') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user"></span>
                                    </div>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="BSU Email Address" value="{{ old('email') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Use your official BSU email for instant verification</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="password" name="password_confirmation" class="form-control" 
                                       placeholder="Confirm Password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror" 
                                       placeholder="Student ID" value="{{ old('student_id') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-id-card"></span>
                                    </div>
                                </div>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" name="course" class="form-control @error('course') is-invalid @enderror" 
                                       placeholder="Course/Program" value="{{ old('course') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-graduation-cap"></span>
                                    </div>
                                </div>
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <select name="year_level" class="form-control @error('year_level') is-invalid @enderror" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                    <option value="5th Year" {{ old('year_level') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                    <option value="Graduate" {{ old('year_level') == 'Graduate' ? 'selected' : '' }}>Graduate Student</option>
                                </select>
                                @error('year_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-phone"></i> Contact Information
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                       placeholder="Phone Number" value="{{ old('phone_number') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-phone"></span>
                                    </div>
                                </div>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <select name="civil_status" class="form-control @error('civil_status') is-invalid @enderror">
                                    <option value="">Civil Status (Optional)</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                                @error('civil_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                  placeholder="Home Address" rows="2" required>{{ old('address') }}</textarea>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-marker-alt"></span>
                            </div>
                        </div>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="form-section mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Emergency Contact
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       placeholder="Emergency Contact Name" value="{{ old('emergency_contact_name') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user-friends"></span>
                                    </div>
                                </div>
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                       placeholder="Relationship (e.g., Parent, Sibling)" value="{{ old('emergency_contact_relationship') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-heart"></span>
                                    </div>
                                </div>
                                @error('emergency_contact_relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="tel" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                       placeholder="Emergency Contact Phone" value="{{ old('emergency_contact_phone') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-phone"></span>
                                    </div>
                                </div>
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <textarea name="emergency_contact_address" class="form-control @error('emergency_contact_address') is-invalid @enderror" 
                                          placeholder="Emergency Contact Address" rows="1" required>{{ old('emergency_contact_address') }}</textarea>
                                @error('emergency_contact_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Information Section -->
                <div class="form-section mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-heartbeat"></i> Health Information
                        <small class="text-muted">(Optional)</small>
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="number" name="height" class="form-control @error('height') is-invalid @enderror" 
                                       placeholder="Height (cm)" value="{{ old('height') }}" min="50" max="250" step="0.1">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="text-sm">cm</span>
                                    </div>
                                </div>
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" 
                                       placeholder="Weight (kg)" value="{{ old('weight') }}" min="20" max="300" step="0.1">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="text-sm">kg</span>
                                    </div>
                                </div>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Privacy Section -->
                <div class="form-section mb-4">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="terms_agreement" class="form-check-input @error('terms_agreement') is-invalid @enderror" 
                               id="terms" value="1" {{ old('terms_agreement') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms and Conditions</a>
                        </label>
                        @error('terms_agreement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="privacy_agreement" class="form-check-input @error('privacy_agreement') is-invalid @enderror" 
                               id="privacy" value="1" {{ old('privacy_agreement') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="privacy">
                            I agree to the <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a>
                        </label>
                        @error('privacy_agreement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Register Account
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Already have account?
                        </a>
                    </div>
                </div>
            </form>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted">
            <small>
                <i class="fas fa-graduation-cap mr-1"></i>
                BSU Health Portal - Student Registration
            </small>
        </p>
    </div>
@stop

@section('adminlte_css')
<style>
.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.form-section:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .col-md-6, .col-md-4 {
        margin-bottom: 0.5rem;
    }
}
</style>
@stop

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            }
        });
    }
});
    
    // Add Terms and Privacy modals to the auth body section
    $('#registrationForm').after(`
        <!-- Terms Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Terms and Conditions</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h5>BSU Health Portal Terms of Use</h5>
                        <p>By registering for the BSU Health Portal, you agree to:</p>
                        <ul>
                            <li>Provide accurate and truthful information about yourself</li>
                            <li>Use this system only for legitimate health-related appointments</li>
                            <li>Respect appointment schedules and notify in advance of cancellations</li>
                            <li>Maintain confidentiality of your account credentials</li>
                            <li>Follow BSU health center policies and procedures</li>
                        </ul>
                        <p>Misuse of this system may result in account suspension and disciplinary action.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Modal -->
        <div class="modal fade" id="privacyModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Privacy Policy</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h5>Information Collection and Use</h5>
                        <p>The BSU Health Center collects and uses your information to:</p>
                        <ul>
                            <li>Provide medical and health services</li>
                            <li>Maintain accurate health records</li>
                            <li>Schedule and manage appointments</li>
                            <li>Contact you regarding health matters</li>
                            <li>Emergency contact purposes when necessary</li>
                        </ul>
                        <h5>Information Protection</h5>
                        <p>Your health information is protected under medical confidentiality laws and BSU policies. Access is restricted to authorized healthcare personnel only.</p>
                        <p>We do not share your personal health information with third parties without your explicit consent, except as required by law.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `);
});
</script>
@stop
