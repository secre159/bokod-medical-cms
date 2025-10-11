@extends('adminlte::page')

@section('title', 'User Details | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <!-- User Info Card -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <x-user-avatar :user="$user" class="profile-user-img img-fluid img-circle" width="150px" height="150px" />
                    </div>

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>

                    <p class="text-muted text-center">
                        <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Status</b> 
                            <span class="float-right">
                                <span class="badge badge-{{ $user->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right">{{ $user->email }}</span>
                        </li>
                        @if($user->phone)
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right">{{ $user->phone }}</span>
                        </li>
                        @endif
                        <li class="list-group-item">
                            <b>Joined</b> <span class="float-right">{{ $user->created_at->format('M d, Y') }}</span>
                        </li>
                        @if($user->last_login_at)
                        <li class="list-group-item">
                            <b>Last Login</b> <span class="float-right">{{ $user->last_login_at->diffForHumans() }}</span>
                        </li>
                        @endif
                    </ul>

                    <div class="text-center">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-default btn-sm ml-2">
                            <i class="fas fa-list"></i> All Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    @if($user->role == 'patient')
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4>{{ $stats['appointments_count'] }}</h4>
                                <small>Appointments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4>{{ $stats['prescriptions_count'] }}</h4>
                                <small>Prescriptions</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                            <div class="mt-3">
                                <small class="text-muted">
                                    Account age: {{ now()->startOfDay()->diffInDays($user->created_at->startOfDay()) }} days
                                </small>
                            </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">Details</a></li>
                        @if($user->role == 'patient')
                        <li class="nav-item"><a class="nav-link" href="#medical" data-toggle="tab">Medical Info</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Details Tab -->
                        <div class="active tab-pane" id="details">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Full Name</strong>
                                    <p class="text-muted">{{ $user->name }}</p>
                                    <hr>

                                    <strong>Email Address</strong>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    <hr>

                                    @if($user->phone)
                                    <strong>Phone Number</strong>
                                    <p class="text-muted">{{ $user->phone }}</p>
                                    <hr>
                                    @endif

                                    @if($user->date_of_birth)
                                    <strong>Date of Birth</strong>
                                    <p class="text-muted">{{ $user->date_of_birth->format('M d, Y') }} ({{ $user->date_of_birth->age }} years old)</p>
                                    <hr>
                                    @endif

                                    @if($user->gender)
                                    <strong>Gender</strong>
                                    <p class="text-muted">{{ ucfirst($user->gender) }}</p>
                                    <hr>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <strong>Role</strong>
                                    <p class="text-muted">
                                        <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </p>
                                    <hr>

                                    <strong>Status</strong>
                                    <p class="text-muted">
                                        <span class="badge badge-{{ $user->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </p>
                                    <hr>

                                    @if($user->address)
                                    <strong>Address</strong>
                                    <p class="text-muted">{{ $user->address }}</p>
                                    <hr>
                                    @endif

                                    <strong>Account Created</strong>
                                    <p class="text-muted">{{ $user->created_at->format('M d, Y g:i A') }}</p>

                                    @if($user->updated_at != $user->created_at)
                                    <hr>
                                    <strong>Last Updated</strong>
                                    <p class="text-muted">{{ $user->updated_at->format('M d, Y g:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Medical Info Tab (for patients) -->
                        @if($user->role == 'patient')
                        <div class="tab-pane" id="medical">
                            @if($user->emergency_contact || $user->emergency_phone)
                            <h5><i class="fas fa-phone-alt text-danger"></i> Emergency Contact</h5>
                            @if($user->emergency_contact)
                            <p><strong>Name:</strong> {{ $user->emergency_contact }}</p>
                            @endif
                            @if($user->emergency_phone)
                            <p><strong>Phone:</strong> {{ $user->emergency_phone }}</p>
                            @endif
                            <hr>
                            @endif

                            @if($user->medical_history)
                            <h5><i class="fas fa-notes-medical text-info"></i> Medical History</h5>
                            <p class="text-muted">{{ $user->medical_history }}</p>
                            <hr>
                            @endif

                            @if($user->allergies)
                            <h5><i class="fas fa-exclamation-triangle text-warning"></i> Allergies</h5>
                            <p class="text-muted">{{ $user->allergies }}</p>
                            <hr>
                            @endif

                            @if($user->notes)
                            <h5><i class="fas fa-sticky-note text-secondary"></i> Additional Notes</h5>
                            <p class="text-muted">{{ $user->notes }}</p>
                            @endif

                            @if(!$user->emergency_contact && !$user->emergency_phone && !$user->medical_history && !$user->allergies && !$user->notes)
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No medical information available.</p>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Medical Info
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Activity Tab -->
                        <div class="tab-pane" id="activity">
                            <div class="timeline timeline-inverse">
                                <div class="time-label">
                                    <span class="bg-success">{{ $user->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-user-plus bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ $user->created_at->format('g:i A') }}</span>
                                        <h3 class="timeline-header">Account Created</h3>
                                        <div class="timeline-body">
                                            User account was created with {{ $user->role }} role.
                                        </div>
                                    </div>
                                </div>


                                @if($user->last_login_at)
                                <div>
                                    <i class="fas fa-sign-in-alt bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ $user->last_login_at->format('g:i A') }}</span>
                                        <h3 class="timeline-header">Last Login</h3>
                                        <div class="timeline-body">
                                            User last logged into the system {{ $user->last_login_at->diffForHumans() }}.
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<style>
    .profile-user-img {
        width: 100px;
        height: 100px;
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
    }
    .box-profile {
        padding: 20px;
    }
    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #adb5bd;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('User show page loaded for:', '{{ $user->name }}');
});
</script>
@endsection