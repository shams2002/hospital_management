<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'father_name',
        'last_name',
        'gender',
        'birth_date',
        'national_number',
        'address',
        'phone',
        'email',
        'social_status',
        'emergency_num',
        'insurance_company',
        'insurance_num',
        'smoker',
        'pregnant',
        'blood_type',
        'genetic_diseases',
        'chronic_diseases',
        'drug_allergy',
        'last_operations',
        'present_medicines',
        'status'
    ];
    //
    public function user()
    {
        return $this->belongsTo(User::class);
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
