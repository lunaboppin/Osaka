<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filter by model type
        if ($request->filled('model')) {
            $modelClass = 'App\\Models\\' . $request->input('model');
            $query->where('auditable_type', $modelClass);
        }

        // Filter by user
        if ($request->filled('user')) {
            $query->where('user_id', $request->input('user'));
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->input('search') . '%');
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get available filter options
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = AuditLog::distinct()
            ->whereNotNull('auditable_type')
            ->pluck('auditable_type')
            ->map(fn ($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.audit-log.index', compact('logs', 'actions', 'modelTypes', 'users'));
    }
}
