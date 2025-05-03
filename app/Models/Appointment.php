<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'specialty_id',
        'doctor_id',
        'patient_id',
        'work_day',
        'work_time',
        'meet_cost',
        'meet_status',
    ];
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
