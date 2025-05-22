<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disease;
use Illuminate\Support\Facades\Validator;

class DiseaseController extends Controller
{

    // عرض كل الأمراض الخاصة بالمريض الحالي فقط
    public function index()
    {
        $diseases = Disease::with(['doctor', 'patient', 'specialty'])
            ->where('patient_id', Auth::user()->patient->id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Diseases retrieved successfully.',
            'data' => $diseases
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
            'patient_status' => 'required|string',
            'available_money' => 'required|integer',
            'urgency_level' => 'required|string',
            'final_time' => 'required|date',
        ]);

        $validated['patient_id'] = Auth::user()->patient->id;
        $validated['needed_amount'] = 0;
        $validated['collected_amount'] = 0;
        $validated['donation_status'] = "pending";

        $disease = Disease::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Disease created successfully.',
            'data' => $disease
        ], 201);
    }


    // عرض مرض معين فقط إذا كان يخص المريض الحالي
    public function show($id)
    {
        $disease = Disease::find($id);

        if (!$disease || $disease->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'status' => 404,
                'message' => 'Unauthorized or disease not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Disease retrieved successfully.',
            'data' => $disease
        ], 200);
    }
}
