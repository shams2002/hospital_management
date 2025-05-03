<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'disease_id',
        'donor_id',
        'image',
        'amount',
        'status',

    ];
    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
