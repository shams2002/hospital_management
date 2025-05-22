<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['patient', 'specialty'])->latest()->get();

        return response()->json([
            'status' => 200,
            'message' => 'All consultations fetched successfully.',
            'data' => $consultations,
        ], 200);
    }
    public function destroy($id)
    {
        $consultation = Consultation::find($id);

        if (! $consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation not found.',
            ], 404);
        }

        $consultation->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Consultation deleted successfully.',
        ], 200);
    }
}
