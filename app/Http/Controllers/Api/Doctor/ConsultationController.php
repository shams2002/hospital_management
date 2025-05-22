<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Answer;

class ConsultationController extends Controller
{
    public function getAuthDoctorConsultations()
    {
        $user = Auth::user();

        if (! $user->is_doctor || !$user->doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $doctor = $user->doctor;

        $consultations = Consultation::where('specialty_id', $doctor->specialty->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Consultations fetched successfully.',
            'data' => $consultations,
        ]);
    }

    /**
     * جلب جميع الاستشارات التي تخص اختصاص الدكتور الحالي
     */
    public function consultationsBySpecialty()
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: Not a doctor.',
            ], 403);
        }

        $consultations = Consultation::with('patient')
            ->where('specialty_id', $doctor->specialty_id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Consultations fetched successfully.',
            'data' => $consultations
        ], 200);
    }

    /**
     * يسمح للدكتور الحالي بالإجابة على استشارة
     */
    public function answerConsultation(Request $request, $consultationId)
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: Not a doctor.',
            ], 403);
        }

        $request->validate([
            'answer' => 'required|string',
        ]);

        // التحقق من أن الاستشارة من اختصاص الدكتور
        $consultation = Consultation::find($consultationId);
        if (! $consultation || $consultation->specialty_id !== $doctor->specialty_id) {
            return response()->json([
                'status' => 403,
                'message' => 'You are not authorized to answer this consultation.',
            ], 403);
        }

        $answer = Answer::create([
            'doctor_id' => $doctor->id,
            'consultation_id' => $consultationId,
            'answer' => $request->answer,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Answer submitted successfully.',
            'data' => $answer
        ], 201);
    }
    public function show($id)
    {
        if (!Auth::user()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized.',
            ], 401);
        }
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: Not a doctor.',
            ], 403);
        }

        // جلب الاستشارة مع العلاقة مع المريض
        $consultation = Consultation::with('patient')->find($id);

        if (! $consultation) {
            return response()->json([
                'status' => 404,
                'message' => 'Consultation not found.',
            ], 404);
        }

        // التحقق أن الاستشارة من اختصاص الدكتور
        if ($consultation->specialty_id !== $doctor->specialty_id) {
            return response()->json([
                'status' => 403,
                'message' => 'You are not authorized to view this consultation.',
            ], 403);
        }

        // جلب كل الأجوبة الخاصة بالاستشارة مع معلومات الطبيب
        $answers = Answer::with('doctor.user')
            ->where('consultation_id', $id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Consultation with all answers fetched successfully.',
            'data' => [
                'consultation' => $consultation,
                'answers' => $answers,
            ],
        ], 200);
    }

    /**
     * يسمح للدكتور بحذف جواب قام به
     */
    public function deleteAnswer($id)
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized: Not a doctor.',
            ], 403);
        }

        $answer = Answer::find($id);

        if (! $answer || $answer->doctor_id !== $doctor->id) {
            return response()->json([
                'status' => 403,
                'message' => 'You are not authorized to delete this answer.',
            ], 403);
        }

        $answer->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Answer deleted successfully.'
        ], 200);
    }
}
