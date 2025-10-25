@extends('adminlte::page')

@section('title', 'Add Course | BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Add New Course</h1>
            <small class="text-muted">Create a course and assign it to a department</small>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Courses
        </a>
    </div>
@stop

@section('content')
    @include('components.modal-alerts')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus mr-2"></i>New Course</h3>
        </div>
        <form method="POST" action="{{ route('courses.store') }}">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Course Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="code">Course Code</label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g., BSIT">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="department_id">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                <option value="">Select department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Course
                </button>
            </div>
        </form>
    </div>
@stop
