<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable = [
        'name'
    ];
    //
    public function doctorShifts()
    {
        return $this->hasMany(DoctorShift::class);
    }
}
