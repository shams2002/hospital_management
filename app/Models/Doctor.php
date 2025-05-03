<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty_id',
        'first_name',
        'father_name',
        'last_name',
        'gender',
        'birth_date',
        'national_number',
        'address',
        'phone',
        'email',
        'license_number',
        'experience_years',
        'meet_cost',
        'image',
        'bio',
    ];
    protected $appends = ["image_url"];
    public function getImageUrlAttribute()
    {
        return url('/storage/uploads/' . $this->image);
    }
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function doctorShifts()
    {
        return $this->hasMany(DoctorShift::class);
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
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
