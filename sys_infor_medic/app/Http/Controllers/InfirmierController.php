<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfirmierController extends Controller
{
    public function dashboard()
    {
        return view('infirmier.dashboard');
    }

    public function dossiers()
    {
        return view('infirmier.dossiers');
    }
}
