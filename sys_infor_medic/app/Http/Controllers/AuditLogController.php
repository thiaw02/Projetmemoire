<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()->orderByDesc('created_at');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qq) use ($q){
                $qq->where('action','like',"%$q%")
                   ->orWhere('auditable_type','like',"%$q%");
            });
        }
        if ($request->filled('user_id') && is_numeric($request->user_id)) {
            $query->where('user_id', (int)$request->user_id);
        }
        $logs = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get(['id','name']);
        return view('admin.audit_logs.index', compact('logs','users'));
    }
}
