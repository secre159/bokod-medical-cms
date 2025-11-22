@extends('adminlte::page')

@section('title', 'Verify PIN')

@section('content_header')
    <h1><i class="fas fa-lock"></i> Settings Access Verification</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shield-alt"></i> Enter Your PIN</h3>
                </div>
                
                <form action="{{ route('settings.verify-pin.submit') }}" method="POST" id="pinForm">
                    @csrf
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-lock fa-4x text-warning"></i>
                            <h4 class="mt-3">Settings Access Required</h4>
                            <p class="text-muted">Enter your 4-6 digit PIN to access System Settings</p>
                        </div>
                        
                        <div class="form-group">
                            <input type="password" 
                                   class="form-control form-control-lg text-center @error('pin') is-invalid @enderror" 
                                   id="pin" 
                                   name="pin" 
                                   maxlength="6" 
                                   pattern="[0-9]{4,6}" 
                                   placeholder="Enter PIN" 
                                   autofocus 
                                   required
                                   style="font-size: 2rem; letter-spacing: 10px;">
                            @error('pin')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <small>Your session will be valid for 30 minutes after verification</small>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning btn-lg btn-block">
                            <i class="fas fa-unlock"></i> Verify & Access Settings
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-question-circle"></i> 
                    Forgot your PIN? Contact the system administrator or reset via database.
                </small>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit when 6 digits entered
document.getElementById('pin').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        setTimeout(() => {
            document.getElementById('pinForm').submit();
        }, 300);
    }
});
</script>
@stop
