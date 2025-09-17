<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dossier_administratifs extends Model
{
 

    use HasFactory;

    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'date_ouverture',
        'statut',
        'notes',
    ];

    // Relation vers le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}


