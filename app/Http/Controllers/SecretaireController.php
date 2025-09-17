<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecretaireController extends Controller
{
    public function dashboard()
    {
        return view('secretaire.dashboard');
    }

    public function dossiersAdmin()
    {
        return view('secretaire.dossieradmin');
    }

    public function rendezvous()
    {
        return view('secretaire.rendezvous');
    }
    public function admissions()
    {
        return view('secretaire.admissions');
    }
}
