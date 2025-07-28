<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $doctor = Auth::user()->doctor;

        return response()->json([
            'status' => 200,
            'message' => 'Doctor profile retrieved successfully.',
            'data' => $doctor
        ], 200);
    }
}
