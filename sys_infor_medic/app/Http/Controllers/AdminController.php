<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Affichage du dashboard
    public function dashboard()
    {
        $users = User::all();

        // Historique simple (si table sessions existe)
        $history = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('users.name as user', DB::raw('FROM_UNIXTIME(sessions.last_activity) as datetime'), 'sessions.ip_address as ip')
            ->orderBy('sessions.last_activity', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return view('admin.dashboard', compact('users', 'history'));
    }

    // Affichage du formulaire d'ajout
    public function createUser()
    {
        return view('admin.users.create');
    }

    // Stockage du nouvel utilisateur
    public function storeUser(Request $request)
    {
        // Validation
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:Homme,Femme',
            'role' => 'required|in:admin,medecin,infirmier,secretaire',
            'specialite' => 'nullable|string|max:255',
        ]);

        // Génération d'un mot de passe aléatoire
        $password = Str::random(8);

        // Création de l'utilisateur
        $user = User::create([
            'name' => $request->nom . ' ' . $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($password),
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'date_naissance' => $request->date_naissance,
            'sexe' => $request->sexe,
            'role' => $request->role,
            'specialite' => $request->specialite,
        ]);

        // Redirection vers la page de confirmation
        return view('admin.users.confirmation', compact('user', 'password'));
    }
}
