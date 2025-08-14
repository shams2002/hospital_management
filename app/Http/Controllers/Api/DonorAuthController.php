<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Donor;

class DonorAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'father_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'national_number' => 'required|string|unique:donors',
            'address' => 'required|string',
            'phone' => 'required|string|unique:donors',
            'email' => 'required|email|unique:users',
            'password'          => 'required|string|min:8|confirmed',
            'country' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Create user
        $user = User::create([
            'name'      => $request->first_name . ' ' . $request->last_name,
            'email'     => $request->email,
            'password'  => $request->password, // or generate a default password
            'is_admin'  => false,
            'is_doctor' => false,
            'is_patient' => false,
            'is_donor'  => true,

        ]);

        // Create donor
        $donor = Donor::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'father_name' => $request->father_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'national_number' => $request->national_number,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Donor registered successfully',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_donor) {
            return response()->json([
                'status' => 403,
                'message' => 'Access denied. Not a donor account.'
            ], 403);
        }

        // جلب بيانات المتبرع المرتبط بالمستخدم
        $donor = Donor::where('user_id', $user->id)->first();
        $token = $user->createToken('donor-token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token,
            'user_id' => $user->id,
            'donor_id' => $donor ? $donor->id : null,  // حقل id للمتبرع
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
