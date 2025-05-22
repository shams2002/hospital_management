<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Disease;
use Illuminate\Support\Facades\Validator;


class DiseaseController extends Controller
{

    public function index()
    {
        $diseases = Disease::with(['doctor', 'patient', 'specialty'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'Diseases retrieved successfully.',
            'data' => $diseases
        ], 200);
    }

    public function show($id)
    {
        $disease = Disease::find($id);

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

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'needed_amount' => 'integer',
            'donation_status' => 'string',
            'is_shown' => 'boolean',

        ]);
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        $disease->update($validator);

        return response()->json([
            'status' => 200,
            'message' => 'Disease updated successfully.',
            'data' => $disease
        ], 200);
    }

    public function destroy($id)
    {
        $disease = Disease::find($id);

        if (!$disease) {
            return response()->json([
                'status' => 404,
                'message' => 'Disease not found.'
            ], 404);
        }

        $disease->delete();

        return response()->json([
            'status' => 204,
            'message' => 'Disease deleted successfully.'
        ], 204);
    }


}
