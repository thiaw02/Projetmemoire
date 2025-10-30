<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function users()
    {
        // Personnel (un service par user)
        return $this->hasMany(User::class, 'service_id');
    }

    public function patients()
    {
        // Patients (plusieurs services par patient)
        return $this->belongsToMany(Patient::class, 'patient_service');
    }
}
