<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::all();

        // Historique simple : simuler, ou récupérer la table sessions si existante
        $history = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('users.name as user', DB::raw('FROM_UNIXTIME(sessions.last_activity) as datetime'), 'sessions.ip_address as ip')
            ->orderBy('sessions.last_activity', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return view('admin.dashboard', compact('users', 'history'));
    }
}
