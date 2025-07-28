<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{

    /**
     * Display the current patient's data.
     */
    public function index(): JsonResponse
    {
        $patient = Patient::where('user_id',  Auth::id())->first();

        if (!$patient) {
            return response()->json([
                'status' => 404,
                'message' => 'Patient not found for current user',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Patient retrieved successfully',
            'data' => $patient
        ], 200);
    }
    /**
     * Show specific patient if authorized.
     */
    public function show(): JsonResponse
    {
        $patient = Patient::where('user_id', Auth::id())->first();

        if (!$patient) {
            return response()->json([
                'status' => 404,
                'message' => 'Patient not found for current user'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Patient retrieved successfully',
            'data' => $patient
        ], 200);
    }

    /**
     * Register a new patient (public access).
     */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name'        => 'required|string|max:255',
    //         'father_name'       => 'required|string|max:255',
    //         'last_name'         => 'required|string|max:255',
    //         'gender'            => 'required|in:male,female',
    //         'birth_date'        => 'required|date',
    //         'national_number'   => 'required|string|unique:patients,national_number',
    //         'address'           => 'required|string|max:255',
    //         'phone'             => 'required|string|unique:patients,phone',
    //         'email'             => 'required|email|unique:users,email',
    //         'password'          => 'required|string|min:8|confirmed',
    //         'social_status'     => 'nullable|string',
    //         'emergency_num'     => 'nullable|string|max:20',
    //         'insurance_company' => 'nullable|string|max:255',
    //         'insurance_num'     => 'nullable|string|max:50',
    //         'smoker'            => 'nullable|boolean',
    //         'pregnant'          => 'nullable|boolean',
    //         'blood_type'        => 'nullable|string|max:3',
    //         'genetic_diseases'  => 'nullable|string',
    //         'chronic_diseases'  => 'nullable|string',
    //         'drug_allergy'      => 'nullable|string',
    //         'last_operations'   => 'nullable|string',
    //         'present_medicines' => 'nullable|string',
    //         'status'            => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $user = User::create([
    //         'name'       => $request->first_name . ' ' . $request->last_name,
    //         'email'      => $request->email,
    //         'password'   => bcrypt($request->password),
    //         'is_patient' => true,
    //     ]);

    //     $patient = Patient::create([
    //         'user_id'           => $user->id,
    //         'first_name'        => $request->first_name,
    //         'father_name'       => $request->father_name,
    //         'last_name'         => $request->last_name,
    //         'gender'            => $request->gender,
    //         'birth_date'        => $request->birth_date,
    //         'national_number'   => $request->national_number,
    //         'address'           => $request->address,
    //         'phone'             => $request->phone,
    //         'email'             => $request->email,
    //         'password'          => bcrypt($request->password),
    //         'social_status'     => $request->social_status,
    //         'emergency_num'     => $request->emergency_num,
    //         'insurance_company' => $request->insurance_company,
    //         'insurance_num'     => $request->insurance_num,
    //         'smoker'            => $request->smoker,
    //         'pregnant'          => $request->pregnant,
    //         'blood_type'        => $request->blood_type,
    //         'genetic_diseases'  => $request->genetic_diseases,
    //         'chronic_diseases'  => $request->chronic_diseases,
    //         'drug_allergy'      => $request->drug_allergy,
    //         'last_operations'   => $request->last_operations,
    //         'present_medicines' => $request->present_medicines,
    //         'status'            => $request->status,
    //     ]);

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Patient created successfully',
    //         'data' => $patient
    //     ], 201);
    // }

    /**
     * Update a patient's information (only if authenticated and matches user).
     */
    public function update(Request $request): JsonResponse
    {
        $patient = Patient::where('user_id', Auth::id())->first();


        if (!$patient) {
            return response()->json([
                'status' => 404,
                'message' => 'Patient not found for current user'
            ], 404);
        }

        $validator = $request->validate([
            'first_name'        => 'nullable|string|max:255',
            'father_name'       => 'nullable|string|max:255',
            'last_name'         => 'nullable|string|max:255',
            'gender'            => 'nullable|in:male,female',
            'birth_date'        => 'nullable|date',
            'national_number'   => 'nullable|string|unique:patients,national_number,' . $patient->id,
            'address'           => 'nullable|string|max:255',
            'phone'             => 'nullable|string|unique:patients,phone,' . $patient->id,
            'email'             => 'nullable|email|unique:patients,email,' . $patient->id,
            'social_status'     => 'nullable|string',
            'emergency_num'     => 'nullable|string|max:20',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_num'     => 'nullable|string|max:50',
            'smoker'            => 'nullable|boolean',
            'pregnant'          => 'nullable|boolean',
            'blood_type'        => 'nullable|string|max:3',
            'genetic_diseases'  => 'nullable|string',
            'chronic_diseases'  => 'nullable|string',
            'drug_allergy'      => 'nullable|string',
            'last_operations'   => 'nullable|string',
            'present_medicines' => 'nullable|string',
        ]);

        $patient->update($validator);

        return response()->json([
            'status' => 200,
            'message' => 'Patient updated successfully',
            'data' => $patient
        ], 200);
    }

    /**
     * Delete a patient if authenticated.
     */
    // public function destroy($id): JsonResponse
    // {
    //     $patient = Patient::find($id);

    //     if (!$patient || $patient->user_id !==  Auth::id()) {
    //         return response()->json([
    //             'status' => 403,
    //             'message' => 'Unauthorized or patient not found'
    //         ], 403);
    //     }

    //     $patient->delete();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Patient deleted successfully'
    //     ], 200);
    // }
}
