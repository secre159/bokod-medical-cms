@extends('adminlte::page')

@section('title', 'Registration Details - BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Registration Details</h1>
        <a href="{{ route('registrations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Registrations
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Patient Information Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-circle"></i> 
                    Patient Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ 
                        $user->registration_status === 'pending' ? 'warning' : 
                        ($user->registration_status === 'approved' ? 'success' : 'danger') 
                    }}">
                        {{ ucfirst($user->registration_status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8">{{ $user->name }}</dd>
                            
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">{{ $user->email }}</dd>
                            
                            <dt class="col-sm-4">Student ID:</dt>
                            <dd class="col-sm-8">{{ $user->patient->position ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Course:</dt>
                            <dd class="col-sm-8">{{ $user->patient->course ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Year Level:</dt>
                            <dd class="col-sm-8">N/A</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Date of Birth:</dt>
                            <dd class="col-sm-8">
                                @if($user->patient && $user->patient->date_of_birth)
                                    {{ \Carbon\Carbon::parse($user->patient->date_of_birth)->format('M d, Y') }}
                                    ({{ \Carbon\Carbon::parse($user->patient->date_of_birth)->age }} years old)
                                @else
                                    N/A
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Gender:</dt>
                            <dd class="col-sm-8">{{ $user->patient->gender ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">{{ $user->patient->phone_number ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Civil Status:</dt>
                            <dd class="col-sm-8">{{ $user->patient->civil_status ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <dt>Address:</dt>
                        <dd>{{ $user->patient->address ?? 'N/A' }}</dd>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Emergency Contact Card -->
        @if($user->patient && ($user->patient->emergency_contact_name || $user->patient->emergency_contact))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-phone"></i> 
                    Emergency Contact Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8">{{ $user->patient->emergency_contact_name ?? $user->patient->emergency_contact ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Relationship:</dt>
                            <dd class="col-sm-8">{{ $user->patient->emergency_contact_relationship ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">{{ $user->patient->emergency_contact_phone ?? $user->patient->emergency_phone ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
                
                @if($user->patient->emergency_contact_address)
                <div class="row">
                    <div class="col-12">
                        <dt>Address:</dt>
                        <dd>{{ $user->patient->emergency_contact_address }}</dd>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Health Information Card -->
        @if($user->patient && ($user->patient->height || $user->patient->weight || $user->patient->medical_history))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat"></i> 
                    Health Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <dt>Height:</dt>
                        <dd>{{ $user->patient->height ? $user->patient->height . ' cm' : 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>Weight:</dt>
                        <dd>{{ $user->patient->weight ? $user->patient->weight . ' kg' : 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4">
                        <dt>BMI:</dt>
                        <dd>{{ $user->patient->bmi ?? 'N/A' }}</dd>
                    </div>
                </div>
                
                @if($user->patient->medical_history)
                <div class="row mt-3">
                    <div class="col-12">
                        <dt>Medical History:</dt>
                        <dd>{{ $user->patient->medical_history }}</dd>
                    </div>
                </div>
                @endif
                
                @if($user->patient->allergies)
                <div class="row">
                    <div class="col-12">
                        <dt>Allergies:</dt>
                        <dd>{{ $user->patient->allergies }}</dd>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Registration Status Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-check"></i> 
                    Registration Status
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Status:</dt>
                    <dd class="col-sm-7">
                        <span class="badge badge-{{ 
                            $user->registration_status === 'pending' ? 'warning' : 
                            ($user->registration_status === 'approved' ? 'success' : 'danger') 
                        }}">
                            {{ ucfirst($user->registration_status) }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-5">Submitted:</dt>
                    <dd class="col-sm-7">{{ $user->created_at->format('M d, Y g:i A') }}</dd>
                    
                    @if($user->approved_at)
                    <dt class="col-sm-5">{{ $user->registration_status === 'approved' ? 'Approved' : 'Processed' }}:</dt>
                    <dd class="col-sm-7">{{ $user->approved_at->format('M d, Y g:i A') }}</dd>
                    @endif
                    
                    @if($user->approved_by)
                    <dt class="col-sm-5">By:</dt>
                    <dd class="col-sm-7">{{ $user->approvedBy->name ?? 'System' }}</dd>
                    @endif
                </dl>
                
                @if($user->rejection_reason)
                <div class="alert alert-danger">
                    <strong>Rejection Reason:</strong><br>
                    {{ $user->rejection_reason }}
                </div>
                @endif
            </div>
        </div>
        
        <!-- Actions Card -->
        @if($user->registration_status === 'pending')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i> 
                    Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Approve Button -->
                    <form action="{{ route('registrations.approve', $user) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block" 
                                onclick="return confirm('Are you sure you want to approve this registration?')">
                            <i class="fas fa-check"></i> Approve Registration
                        </button>
                    </form>
                    
                    <!-- Reject Button -->
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times"></i> Reject Registration
                    </button>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Account Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-cog"></i> 
                    Account Information
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">User ID:</dt>
                    <dd class="col-sm-7">#{{ $user->id }}</dd>
                    
                    <dt class="col-sm-5">Role:</dt>
                    <dd class="col-sm-7">{{ ucfirst($user->role) }}</dd>
                    
                    <dt class="col-sm-5">Source:</dt>
                    <dd class="col-sm-7">{{ ucfirst($user->registration_source) }} Registration</dd>
                    
                    <dt class="col-sm-5">Account Status:</dt>
                    <dd class="col-sm-7">
                        <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-5">Email Verified:</dt>
                    <dd class="col-sm-7">
                        @if($user->email_verified_at)
                            <span class="badge badge-success">Verified</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('registrations.reject', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection *</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Please provide a clear reason for rejecting this registration..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-title {
        font-weight: 600;
    }
    dt {
        font-weight: 600;
        color: #495057;
    }
    .badge {
        font-size: 0.9em;
    }
    .alert {
        margin-bottom: 0;
    }
    .d-grid .btn {
        margin-bottom: 10px;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-focus on rejection reason when modal opens
    $('#rejectModal').on('shown.bs.modal', function () {
        $('#rejection_reason').focus();
    });
});
</script>
@stop