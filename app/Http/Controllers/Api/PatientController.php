<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index(): JsonResponse
    {
        $patients = Patient::all();

        return response()->json([
            'status' => 200,
            'message' => 'Patients retrieved successfully',
            'data' => $patients
        ], 200);
    }

    /**
     * Display the specified patient.
     */
    public function show($id): JsonResponse
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'status' => 404,
                'message' => 'Patient not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Patient retrieved successfully',
            'data' => $patient
        ], 200);
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy($id): JsonResponse
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return response()->json([
                'status' => 404,
                'message' => 'Patient not found'
            ], 404);
        }

        $patient->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Patient deleted successfully'
        ], 200);
    }
}
