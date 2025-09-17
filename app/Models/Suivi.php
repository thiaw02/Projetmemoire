<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{


    protected $fillable = ['patient_id', 'temperature', 'tension'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
