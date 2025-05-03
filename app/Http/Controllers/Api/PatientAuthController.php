<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\PatientRegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientAuthController extends Controller
{
    //  تسجيل حساب جديد
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'father_name'       => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date'        => 'required|date',
            'national_number'   => 'required|string|unique:patients,national_number',
            'address'           => 'required|string|max:255',
            'phone'             => 'required|string|unique:patients,phone',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
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

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::create([
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'password'   => $request->password, // or generate a default password
            'is_admin'   => false, // non-admin user
            'is_doctor'  => false,
            'is_patient' => true,
            'is_donor'   => false,
        ]);

        $patient = Patient::create([
            'user_id'           => $user->id,
            'first_name'        => $request->first_name,
            'father_name'       => $request->father_name,
            'last_name'         => $request->last_name,
            'gender'            => $request->gender,
            'birth_date'        => $request->birth_date,
            'national_number'   => $request->national_number,
            'address'           => $request->address,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'password'          => $request->password,
            'social_status'     => $request->social_status,
            'emergency_num'     => $request->emergency_num,
            'insurance_company' => $request->insurance_company,
            'insurance_num'     => $request->insurance_num,
            'smoker'            => $request->smoker,
            'pregnant'          => $request->pregnant,
            'blood_type'        => $request->blood_type,
            'genetic_diseases'  => $request->genetic_diseases,
            'chronic_diseases'  => $request->chronic_diseases,
            'drug_allergy'      => $request->drug_allergy,
            'last_operations'   => $request->last_operations,
            'present_medicines' => $request->present_medicines,
            'status'            => "pending",
        ]);
        return response()->json([
            'status' => 201,
            'message' => 'Patient created',

        ], 201);
    }

    // 🔐 تسجيل الدخول
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_patient) {
            return response()->json([
                'status' => 403,
                'message' => 'Access denied. Not a patient account.'
            ], 403);
        }

        $token = $user->createToken('patient-token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token,
            'user_id' => $user->id
        ], 200);
    }

    // 🚪 تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
