@extends('adminlte::page')

@section('title', 'Activity Logs')

@section('content_header')
    <h1>Activity Logs</h1>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <form method="GET" class="form-inline mb-3">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Search path/IP/UA">
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control mr-2">
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control mr-2">
      <select name="action" class="form-control mr-2">
        <option value="">Action</option>
        @foreach(['created','updated','deleted','login','logout','backup','restore'] as $a)
          <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>{{ ucfirst($a) }}</option>
        @endforeach
      </select>
      <select name="user" class="form-control mr-2">
        <option value="">User</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" {{ request('user')==$u->id?'selected':'' }}>{{ $u->name }}</option>
        @endforeach
      </select>
      <button class="btn btn-primary">Filter</button>
    </form>

    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Date</th>
            <th>User</th>
            <th>Action</th>
            <th>Subject</th>
            <th>IP</th>
            <th>Path</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->created_at }}</td>
            <td>{{ optional(\App\Models\User::find($log->causer_id))->name ?? 'System' }}</td>
            <td><span class="badge badge-info">{{ $log->action }}</span></td>
            <td>{{ class_basename($log->subject_type) }}#{{ $log->subject_id }}</td>
            <td>{{ $log->ip }}</td>
            <td>{{ $log->path }}</td>
            <td>
              @if($log->properties)
                <pre class="mb-0" style="white-space:pre-wrap;max-width:480px;">{{ json_encode($log->properties, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted">No activity yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{ $logs->links('pagination::bootstrap-4') }}
  </div>
</div>
@endsection