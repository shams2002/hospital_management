<?php

namespace App\Http\Controllers\Api\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DonorController extends Controller
{
    /**
     * Display the authenticated donor.
     */
    // public function index(): JsonResponse
    // {
    //     $user = Auth::user();

    //     if (!$user->is_donor) {
    //         return response()->json([
    //             'status' => 403,
    //             'message' => 'Access denied. Not a donor.'
    //         ], 403);
    //     }

    //     $donor = Donor::where('user_id', $user->id)->first();

    //     if (!$donor) {
    //         return response()->json([
    //             'status' => 404,
    //             'message' => 'Donor profile not found.'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Donor retrieved successfully.',
    //         'data' => $donor
    //     ], 200);
    // }

    /**
     * Store a newly created donor profile.
     */
    // public function store(Request $request): JsonResponse
    // {
    //     $user = Auth::user();

    //     if (!$user->is_donor) {
    //         return response()->json([
    //             'status' => 403,
    //             'message' => 'Access denied. Not a donor.'
    //         ], 403);
    //     }

    //     $validated = $request->validate([
    //         'first_name' => 'required|string',
    //         'father_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'gender' => 'required|in:male,female',
    //         'birth_date' => 'required|date',
    //         'national_number' => 'required|string|unique:donors,national_number',
    //         'address' => 'required|string',
    //         'phone' => 'required|string',
    //         'email' => 'required|email|unique:donors,email',
    //         'country' => 'required|string',
    //     ]);

    //     $validated['user_id'] = $user->id;

    //     $donor = Donor::create($validated);

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Donor profile created successfully.',
    //         'data' => $donor
    //     ], 201);
    // }

    /**
     * Display the authenticated donor profile by ID (if it belongs to user).
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        $donor = Donor::where('user_id', $user->id)->first();
        if (!$donor) {
            return response()->json([
                'status' => 404,
                'message' => 'Donor not found for the current user.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Donor retrieved successfully.',
            'data' => $donor
        ], 200);
    }

    /**
     * Update the authenticated donor profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $donor = Donor::where('user_id', $user->id)->first();

        if (!$donor) {
            return response()->json([
                'status' => 404,
                'message' => 'Donor not found for the current user.'
            ], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string',
            'father_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date',
            'national_number' => 'sometimes|string|unique:donors,national_number,' . $donor->id,
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'email' => 'sometimes|email|unique:donors,email,' . $donor->id,
            'country' => 'sometimes|string',
        ]);

        $donor->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Donor updated successfully.',
            'data' => $donor
        ], 200);
    }

    /**
     * Remove the authenticated donor profile.
     */
    // public function destroy($id): JsonResponse
    // {
    //     $user = Auth::user();

    //     $donor = Donor::where('id', $id)->where('user_id', $user->id)->first();

    //     if (!$donor) {
    //         return response()->json([
    //             'status' => 404,
    //             'message' => 'Donor not found or unauthorized.'
    //         ], 404);
    //     }

    //     $donor->delete();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Donor deleted successfully.'
    //     ], 200);
    // }
}
