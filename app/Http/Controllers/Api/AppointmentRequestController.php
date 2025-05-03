<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppointmentRequest;

class AppointmentRequestController extends Controller
{

    public function index()
    {
        $requests = AppointmentRequest::where('patient_id', Auth::user()->patient->id)->get();

        return response()->json([
            'status' => 201,
            'data' => $requests
        ]);
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
            'message' => 'Appointment request created successfully',
            'data' => $appointmentRequest
        ]);
    }

    public function show($id)
    {
        $appointmentRequest = AppointmentRequest::findOrFail($id);

        return response()->json([
            'status' => 201,
            'data' => $appointmentRequest
        ]);
    }

    public function update(Request $request, $id)
    {
        $appointmentRequest = AppointmentRequest::findOrFail($id);

        $request->validate([
            'specialty_id' => 'sometimes|exists:specialties,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
        ]);

        $appointmentRequest->update($request->all());

        return response()->json([
            'status' => 201,
            'message' => 'Appointment request updated successfully',
            'data' => $appointmentRequest
        ]);
    }

    public function destroy($id)
    {
        $appointmentRequest = AppointmentRequest::findOrFail($id);
        $appointmentRequest->delete();

        return response()->json([
            'status' => 201,
            'message' => 'Appointment request deleted successfully'
        ]);
    }
}
