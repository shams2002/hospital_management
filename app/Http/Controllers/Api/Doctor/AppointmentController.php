<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function indexForCurrentDoctor()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Access denied. You must be logged in as a doctor.'
            ], 403);
        }

        $appointments = Appointment::with(['patient', 'specialty'])
            ->where('doctor_id', $doctor->id)
            ->whereIn('meet_status', ['accepted', 'done'])
            ->get();


        return response()->json([
            'status' => 200,
            'message' => 'Appointments for current doctor retrieved successfully.',
            'data' => $appointments
        ], 200);
    }
}
