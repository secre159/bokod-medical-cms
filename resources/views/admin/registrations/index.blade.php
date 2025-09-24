@extends('adminlte::page')

@section('title', 'Registration Approvals - BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Student Registration Approvals</h1>
            <small class="text-muted">Review and approve student self-registrations</small>
        </div>
        <div>
            @if($counts['pending'] > 0)
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#bulkApproveModal">
                    <i class="fas fa-check-double mr-1"></i> Bulk Approve
                </button>
            @endif
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Status Tabs --}}
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" 
                       href="{{ route('registrations.index', ['status' => 'pending']) }}">
                        <i class="fas fa-clock mr-1"></i>
                        Pending
                        @if($counts['pending'] > 0)
                            <span class="badge badge-warning ml-1">{{ $counts['pending'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" 
                       href="{{ route('registrations.index', ['status' => 'approved']) }}">
                        <i class="fas fa-check mr-1"></i>
                        Approved
                        @if($counts['approved'] > 0)
                            <span class="badge badge-success ml-1">{{ $counts['approved'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" 
                       href="{{ route('registrations.index', ['status' => 'rejected']) }}">
                        <i class="fas fa-times mr-1"></i>
                        Rejected
                        @if($counts['rejected'] > 0)
                            <span class="badge badge-danger ml-1">{{ $counts['rejected'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('registrations.index', ['status' => 'all']) }}">
                        <i class="fas fa-list mr-1"></i>
                        All Registrations
                        <span class="badge badge-info ml-1">{{ $counts['all'] }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-2"></i>
                Search & Filter
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('registrations.index') }}" class="row">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="search">Search Registrations</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by name, email, student ID, course..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <a href="{{ route('registrations.index', ['status' => $status]) }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Clear Search
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Registrations Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-graduate mr-2"></i>
                Student Registrations 
                @if($status !== 'all')
                    ({{ ucfirst($status) }} - {{ $registrations->total() }})
                @else
                    (All - {{ $registrations->total() }})
                @endif
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            @if($registrations->count() > 0)
                <form id="bulkActionForm">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                @if($status === 'pending')
                                    <th width="30">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="selectAll">
                                            <label for="selectAll"></label>
                                        </div>
                                    </th>
                                @endif
                                <th>Student Info</th>
                                <th>Contact Details</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr class="{{ $registration->registration_status === 'rejected' ? 'table-danger' : ($registration->registration_status === 'approved' ? 'table-success' : '') }}">
                                    @if($status === 'pending')
                                        <td>
                                            @if($registration->registration_status === 'pending')
                                                <div class="icheck-primary">
                                                    <input type="checkbox" class="registration-checkbox" 
                                                           value="{{ $registration->id }}" id="check{{ $registration->id }}">
                                                    <label for="check{{ $registration->id }}"></label>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <div class="user-block">
                                            <div class="username">
                                                <strong>{{ $registration->name }}</strong>
                                                @if($registration->patient && $registration->patient->position)
                                                    <span class="badge badge-primary badge-sm ml-1">{{ $registration->patient->position }}</span>
                                                @endif
                                            </div>
                                            @if($registration->patient)
                                                <div class="description">
                                                    @if($registration->patient->course)
                                                        <div><i class="fas fa-graduation-cap mr-1"></i>{{ $registration->patient->course }}</div>
                                                    @endif
                                                    <div class="d-flex flex-wrap">
                                                        @if($registration->patient->gender)
                                                            <span class="mr-3"><i class="fas fa-user mr-1"></i>{{ ucfirst($registration->patient->gender) }}</span>
                                                        @endif
                                                        @if($registration->patient->date_of_birth)
                                                            @php
                                                                $age = \Carbon\Carbon::parse($registration->patient->date_of_birth)->age;
                                                            @endphp
                                                            <span class="mr-3"><i class="fas fa-birthday-cake mr-1"></i>{{ $age }} years</span>
                                                        @endif
                                                        @if($registration->patient->civil_status)
                                                            <span><i class="fas fa-heart mr-1"></i>{{ ucfirst($registration->patient->civil_status) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div>
                                                <i class="fas fa-envelope mr-1 text-muted"></i>
                                                <a href="mailto:{{ $registration->email }}">{{ $registration->email }}</a>
                                                @php
                                                    $domain = substr($registration->email, strpos($registration->email, '@'));
                                                    $emailProvider = '';
                                                    if (str_contains($domain, 'gmail')) $emailProvider = 'Gmail';
                                                    elseif (str_contains($domain, 'yahoo')) $emailProvider = 'Yahoo';
                                                    elseif (str_contains($domain, 'hotmail') || str_contains($domain, 'outlook')) $emailProvider = 'Outlook';
                                                    elseif (str_contains($domain, 'bsu.edu.ph')) $emailProvider = 'BSU Official';
                                                    else $emailProvider = 'Other';
                                                @endphp
                                                @if($emailProvider)
                                                    <span class="badge badge-secondary badge-sm ml-1">{{ $emailProvider }}</span>
                                                @endif
                                            </div>
                                            @if($registration->patient && $registration->patient->phone_number)
                                                <div class="mt-1">
                                                    <i class="fas fa-phone mr-1 text-muted"></i>
                                                    <a href="tel:{{ $registration->patient->phone_number }}">{{ $registration->patient->phone_number }}</a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div>{{ $registration->created_at->format('M d, Y') }}</div>
                                            <div class="text-muted">{{ $registration->created_at->format('h:i A') }}</div>
                                            <div class="text-muted">{{ $registration->created_at->diffForHumans() }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($registration->registration_status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock mr-1"></i> Pending Review
                                            </span>
                                        @elseif($registration->registration_status === 'approved')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i> Approved
                                            </span>
                                            @if($registration->approved_at)
                                                <div class="text-xs text-muted mt-1">
                                                    {{ $registration->approved_at->format('M d, Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times mr-1"></i> Rejected
                                            </span>
                                            @if($registration->rejection_reason)
                                                <div class="text-xs text-muted mt-1" title="{{ $registration->rejection_reason }}">
                                                    Reason: {{ Str::limit($registration->rejection_reason, 30) }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('registrations.show', $registration) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($registration->registration_status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="approveRegistration({{ $registration->id }}, '{{ $registration->name }}')" 
                                                        title="Approve Registration">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-toggle="modal" data-target="#rejectModal{{ $registration->id }}" 
                                                        title="Reject Registration">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Reject Modal for each registration --}}
                                @if($registration->registration_status === 'pending')
                                    <div class="modal fade" id="rejectModal{{ $registration->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('registrations.reject', $registration) }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Reject Registration</h4>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject the registration for <strong>{{ $registration->name }}</strong>?</p>
                                                        <div class="form-group">
                                                            <label for="rejection_reason{{ $registration->id }}">Reason for Rejection <span class="text-danger">*</span></label>
                                                            <textarea name="rejection_reason" id="rejection_reason{{ $registration->id }}" 
                                                                      class="form-control" rows="3" required
                                                                      placeholder="Please provide a reason for rejection..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-times mr-1"></i> Reject Registration
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </form>

                {{-- Pagination --}}
                <div class="card-footer">
                    {{ $registrations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center p-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No registrations found</h4>
                    <p class="text-muted">
                        @if($status === 'pending')
                            No pending registrations at this time.
                        @else
                            No {{ $status }} registrations found.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@stop

{{-- Bulk Approve Modal --}}
<div class="modal fade" id="bulkApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bulk Approve Registrations</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve all selected registrations?</p>
                <p id="selectedCount" class="text-muted">No registrations selected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="bulkApprove()">
                    <i class="fas fa-check-double mr-1"></i> Approve Selected
                </button>
            </div>
        </div>
    </div>
</div>

@section('adminlte_js')
<script>
function approveRegistration(userId, userName) {
    if (confirm(`Are you sure you want to approve the registration for ${userName}?`)) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/registrations/${userId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('.registration-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (userIds.length === 0) {
        alert('Please select at least one registration to approve.');
        return;
    }
    
    // Create and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("registrations.bulkApprove") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    userIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Handle select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.registration-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Handle individual checkboxes
document.querySelectorAll('.registration-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.registration-checkbox:checked');
    const count = checkedBoxes.length;
    const selectedCountEl = document.getElementById('selectedCount');
    
    if (count === 0) {
        selectedCountEl.textContent = 'No registrations selected.';
    } else {
        selectedCountEl.textContent = `${count} registration(s) selected for approval.`;
    }
}
</script>
@stop