<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;


class AppointmentController extends Controller
{

    public function index()
    {
        $patientId = Auth::user()->patient->id;

        $appointments = Appointment::with('doctor', 'patient')
            ->where('patient_id', $patientId)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Appointments fetched successfully.',
            'data' => $appointments
        ], 200);
    }

    public function show($id)
    {
        $appointment = Appointment::with('doctor', 'patient')->find($id);

        if (!$appointment || $appointment->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized access to this appointment.'
            ], 403);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Appointment details retrieved successfully.',
            'data' => $appointment
        ], 200);
    }
    public function acceptAppointment(Request $request,  $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json([
                'status' => 404,
                'message' => 'Unauthorized access to this appointment.'
            ], 404);
        }

        $appointment->update(["meet_status" => "accepted"]);

        return response()->json([
            'status' => 200,
            'message' => 'Appointment accepted successfully.',

        ], 200);
    }
    public function rejectAppointment(Request $request,  $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json([
                'status' => 404,
                'message' => 'Unauthorized access to this appointment.'
            ], 404);
        }

        $appointment->update(["meet_status" => "rejected"]);

        return response()->json([
            'status' => 200,
            'message' => 'Appointment rejected successfully.',

        ], 200);
    }
}
