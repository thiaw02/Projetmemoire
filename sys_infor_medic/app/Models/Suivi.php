<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'temperature',
        'tension',
        'date_suivi',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

