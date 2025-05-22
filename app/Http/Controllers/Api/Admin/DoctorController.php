<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorShift;
use App\Models\Specialty;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;

class DoctorController extends Controller
{
    /**
     * Register a new doctor (only accessible by admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'specialty_id' => 'required|exists:specialties,id',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'national_number' => 'required|string|unique:doctors,national_number',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'license_number' => 'required|string|max:255',
            'experience_years' => 'required|integer',
            'meet_cost' => 'required|numeric',
            'image' => 'nullable|image|max:2048|mimes:jpeg,jpg,png',
            'bio' => 'nullable|string',
            'shifts' => 'required|array',
            'shifts.*.day_id' => 'required|exists:days,id',
            'shifts.*.from' => 'required|date_format:H:i',
            'shifts.*.to' => 'required|date_format:H:i|after:shifts.*.from',
        ]);

        $imageName = "";
        if ($request->file('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
        }

        // Create the user for the doctor (non-admin)
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => $request->password, // or generate a default password
            'is_doctor' => true,
        ]);

        // Create the doctor profile
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $request->specialty_id,
            'first_name' => $request->first_name,
            'father_name' => $request->father_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'national_number' => $request->national_number,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'license_number' => $request->license_number,
            'experience_years' => $request->experience_years,
            'meet_cost' => $request->meet_cost,
            'image' => $imageName,
            'bio' => $request->bio,
        ]);

        foreach ($request->shifts as $shift) {
            DoctorShift::create([
                'doctor_id' => $doctor->id,
                'day_id' => $shift['day_id'],
                'from' => $shift['from'],
                'to' => $shift['to'],
            ]);
        }

        return response()->json(['status' => 201, 'message' => 'Doctor created.', 'data' => $doctor], 201);
    }


    /**
     * List all doctors (Admin only).
     */
    public function index()
    {
        $doctors = Doctor::with('specialty')->get();
        return response()->json([
            "status" => 200,
            "message" => "Doctors retrived",
            "data" => $doctors
        ], 200);
    }

    /**
     * Show doctor details.
     */
    public function show($id)
    {

        $doctor = Doctor::with('specialty', 'doctorShifts.day')->find($id);
        if (!$doctor) {
            return response()->json(
                [
                    "status" => 404,
                    "message" => "Not Found"
                ],
                404

            );
        }

        return response()->json([
            'status' => 200,
            "message" => "Doctors retrived",
            'data' => $doctor,
        ], 200);
    }

    /**
     * Update doctor details.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json(
                [
                    "status" => 404,
                    "message" => "Not Found"
                ],
                404

            );
        }
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'specialty_id' => 'nullable|exists:specialties,id',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'national_number' => 'nullable|string|unique:doctors,national_number,' . $doctor->id,

            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:users,email,' . $doctor->user->id,
            'license_number' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer',
            'meet_cost' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048|mimes:jpeg,jpg,png',
            'bio' => 'nullable|string',
            'shifts' => 'array',
            'shifts.*.day_id' => 'exists:days,id',
            'shifts.*.from' => 'date_format:H:i',
            'shifts.*.to' => 'date_format:H:i|after:shifts.*.from',
        ]);

        $imageName = $doctor->image;
        if ($request->file('image')) {
            $oldImagePath = public_path('storage/uploads/' . $doctor->image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
        }

        $doctor->update(array_merge($request->all(), ['image' => $imageName]));

        if ($request->shifts) {
            // Sync doctor shifts (add, update, delete)
            $existingShiftIds = $doctor->doctorShifts->pluck('id')->toArray();
            $incomingShiftIds = collect($request->shifts)->pluck('id')->filter()->toArray(); // Only existing IDs

            // Delete shifts that were removed
            $shiftsToDelete = array_diff($existingShiftIds, $incomingShiftIds);
            DoctorShift::destroy($shiftsToDelete);


            foreach ($request->shifts as $shift) {
                if (isset($shift['id'])) {
                    // Update existing shift
                    $existingShift = DoctorShift::find($shift['id']);
                    if ($existingShift) {
                        $existingShift->update([
                            'day_id' => $shift['day_id'],
                            'from' => $shift['from'],
                            'to' => $shift['to'],
                        ]);
                    }
                } else {
                    // Create new shift
                    DoctorShift::create([
                        'doctor_id' => $doctor->id,
                        'day_id' => $shift['day_id'],
                        'from' => $shift['from'],
                        'to' => $shift['to'],
                    ]);
                }
            }
        }
        return response()->json(['status' => 200, 'message' => 'Doctor updated.', 'doctor' => $doctor], 200);
    }

    /**
     * Delete doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json(
                [
                    "status" => 404,
                    "message" => "Not Found"
                ],
                404
            );
        }
        $oldImagePath = public_path('storage/uploads/' . $doctor->image);
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }

        // Delete related doctor shifts
        if ($doctor->doctorShifts && $doctor->doctorShifts->count() > 0) {
            $doctor->doctorShifts()->delete();
        }
        $doctor->delete();

        return response()->json(['message' => 'Doctor deleted.'], 200);
    }
}
