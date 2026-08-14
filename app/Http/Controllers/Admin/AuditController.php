<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Display the audit log with filtering.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->paginate(30)->withQueryString();

        $actionTypes = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit.index', compact('logs', 'actionTypes'));
    }
}
