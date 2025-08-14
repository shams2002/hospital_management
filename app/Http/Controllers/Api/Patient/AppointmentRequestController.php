<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppointmentRequest;

class AppointmentRequestController extends Controller
{

    public function index()
    {
        $requests = AppointmentRequest::with([
            'specialty',
            'doctor.user',
            'patient.user'
        ])
            ->where('patient_id', Auth::user()->patient->id)->get();

        return response()->json([
            'status' => 200,
            'message' => 'Appointment requests retrieved successfully.',
            'data' => $requests
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $appointmentRequest = AppointmentRequest::create([
            'patient_id' => Auth::user()->patient->id,
            'specialty_id' => $request->specialty_id,
            'doctor_id' => $request->doctor_id,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Appointment request created successfully.',
            'data' => $appointmentRequest
        ], 201);
    }

    public function show($id)
    {
        $appointmentRequest = AppointmentRequest::find($id);

        if (!$appointmentRequest || $appointmentRequest->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'status' => 404,
                'message' => 'Unauthorized access or appointment not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Appointment request retrieved successfully.',
            'data' => $appointmentRequest
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $appointmentRequest = AppointmentRequest::find($id);

        if (!$appointmentRequest || $appointmentRequest->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized access or appointment not found.'
            ], 403);
        }

        $request->validate([
            'specialty_id' => 'sometimes|exists:specialties,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
        ]);

        $appointmentRequest->update($request->only(['specialty_id', 'doctor_id']));

        return response()->json([
            'status' => 200,
            'message' => 'Appointment request updated successfully.',
            'data' => $appointmentRequest
        ], 200);
    }

    public function destroy($id)
    {
        $appointmentRequest = AppointmentRequest::find($id);

        if (!$appointmentRequest || $appointmentRequest->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized access or appointment not found.'
            ], 403);
        }

        $appointmentRequest->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Appointment request deleted successfully.'
        ], 200);
    }
}
