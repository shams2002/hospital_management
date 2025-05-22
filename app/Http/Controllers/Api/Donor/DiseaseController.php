<?php

namespace App\Http\Controllers\Api\Donor;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{

    public function index()
    {
        $diseases = Disease::with(['doctor', 'patient', 'specialty'])->where('is_shown', true)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Diseases retrieved successfully.',
            'data' => $diseases
        ], 200);
    }

    public function show($id)
    {
        $disease = Disease::find($id);

        if ($disease->is_shown !== true) {
            return response()->json([
                'status' => 400,
                'message' => 'Disease not verified.'
            ], 400);
        }

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
}
