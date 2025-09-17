<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admissions extends Model
{

    use HasFactory;

    protected $fillable = [
        'patient_id',
        'date_admission',
        'date_sortie',
        'motif_admission',
        'service',
        'observations',
    ];

    // Relation vers patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}


