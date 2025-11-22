@extends('adminlte::page')

@section('title', 'Settings PIN Setup')

@section('content_header')
    <h1><i class="fas fa-lock"></i> Settings PIN Setup</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> 
                        {{ $has_pin ? 'Update Settings PIN' : 'Create Settings PIN' }}
                    </h3>
                </div>
                
                <form action="{{ route('settings.pin.save') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if($has_pin)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You already have a PIN set. Enter your current PIN to update it.
                        </div>
                        
                        <div class="form-group">
                            <label for="current_pin">Current PIN</label>
                            <input type="password" class="form-control @error('current_pin') is-invalid @enderror" 
                                   id="current_pin" name="current_pin" maxlength="6" pattern="[0-9]{4,6}" 
                                   placeholder="Enter current PIN" required>
                            @error('current_pin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>First Time Setup:</strong> Create a 4-6 digit PIN to secure Settings access.
                        </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="pin">New PIN (4-6 digits)</label>
                            <input type="password" class="form-control @error('pin') is-invalid @enderror" 
                                   id="pin" name="pin" maxlength="6" pattern="[0-9]{4,6}" 
                                   placeholder="Enter 4-6 digit PIN" required>
                            <small class="form-text text-muted">Choose a secure PIN you'll remember</small>
                            @error('pin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="pin_confirmation">Confirm New PIN</label>
                            <input type="password" class="form-control" id="pin_confirmation" 
                                   name="pin_confirmation" maxlength="6" pattern="[0-9]{4,6}" 
                                   placeholder="Re-enter PIN" required>
                        </div>
                        
                        @if($has_pin)
                        <hr>
                        <h5><i class="fas fa-trash-alt text-danger"></i> Remove PIN</h5>
                        <p class="text-muted">Want to disable PIN protection?</p>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#removePinModal">
                            <i class="fas fa-trash"></i> Remove PIN
                        </button>
                        @endif
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $has_pin ? 'Update PIN' : 'Create PIN' }}
                        </button>
                        @if($has_pin)
                        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($has_pin)
<!-- Remove PIN Modal -->
<div class="modal fade" id="removePinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.pin.remove') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Remove PIN</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Enter your account password to remove PIN protection:</p>
                    <div class="form-group">
                        <label>Account Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> After removal, Settings will be accessible without PIN.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Remove PIN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@stop
