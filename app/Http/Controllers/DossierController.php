<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier_medicaux;

class DossierController extends Controller
{
    // Formulaire pour éditer un dossier médical
    public function edit($id)
    {
        $dossier = Dossier_medicaux::findOrFail($id);
        return view('dossier.edit', compact('dossier')); // Crée resources/views/dossier/edit.blade.php
    }

    // Mettre à jour le dossier
    public function update(Request $request, $id)
    {
        $dossier = Dossier_medicaux::findOrFail($id);
        $dossier->update($request->all());

        return redirect()->route('dossier.index')->with('success', 'Dossier mis à jour avec succès.');
    }
}
