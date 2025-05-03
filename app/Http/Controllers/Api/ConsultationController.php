<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;
use App\Models\Doctor;

class ConsultationController extends Controller
{
    public function getSpecialtyDoctors(Request $request)
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
        ]);

        $doctors = Doctor::where('specialty_id', $request->specialty_id)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Doctors fetched successfully.',
            'data' => $doctors,
        ]);
    }

   
    public function getAuthPatientConsultations()
    {
        $user = Auth::user();

        $consultations = Consultation::where('patient_id', $user->patient->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Consultations fetched successfully.',
            'data' => $consultations,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'specialty_id' => 'required|exists:specialties,id',

            'question' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        if (! $user->is_patient) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $patientId = $user->patient->id;

        $consultation = Consultation::create([
            'patient_id'   => $patientId,
            'specialty_id' => $validated['specialty_id'],
            'question'     => $validated['question'],
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Consultation created successfully.',
            'data' => $consultation,
        ], 201);
    }
}
