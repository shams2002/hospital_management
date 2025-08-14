<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use App\Models\Appointment;


class AppointmentController extends Controller
{

    public function index()
    {
        $appointments = Appointment::with('doctor', 'patient', 'specialty')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Appointments fetched successfully.',
            'data' => $appointments
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
            'work_day' => 'required|string',
            'work_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor = Doctor::find($request->doctor_id);

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'specialty_id' => $request->specialty_id,
            'doctor_id' => $request->doctor_id,
            'work_day' => $request->work_day,
            'work_time' => $request->work_time,
            'meet_cost' => $doctor->meet_cost,
            'meet_status' => "scheduled"
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Appointment scheduled successfully',
            'data' => $appointment
        ], 201);
    }

    public function show($id)
    {
        $appointment = Appointment::with('doctor', 'patient', 'specialty')->find($id);

        if (!$appointment) {
            return response()->json([
                'status' => 404,
                'message' => 'appointment not found'
            ], 404);
        }


        return response()->json([
            'status' => 200,
            'message' => 'Appointment details retrieved successfully.',
            'data' => $appointment
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'status' => 404,
                'message' => 'Appointment not found.'
            ], 404);
        }

        // dd($appointment->doctor->id);

        $validator = $request->validate([
            'doctore_id' => 'sometimes|exists:doctors,id',
            'work_day' => 'sometimes|string',
            'work_time' => 'sometimes|string',
            'status' => 'sometimes|in:rescheduled,done'
        ]);

        // Optional: Custom check to prevent assigning same doctor again
        if ($request->has('doctore_id') && $request->doctore_id == $appointment->doctor->id) {
            return response()->json([
                'message' => 'The doctor is already assigned to this appointment.'
            ], 422);
        }


        // Update the appointment
        $appointment->update($validator);



        return response()->json([
            'status' => 200,
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ], 200);
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'status' => 404,
                'message' => 'Appointment not found.'
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Appointment deleted successfully'
        ], 200);
    }
}
