<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use App\Models\Appointment;

class AppointmentController extends Controller
{

    public function index()
    {
        return response()->json([
            'status' => 201,
            'data' => Appointment::with('doctor', 'patient')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
            'work_day' => 'required|string',
            'work_time' => 'required|string',
            'meet_status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor = Doctor::findOrFail($request->doctor_id);

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'specialty_id' => $request->specialty_id,
            'doctor_id' => $request->doctor_id,
            'work_day' => $request->work_day,
            'work_time' => $request->work_time,
            'meet_cost' => $doctor->meet_cost,
            'meet_status' => $request->meet_status
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Appointment scheduled successfully',
            'data' => $appointment
        ]);
    }

    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);

        return response()->json([
            'status' => 201,
            'data' => $appointment
        ]);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'work_day' => 'sometimes|string',
            'work_time' => 'sometimes|string',
            'meet_status' => 'sometimes|string'
        ]);

        $appointment->update($request->all());

        return response()->json([
            'status' => 201,
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'status' => 201,
            'message' => 'Appointment deleted successfully'
        ]);
    }
}
