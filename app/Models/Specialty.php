<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $fillable = [
        'name'
    ];
    //
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function appointmentRequests()
    {
        return $this->hasMany(AppointmentRequest::class);
    }
    public function diseases()
    {
        return $this->hasMany(Disease::class);
    }
}
