<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }
        if ($request->filled('user')) {
            $query->where('causer_id', $request->integer('user'));
        }
        if ($request->filled('model')) {
            $query->where('subject_type', $request->string('model'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function($qq) use ($q) {
                $qq->where('path', 'like', "%$q%")
                   ->orWhere('ip', 'like', "%$q%")
                   ->orWhere('user_agent', 'like', "%$q%")
                   ->orWhereJsonContains('properties->attributes->patient_name', $q);
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id','name']);

        return view('admin.logs.index', compact('logs', 'users'));
    }
}
