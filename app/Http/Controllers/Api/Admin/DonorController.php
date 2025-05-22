<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donor;
use Illuminate\Http\JsonResponse;

class DonorController extends Controller
{
    public function index(): JsonResponse
    {
        $donors = Donor::all();

        return response()->json([
            'status' => 200,
            'message' => 'Donors retrieved successfully',
            'data' => $donors
        ], 200);
    }

    /**
     * Display the specified donor.
     */
    public function show($id): JsonResponse
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json([
                'status' => 404,
                'message' => 'Donor not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Donor retrieved successfully',
            'data' => $donor
        ], 200);
    }
    // public function update(Request $request, $id): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'first_name'       => 'string|max:255',
    //         'father_name'      => 'string|max:255',
    //         'last_name'        => 'string|max:255',
    //         'gender'           => 'in:male,female,other',
    //         'birth_date'       => 'date',
    //         'national_number'  => 'string|max:20|unique:donors,national_number,' . $id,
    //         'address'          => 'string|max:500',
    //         'phone'            => 'string|max:20',
    //         'email'            => 'email|max:255|unique:donors,email,' . $id,
    //         'country'          => 'string|max:100',
    //     ]);

    //     $donor = Donor::find($id);

    //     if (!$donor) {
    //         return response()->json([
    //             'status' => 404,
    //             'message' => 'Donor not found'
    //         ], 404);
    //     }

    //     $donor->update($validated);

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Donor updated successfully',
    //         'data' => $donor
    //     ], 200);
    // }

    /**
     * Remove the specified donor from storage.
     */
    public function destroy($id): JsonResponse
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json([
                'status' => 404,
                'message' => 'Donor not found'
            ], 404);
        }

        $donor->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Donor deleted successfully'
        ], 200);
    }
}
