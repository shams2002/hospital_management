<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialityController extends Controller
{
    // عرض جميع الـ specialties
    public function index()
    {
        $specialties = Specialty::all();

        return response()->json([
            'status' => 200,
            'message' => 'Specialties retrieved successfully',
            'data' => $specialties
        ], 200);
    }

    // عرض specialty معين
    public function show($id)
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json([
                'status' => 404,
                'message' => 'Specialty not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Specialty retrieved successfully',
            'data' => $specialty
        ], 200);
    }

    // إنشاء specialty جديد
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $specialty = Specialty::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Specialty created.',
            'data' => $specialty
        ], 201);
    }

    // تحديث specialty
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',

        ]);
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json([
                'status' => 404,
                'message' => 'Specialty not found'
            ], 404);
        }

        $specialty->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Specialty updated successfully',
            'data' => $specialty
        ], 200);
    }

    // حذف specialty
    public function destroy($id)
    {
        $specialty = Specialty::find($id);

        if (!$specialty) {
            return response()->json([
                'status' => 404,
                'message' => 'Specialty not found'
            ], 404);
        }

        $specialty->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Specialty deleted successfully'
        ]);
    }
}
