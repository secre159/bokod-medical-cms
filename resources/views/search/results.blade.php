@extends('adminlte::page')

@section('title', 'Search Results | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-search mr-2"></i>Search Results
                @if($results['total'] > 0)
                    <small class="text-muted">{{ $results['total'] }} results found for "{{ $query }}"</small>
                @endif
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Search Results</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    {{-- Search Summary --}}
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Search Query: <strong>"{{ $query }}"</strong>
                    </h5>
                </div>
                <div class="col-md-6 text-right">
                    @if($results['total'] > 0)
                        <span class="badge badge-success badge-lg">{{ $results['total'] }} Results Found</span>
                    @else
                        <span class="badge badge-warning badge-lg">No Results Found</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($results['total'] == 0)
        {{-- No Results --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No results found</h4>
                <p class="text-muted">
                    We couldn't find anything matching "{{ $query }}". Try different keywords or check your spelling.
                </p>
                <div class="mt-4">
                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary">
                        <i class="fas fa-home mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Results by Category --}}
        <div class="row">
            
            {{-- Patients Results (Admin Only) --}}
            @if(auth()->user()->role === 'admin' && isset($results['patients']) && $results['patients']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-injured text-primary mr-2"></i>
                            Patients ({{ $results['patients']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['patients'] as $patient)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-user-injured text-primary fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $patient->patient_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope mr-1"></i>{{ $patient->email }}
                                                @if($patient->phone_number)
                                                    | <i class="fas fa-phone mr-1"></i>{{ $patient->phone_number }}
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Appointments Results --}}
            @if(isset($results['appointments']) && $results['appointments']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check text-success mr-2"></i>
                            Appointments ({{ $results['appointments']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['appointments'] as $appointment)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-calendar-check text-success fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>
                                                @if(auth()->user()->role === 'admin')
                                                    {{ $appointment->patient->patient_name ?? 'Unknown Patient' }}
                                                @else
                                                    My Appointment
                                                @endif
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>{{ $appointment->appointment_date->format('M d, Y') }}
                                                <i class="fas fa-clock ml-2 mr-1"></i>{{ $appointment->appointment_time->format('h:i A') }}
                                            </small>
                                            <br>
                                            <small>{{ Str::limit($appointment->reason, 60) }}</small>
                                        </td>
                                        <td class="text-right">
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                            @else
                                                <span class="badge badge-info">{{ ucfirst($appointment->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Medicines Results (Admin Only) --}}
            @if(auth()->user()->role === 'admin' && isset($results['medicines']) && $results['medicines']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pills text-warning mr-2"></i>
                            Medicines ({{ $results['medicines']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['medicines'] as $medicine)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-pills text-warning fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $medicine->name }}</strong>
                                            @if($medicine->brand_name && $medicine->brand_name != $medicine->name)
                                                <span class="text-muted">({{ $medicine->brand_name }})</span>
                                            @endif
                                            <br>
                                            @if($medicine->generic_name)
                                                <small class="text-muted">Generic: {{ $medicine->generic_name }}</small><br>
                                            @endif
                                            <small>{{ Str::limit($medicine->description, 60) }}</small>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-{{ $medicine->stock_quantity > 10 ? 'success' : 'warning' }}">
                                                Stock: {{ $medicine->stock_quantity }}
                                            </span>
                                            <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-sm btn-warning ml-2">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Prescriptions Results --}}
            @if(isset($results['prescriptions']) && $results['prescriptions']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-prescription-bottle-alt text-info mr-2"></i>
                            Prescriptions ({{ $results['prescriptions']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['prescriptions'] as $prescription)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-prescription-bottle-alt text-info fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $prescription->medication_name }}</strong>
                                            <br>
                                            @if(auth()->user()->role === 'admin' && $prescription->patient)
                                                <small class="text-muted">Patient: {{ $prescription->patient->patient_name }}</small><br>
                                            @endif
                                            <small class="text-muted">
                                                Dosage: {{ $prescription->dosage }}
                                                @if($prescription->frequency)
                                                    | Frequency: {{ $prescription->frequency }}
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-{{ $prescription->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($prescription->status) }}
                                            </span>
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-sm btn-info ml-2">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Users Results (Admin Only) --}}
            @if(auth()->user()->role === 'admin' && isset($results['users']) && $results['users']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users text-secondary mr-2"></i>
                            Users ({{ $results['users']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['users'] as $user)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-user text-secondary fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                            <br>
                                            <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Messages Results --}}
            @if(isset($results['messages']) && $results['messages']->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comments text-primary mr-2"></i>
                            Messages ({{ $results['messages']->count() }})
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($results['messages'] as $conversation)
                                    <tr>
                                        <td width="5%">
                                            <i class="fas fa-comments text-primary fa-lg"></i>
                                        </td>
                                        <td>
                                            <strong>
                                                @if(auth()->user()->role === 'admin')
                                                    Chat with {{ $conversation->patient->name ?? 'Unknown Patient' }}
                                                @else
                                                    Chat with {{ $conversation->admin->name ?? 'Medical Staff' }}
                                                @endif
                                            </strong>
                                            @if($conversation->latestMessage)
                                                <br>
                                                <small class="text-muted">
                                                    Latest: {{ Str::limit($conversation->latestMessage->message, 60) }}
                                                </small>
                                                <br>
                                                <small class="text-muted">{{ $conversation->latestMessage->created_at->diffForHumans() }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('admin.messages.index', ['conversation' => $conversation->id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-comments mr-1"></i>Open Chat
                                                </a>
                                            @else
                                                <a href="{{ route('patient.messages.index', ['conversation' => $conversation->id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-comments mr-1"></i>Open Chat
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    @endif
@endsection

@section('css')
<style>
    .badge-lg {
        font-size: 0.9em;
        padding: 0.5rem 0.75rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .card-header {
        background-color: #f8f9fa;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
@endsection