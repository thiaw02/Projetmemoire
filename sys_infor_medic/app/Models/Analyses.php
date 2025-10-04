<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyses extends Model
{
    use HasFactory;

    protected $table = 'analyses';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type_analyse',
        'resultats',
        'date_analyse',
        'etat',
    ];

    public function patient()
{
    return $this->belongsTo(Patient::class, 'patient_id');
}


    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}

