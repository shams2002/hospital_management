<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;
use App\Models\Answer;

class ConsultationController extends Controller
{

    /**
     * Display a listing of all consultations by all patients.
     */
    public function index()
    {
        $consultations = Consultation::with(['patient.user', 'specialty'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'All consultations retrieved successfully.',
            'data' => $consultations
        ], 200);
    }

    /**
     * Display the specified consultation with its answer (if any).
     */
    public function show($id)
    {
        $consultation = Consultation::with(['specialty', 'patient.user', 'answers.doctor.user'])->find($id);

        if (!$consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Consultation retrieved successfully.',
            'data' => $consultation
        ], 200);
    }
    /**
     * Display consultations of the currently authenticated patient.
     */
    public function myConsultations()
    {
        $patient = Auth::user()->patient;

        if (!$patient) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: Only patients can view their consultations.'
            ], 403);
        }

        $consultations = Consultation::with(['specialty', 'answers.doctor.user'])
            ->where('patient_id', $patient->id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Your consultations retrieved successfully.',
            'data' => $consultations
        ], 200);
    }

    /**
     * Store a new consultation by the currently authenticated patient.
     */
    public function store(Request $request)
    {

        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'question'     => 'required|string|max:1000',
        ]);

        $consultation = Consultation::create([
            'patient_id'   => Auth::user()->patient->id,
            'specialty_id' => $request->specialty_id,
            'question'     => $request->question,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Consultation created successfully.',
            'data' => $consultation
        ], 201);
    }

    /**
     * Update an existing consultation (only by its owner patient).
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::find($id);

        if (!$consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation not found.'
            ], 404);
        }

        if (Auth::user()->patient->id !== $consultation->patient_id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: You can only update your own consultation.'
            ], 403);
        }

        $request->validate([
            'specialty_id' => 'sometimes|exists:specialties,id',
            'question'     => 'sometimes|string|max:1000',
        ]);

        $consultation->update($request->only(['specialty_id', 'question']));

        return response()->json([
            'status' => 200,
            'message' => 'Consultation updated successfully.',
            'data' => $consultation
        ], 200);
    }

    /**
     * Remove a consultation (only by its owner patient).
     */
    public function destroy($id)
    {
        $consultation = Consultation::find($id);

        if (!$consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation not found.'
            ], 404);
        }

        if (Auth::user()->patient->id !== $consultation->patient_id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: You can only delete your own consultation.'
            ], 403);
        }

        $consultation->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Consultation deleted successfully.'
        ], 200);
    }
}
