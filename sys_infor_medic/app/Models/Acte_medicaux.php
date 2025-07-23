<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acte_medicaux extends Model
{
    use HasFactory;

    protected $table = 'acte_medicaux';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'type_acte',
        'description',
        'date_acte',
        'cout',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
}

