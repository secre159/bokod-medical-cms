@extends('adminlte::page')

@section('title', 'Courses | BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Courses</h1>
            <small class="text-muted">Manage academic courses and departments</small>
        </div>
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Course
        </a>
    </div>
@stop

@section('content')
    @include('components.modal-alerts')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Course List</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40%">Course Name</th>
                        <th style="width: 20%">Code</th>
                        <th style="width: 25%">Department</th>
                        <th style="width: 15%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->code ?: '—' }}</td>
                            <td>{{ $course->department?->name ?: '—' }}</td>
                            <td class="text-center">
                                @if($course->active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted p-4">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())
            <div class="card-footer">
                {{ $courses->links('custom.simple-pagination') }}
            </div>
        @endif
    </div>
@stop
