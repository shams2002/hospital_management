<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorBySpecialityController extends Controller
{
    public function show($id)
    {
        $doctors = Doctor::with('specialty')
            ->where('specialty_id', $id)
            ->select('first_name', 'father_name', 'last_name', 'specialty_id')
            ->get();

        return response()->json([
            "status" => 200,
            "message" => "Doctors retrieved for specialty ID: $id",
            "data" => $doctors
        ]);
    }
}
