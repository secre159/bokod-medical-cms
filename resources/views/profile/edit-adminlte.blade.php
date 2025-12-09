@extends('adminlte::page')

@section('title', 'My Profile')

@section('content_header')
    <h1><i class="fas fa-user mr-2"></i>My Profile</h1>
@stop

@section('content')
    {{-- Profile Information Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>Profile Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
                
                @if (session('status') === 'profile-updated')
                    <span class="text-success ml-2">
                        <i class="fas fa-check-circle"></i> Saved!
                    </span>
                @endif
            </form>
        </div>
    </div>

    {{-- Password Update Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-lock mr-2"></i>Update Password</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="current_password">Current Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password', 'updatePassword')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password', 'updatePassword')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key mr-1"></i> Update Password
                </button>
                
                @if (session('status') === 'password-updated')
                    <span class="text-success ml-2">
                        <i class="fas fa-check-circle"></i> Password updated!
                    </span>
                @endif
            </form>
        </div>
    </div>

    {{-- Account Information --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Account Information</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Role:</strong> <span class="badge badge-info">{{ ucfirst($user->role) }}</span></p>
                    <p><strong>Status:</strong> <span class="badge badge-success">{{ ucfirst($user->status) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Account Created:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    <p><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .card {
        margin-bottom: 20px;
    }
</style>
@stop
