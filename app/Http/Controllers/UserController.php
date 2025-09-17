<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Liste utilisateurs (hors patients)
    public function dashboard()
    {
        $users = User::where('role', '!=', 'patient')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'role'=>'required|in:admin,medecin,infirmier,secretaire',
            'specialite'=>'nullable|string|max:255',
            'password'=>'required|string|min:6|confirmed',
        ]);
        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur ajouté');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>"required|email|unique:users,email,{$id}",
            'role'=>'required|in:admin,medecin,infirmier,secretaire',
            'specialite'=>'nullable|string|max:255',
            'password'=>'nullable|string|min:6|confirmed',
        ]);
        if ($data['password'] ?? false) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur modifié');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur supprimé');
    }

    // Patients
    public function patientsList()
    {
        $patients = User::where('role', 'patient')->get();
        return view('admin.patients.index', compact('patients'));
    }

    public function createPatient()
    {
        return view('admin.patients.create');
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6|confirmed',
        ]);
        $data['role'] = 'patient';
        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Patient ajouté');
    }

    public function editPatient($id)
    {
        $patient = User::findOrFail($id);
        return view('admin.patients.edit', compact('patient'));
    }

    public function updatePatient(Request $request, $id)
    {
        $patient = User::findOrFail($id);
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>"required|email|unique:users,email,{$id}",
            'password'=>'nullable|string|min:6|confirmed',
        ]);
        if ($data['password'] ?? false) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $patient->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Patient modifié');
    }

    public function destroyPatient($id)
    {
        User::destroy($id);
        return redirect()->route('admin.dashboard')->with('success', 'Patient supprimé');
    }
}
