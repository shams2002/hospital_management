<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorShift extends Model
{
    protected $fillable = [
        'day_id',
        'doctor_id',
        'from',
        'to',
    ];
    //
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}
