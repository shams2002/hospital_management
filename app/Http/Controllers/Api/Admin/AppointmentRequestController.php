<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentRequest;
use Illuminate\Http\Request;

class AppointmentRequestController extends Controller
{
    public function index()
    {
        $requests = AppointmentRequest::with(['patient', 'doctor', 'specialty'])
            ->latest()
            ->get();


        return response()->json([
            'status' => 200,
            'message' => 'Appointment requests fetched successfully.',
            'data' => $requests
        ], 200);
    }
    public function show($id)
    {
        $appointmentRequest = AppointmentRequest::with(['patient', 'doctor', 'specialty'])
            ->find($id);

        if (!$appointmentRequest) {
            return response()->json([
                'status' => 404,
                'message' => 'Appointment request not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Appointment request details retrieved successfully.',
            'data' => $appointmentRequest
        ], 200);
    }
}
