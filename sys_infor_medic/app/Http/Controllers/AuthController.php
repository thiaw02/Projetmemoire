<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $role = $request->input('role');

        // Stocker le rôle en session (simulé)
        session(['role' => $role]);

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'secretaire':
                return redirect()->route('secretaire.dashboard');
            case 'medecin':
                return redirect()->route('medecin.dashboard');
            case 'infirmier':
                return redirect()->route('infirmier.dashboard');
            case 'patient':
                return redirect()->route('patient.dashboard');
            default:
                return back()->withErrors(['role' => 'Rôle invalide']);
        }
    }

    public function logout()
    {
        session()->flush(); // Effacer les données de session
        return redirect()->route('login');
    }
}
