<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Patient;
use App\Mail\InscriptionConfirmee;
use App\Notifications\WelcomePatient;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (!Auth::user()->active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administrateur.'])->onlyInput('email');
            }

            $role = Auth::user()->role;

            return match ($role) {
                'admin'      => redirect()->route('admin.dashboard'),
                'secretaire' => redirect()->route('secretaire.dashboard'),
                'medecin'    => redirect()->route('medecin.dashboard'),
                'infirmier'  => redirect()->route('infirmier.dashboard'),
                'patient'    => redirect()->route('patient.dashboard'),
                default      => redirect('/login')->withErrors(['email' => 'Rôle utilisateur non reconnu.']),
            };
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('auth.inscription');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom'           => ['required', 'string', 'max:255'],
            'prenom'        => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'sexe'          => ['required', 'string', 'in:Masculin,Féminin'],
            'date_naissance'=> ['required', 'date'],
            'adresse'       => ['nullable', 'string'],
            'telephone'     => ['nullable', 'string'],
            'groupe_sanguin'=> ['nullable', 'string'],
            'antecedents'   => ['nullable', 'string'],
        ]);

        $mot_de_passe_defaut = Str::random(8);

        // Hachage du mot de passe pour l'enregistrement
        $user = User::create([
            'name'     => $request->prenom . ' ' . $request->nom,
            'email'    => $request->email,
            'password' => Hash::make($mot_de_passe_defaut),
            'role'     => 'patient',
        ]);

        $numero_dossier = 'PAT' . now()->format('Ymd') . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        $user->patient()->create([
            'numero_dossier'  => $numero_dossier,
            'nom'            => $request->nom,
            'prenom'         => $request->prenom,
            'user_id'        => $user->id,
            'sexe'           => $request->sexe,
            'date_naissance' => $request->date_naissance,
            'adresse'        => $request->adresse,
            'email'          => $request->email,
            'telephone'      => $request->telephone,
            'groupe_sanguin' => $request->groupe_sanguin,
            'antecedents'    => $request->antecedents,
        ]);

        session([
            'numero_dossier'   => $numero_dossier,
            'email'            => $request->email,
            'password_defaut'  => $mot_de_passe_defaut,
        ]);

        Mail::to($request->email)->send(new InscriptionConfirmee(
            $numero_dossier,
            $request->email,
            $mot_de_passe_defaut
        ));

        $user->notify(new WelcomePatient());

        return view('auth.inscription_succes');
    }
}