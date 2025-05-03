<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disease;



class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::with(['doctor', 'patient', 'specialty'])->get();

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

        $disease = Disease::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Disease created successfully.',
            'data' => $disease
        ], 201);
    }

    public function show($id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Disease retrieved successfully.',
            'data' => $disease
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        if (Auth::user()->patient->id !== $disease->patient_id) {
            return response()->json([
                'status' => 403,
                'message' => 'You are not authorized to update this disease.'
            ], 403);
        }

        $disease->update($request->only(['patient_status', 'available_money', 'urgency_level', 'final_time']));

        return response()->json([
            'status' => 200,
            'message' => 'Disease updated successfully.',
            'data' => $disease
        ], 200);
    }

    public function destroy($id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        $disease->delete();

        return response()->json([
            'status' => 204,
            'message' => 'Disease deleted successfully.'
        ], 204);
    }

    public function adminUpdate(Request $request, $id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        $disease->update($request->only(['needed_amount', 'donation_status']));

        return response()->json([
            'status' => 200,
            'message' => 'Disease updated by admin.',
            'data' => $disease
        ], 200);
    }
}
