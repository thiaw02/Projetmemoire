<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AffectationsController extends Controller
{
    public function index(Request $request)
    {
        $doctors = User::where('role', 'medecin')->orderBy('name')->get();
        $nurses = User::where('role', 'infirmier')->orderBy('name')->get();

        $doctorId = (int) $request->query('doctor_id', 0);
        $selectedDoctor = $doctorId ? $doctors->firstWhere('id', $doctorId) : null;
        $assignedNurseIds = $selectedDoctor ? $selectedDoctor->nurses()->pluck('users.id')->toArray() : [];

        return view('admin.affectations.index', [
            'doctors' => $doctors,
            'nurses' => $nurses,
            'selectedDoctor' => $selectedDoctor,
            'assignedNurseIds' => $assignedNurseIds,
        ]);
    }

    public function update(Request $request, $doctor)
    {
        // Validate doctor exists and is medecin
        $medecin = User::where('id', $doctor)->where('role', 'medecin')->firstOrFail();

        $validated = $request->validate([
            'nurses' => ['array'],
            'nurses.*' => ['integer', 'exists:users,id'],
        ]);

        $nurseIds = collect($validated['nurses'] ?? [])->unique()->values();

        // Ensure all provided IDs are infirmiers
        $validNurses = User::whereIn('id', $nurseIds)->where('role', 'infirmier')->pluck('id');

        // Sync assignments
        $medecin->nurses()->sync($validNurses->all());

        return redirect()->route('admin.affectations.index', ['doctor_id' => $medecin->id])
            ->with('success', 'Affectations mises à jour avec succès.');
    }
}
