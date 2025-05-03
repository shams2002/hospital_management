<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donor;
use Illuminate\Http\JsonResponse;

class DonorController extends Controller
{
    /**
     * Display a listing of the donors.
     */
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
