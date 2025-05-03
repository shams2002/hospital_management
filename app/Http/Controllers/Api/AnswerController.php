<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{

    public function store(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id', // التأكد من أن الاستشارة موجودة
            'answer' => 'required|string|max:1000', // التأكد من أن الإجابة ليست فارغة
        ]);

        // الحصول على الطبيب الذي قام بتسجيل الدخول
        $user = Auth::user();

        // تحقق من أن الطبيب يمكنه الرد على الاستشارة المحددة
        $consultation = Consultation::find($validated['consultation_id']);



        // إنشاء الإجابة المرتبطة بالاستشارة
        $answer = Answer::create([
            'doctor_id' => $user->doctor->id,
            'consultation_id' => $validated['consultation_id'],
            'answer' => $validated['answer'],
        ]);

        // إرجاع الاستجابة مع البيانات الجديدة
        return response()->json([
            'status' => 200,
            'message' => 'Answer submitted successfully.',
            'data' => $answer,
        ], 201);
    }
}
