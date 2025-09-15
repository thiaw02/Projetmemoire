<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuiviController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('suivi.create');
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'temperature' => 'required',
        'tension' => 'required',
    ]);

    \App\Models\Suivi::create($request->all());

    return redirect()->route('infirmier.dashboard')->with('success', 'Suivi ajouté !');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
