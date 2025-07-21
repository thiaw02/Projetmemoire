<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedecinController extends Controller
{
    public function dashboard() {
    return view('medecin.dashboard');
}

public function dossierpatient() {
    return view('medecin.dossierpatient');
}

public function consultations() {
    return view('medecin.consultations');
}

public function ordonnances() {
    return view('medecin.ordonnances');
}

}
