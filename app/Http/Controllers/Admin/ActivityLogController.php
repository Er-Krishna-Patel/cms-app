<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends \App\Http\Controllers\Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->query('user'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->query('action'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->query('subject_type'));
        }

        $logs = $query->paginate(50);
        $users = \App\Models\User::select('id', 'name')->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $subjects = ActivityLog::distinct()->pluck('subject_type');

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions', 'subjects'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
