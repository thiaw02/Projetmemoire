<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossier_medicaux extends Model
{
    use HasFactory;

    protected $table = 'dossier_medicaux';

    protected $fillable = [
        'patient_id',
        'diagnostic',
        'traitement',
        'date_consultation',
    ];

    /**
     * Relation avec le patient
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

