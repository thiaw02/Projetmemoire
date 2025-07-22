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
    public function showRegistrationForm()
{
    return view('auth.inscription'); // adapte si ton fichier s'appelle autrement
}
 public function register(Request $request)
    {
        // Valider les données entrées
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Créer un nouvel utilisateur
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Rediriger vers login avec message de succès
        return redirect('/login')->with('success', 'Inscription réussie ! Veuillez vous connecter.');
    }

    // Affiche la page de connexion
    public function showLoginForm()
    {
        return view('auth.login'); // resources/views/auth/login.blade.php
    }

}
