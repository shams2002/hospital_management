<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'doctor_id',
        'consultation_id',
        'answer'
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
    //
}
