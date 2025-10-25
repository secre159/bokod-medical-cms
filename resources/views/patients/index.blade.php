@extends('adminlte::page')

@section('title', 'Patients - BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Patient Management</h1>
            <small class="text-muted">Manage patient records and information</small>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add New Patient
        </a>
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

    {{-- Filters and Search --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filters & Search
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('patients.index') }}" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">Search Patients</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by name, email, phone..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter">Status Filter</label>
                        <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
                            <option value="">All Active Patients</option>
                            <option value="archived" {{ request('filter') === 'archived' ? 'selected' : '' }}>
                                Archived Patients
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>
                Patients List ({{ $patients->total() }} total)
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            @if($patients->count() > 0)
                <table class="table table-bordered table-striped table-sm" id="patientsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Civil Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr class="{{ $patient->archived ? 'table-secondary' : '' }}">
                                <td>
                                    <strong>#{{ $patient->id }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            @if($patient->user)
                                                <x-user-avatar :user="$patient->user" size="thumbnail" width="32px" height="32px" class="img-circle elevation-1" />
                                            @else
                                                <div class="avatar-placeholder bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                    {{ strtoupper(substr($patient->patient_name,0,1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $patient->patient_name }}</strong>
                                            @if($patient->archived)
                                                <span class="badge badge-secondary badge-sm ml-1">Archived</span>
                                            @endif
                                            @if($patient->position)
                                                <br><small class="text-muted">{{ $patient->position }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($patient->email)
                                        <a href="mailto:{{ $patient->email }}"><x-masked-email :email="$patient->email" /></a>
                                    @else
                                        <span class="text-muted">No email</span>
                                    @endif
                                </td>
                                <td>
                                    @if($patient->phone_number)
                                        <a href="tel:{{ $patient->phone_number }}">{{ $patient->phone_number }}</a>
                                    @else
                                        <span class="text-muted">No phone</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $patient->gender === 'Male' ? 'primary' : ($patient->gender === 'Female' ? 'pink' : 'secondary') }}">
                                        {{ $patient->gender }}
                                    </span>
                                </td>
                                <td>
                                    @if($patient->age)
                                        {{ $patient->age }} years
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline-{{ $patient->civil_status === 'Single' ? 'primary' : 'info' }}">
                                        {{ $patient->civil_status }}
                                    </span>
                                </td>
                                <td>
                                    @if($patient->archived)
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-archive mr-1"></i> Archived
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i> Active
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" 
                                           class="btn btn-sm btn-primary" title="Edit Patient">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('patients.destroy', $patient) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('{{ $patient->archived ? 'Restore' : 'Archive' }} this patient?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-{{ $patient->archived ? 'success' : 'warning' }}" 
                                                    title="{{ $patient->archived ? 'Restore' : 'Archive' }} Patient">
                                                <i class="fas fa-{{ $patient->archived ? 'undo' : 'archive' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Patients Found</h4>
                    <p class="text-muted">
                        @if(request('search') || request('filter'))
                            No patients match your current filters.
                        @else
                            Start by adding your first patient to the system.
                        @endif
                    </p>
                    @if(!request('search') && !request('filter'))
                        <a href="{{ route('patients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add First Patient
                        </a>
                    @endif
                </div>
            @endif
        </div>
        
        @if($patients->hasPages())
            <div class="card-footer clearfix">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

    {{-- Statistics Summary --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $patients->total() }}</h3>
                    <p>Total Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Patient::active()->count() }}</h3>
                    <p>Active Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Patient::archived()->count() }}</h3>
                    <p>Archived Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-archive"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\Patient::whereNotNull('date_of_birth')->where('date_of_birth', '<', now()->subYears(60))->count() }}</h3>
                    <p>Senior Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .user-block .username {
            margin-top: 0;
            font-weight: 600;
        }
        .user-block .description {
            font-size: 12px;
            color: #6c757d;
        }
        .table-secondary {
            opacity: 0.7;
        }
        .btn-group .btn { margin-right: 2px; }

        /* Compact table and sticky header */
        .table th { font-weight:600; background:#f8f9fa; white-space:nowrap; }
        .table td { white-space:nowrap; }
        #patientsTable thead th { position: sticky; top: 0; z-index: 3; background: #f8f9fa; }

        /* Column resize grips (colResizable) */
        .JCLRgrips { height: 0; position: relative; }
        .JCLRgrip { position: absolute; z-index: 5; }
        .JCLRgrip .JColResizer { position: absolute; background: transparent; width: 8px; margin-left: -4px; cursor: col-resize; height: 100vh; top: 0; }
        .dragging .JColResizer { border-left: 2px dashed #007bff; }

        .avatar-placeholder { font-weight:600; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/colresizable/colResizable-1.6.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);

            // DataTable for sorting only (Laravel handles pagination)
            @if($patients->count() > 0)
            $('#patientsTable').DataTable({
                paging: false,
                lengthChange: false,
                searching: false,
                ordering: true,
                info: false,
                autoWidth: false,
                responsive: true,
                order: [[0, 'desc']],
                columnDefs: [ { orderable: false, targets: [8] } ]
            });

            // Enable drag-to-resize columns (header + body stay in sync)
            $('#patientsTable').colResizable({
                liveDrag: true,
                resizeMode: 'overflow',
                draggingClass: 'dragging',
                minWidth: 60,
            });
            @endif
        });
    </script>
@stop
