<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
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
        'country',
    ];
    //
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
