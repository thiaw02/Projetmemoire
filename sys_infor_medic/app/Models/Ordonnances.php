<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordonnances extends Model
{
    use HasFactory;

    protected $table = 'ordonnances';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'contenu',
        'date_ordonnance',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }
    public function consultation()
{
    return $this->belongsTo(Consultations::class);
}

}


