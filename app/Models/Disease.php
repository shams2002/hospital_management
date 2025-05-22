<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = [
        'specialty_id',
        'doctor_id',
        'patient_id',
        'patient_status',
        'available_money',
        'needed_amount',
        'collected_amount',
        'urgency_level',
        'final_time',
        'donation_status',
        'is_shown'
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
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
