<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use App\Models\PatientDocument;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $settings = Setting::pluck('value','key')->toArray();
        $user = $request->user();
        $documents = [];
        if (($user->role ?? null) === 'patient' && $user->patient) {
            $documents = PatientDocument::where('patient_id', $user->patient->id)->latest()->get();
        }
        return view('profile.edit', [
            'user' => $user,
            'settings' => $settings,
            'documents' => $documents,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $user->fill($data);
        // Champs spécifiques par rôle
        if ($user->role === 'medecin') {
            $extra = $request->validate([
                'specialite' => ['nullable','string','max:255']
            ]);
            $user->specialite = $extra['specialite'] ?? $user->specialite;
        }
        // Champs spécifiques secrétaires/infirmiers
        if (in_array($user->role, ['secretaire','infirmier'])) {
            $extra = $request->validate([
                'pro_phone' => ['nullable','string','max:255']
            ]);
            $user->pro_phone = $extra['pro_phone'] ?? $user->pro_phone;
        }
        // Champs spécifiques médecin
        if ($user->role === 'medecin') {
            $extra2 = $request->validate([
                'matricule' => ['nullable','string','max:255'],
                'cabinet'   => ['nullable','string','max:255'],
                'horaires'  => ['nullable','string','max:2000'],
            ]);
            $user->matricule = $extra2['matricule'] ?? $user->matricule;
            $user->cabinet   = $extra2['cabinet'] ?? $user->cabinet;
            $user->horaires  = $extra2['horaires'] ?? $user->horaires;
        }
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();
        return Redirect::route('profile.edit')->with('success', 'Profil mis à jour');
    }

    public function updatePatientInfo(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== 'patient') {
            abort(403);
        }
        $validated = $request->validate([
            'nom'             => ['required','string','max:255'],
            'prenom'          => ['required','string','max:255'],
            'telephone'       => ['nullable','string','max:255'],
            'sexe'            => ['required','string','in:Masculin,Féminin'],
            'date_naissance'  => ['required','date'],
            'adresse'         => ['nullable','string','max:255'],
            'groupe_sanguin'  => ['nullable','string','max:255'],
            'antecedents'     => ['nullable','string'],
        ]);
        // Mettre à jour ou créer la fiche patient
        $patient = $user->patient ?: $user->patient()->create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'user_id' => $user->id,
            'sexe' => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'email' => $user->email,
        ]);
        $patient->update([
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'sexe'           => $validated['sexe'],
            'date_naissance' => $validated['date_naissance'],
            'adresse'        => $validated['adresse'] ?? null,
            'telephone'      => $validated['telephone'] ?? null,
            'groupe_sanguin' => $validated['groupe_sanguin'] ?? null,
            'antecedents'    => $validated['antecedents'] ?? null,
            'email'          => $user->email,
        ]);
        return Redirect::route('profile.edit')->with('success', 'Informations patient mises à jour');
    }

    /**
     * Delete the user's account.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required','string','min:6','confirmed']
        ]);
        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return Redirect::route('profile.edit')->withErrors(['current_password' => 'Mot de passe actuel incorrect'], 'updatePassword');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return Redirect::route('profile.edit')->with('success', 'Mot de passe mis à jour');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048']
        ]);
        
        $user = $request->user();
        
        // Supprimer l'ancien avatar s'il existe
        if ($user->avatar_url) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        // Sauvegarder le nouveau
        $path = $request->file('avatar')->store('avatars', 'public');
        $url = Storage::url($path);
        $user->avatar_url = $url;
        $user->save();
        
        // Rediriger vers la page paramètres patient si c'est un patient
        $route = $user->role === 'patient' ? 'patient.settings' : 'profile.edit';
        return Redirect::route($route)->with('success', 'Photo de profil mise à jour');
    }
    
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->avatar_url) {
            // Supprimer le fichier du stockage
            $path = str_replace('/storage/', '', $user->avatar_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            // Supprimer l'URL de la base de données
            $user->avatar_url = null;
            $user->save();
        }
        
        // Rediriger vers la page paramètres patient si c'est un patient
        $route = $user->role === 'patient' ? 'patient.settings' : 'profile.edit';
        return Redirect::route($route)->with('success', 'Photo de profil supprimée');
    }

    public function uploadPatientDocument(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== 'patient' || !$user->patient) {
            abort(403);
        }
        $validated = $request->validate([
            'file' => ['required','file','mimes:pdf,jpg,jpeg,png,webp','max:4096'],
            'type' => ['nullable','string','max:50'],
            'label'=> ['nullable','string','max:255'],
        ]);
        $path = $request->file('file')->store('patient_docs','public');
        $url = \Illuminate\Support\Facades\Storage::url($path);
        PatientDocument::create([
            'patient_id' => $user->patient->id,
            'label' => $validated['label'] ?? null,
            'type' => $validated['type'] ?? null,
            'file_path' => $url,
            'uploaded_by' => $user->id,
        ]);
        return Redirect::route('profile.edit')->with('success','Document ajouté');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            abort(403);
        }
        $data = $request->validate([
            'site_name' => ['nullable','string','max:255'],
            'allow_registrations' => ['nullable','boolean'],
        ]);
        $pairs = [
            'site_name' => $data['site_name'] ?? null,
            'allow_registrations' => isset($data['allow_registrations']) ? ($data['allow_registrations'] ? '1' : '0') : null,
        ];
        foreach ($pairs as $k => $v) {
            if (!is_null($v)) {
                Setting::updateOrCreate(['key' => $k], ['value' => $v]);
            }
        }
        return Redirect::route('profile.edit')->with('success', 'Paramètres plateforme mis à jour');
    }

    public function deletePatientDocument(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== 'patient' || !$user->patient) abort(403);
        $doc = \App\Models\PatientDocument::where('id',$id)->where('patient_id', $user->patient->id)->firstOrFail();
        // Optionnel: supprimer le fichier du disque
        if ($doc->file_path) {
            $rel = str_replace('/storage/', '', $doc->file_path);
            if (\Storage::disk('public')->exists($rel)) {
                \Storage::disk('public')->delete($rel);
            }
        }
        $doc->delete();
        return Redirect::route('profile.edit')->with('success','Document supprimé');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
