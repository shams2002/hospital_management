<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function getAuthDoctorConsultations()
    {
        $user = Auth::user();

        if (! $user->is_doctor || !$user->doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $doctor = $user->doctor;

        $consultations = Consultation::where('specialty_id', $doctor->specialty->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Consultations fetched successfully.',
            'data' => $consultations,
        ]);
    }
}
