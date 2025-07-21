<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard()
    {
        return view('patient.dashboard');
    }

    public function rendezvous()
    {
        return view('patient.rendezvous');
    }

    public function dossier()
    {
        return view('patient.dossiermedical');
    }
}
