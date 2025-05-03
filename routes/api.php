<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\DoctorAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DonorAuthController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\AppointmentRequestController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DiseaseController;
use App\Http\Controllers\Api\Doctor\ConsultationController as DoctorConsultationController;
use App\Http\Controllers\Api\DonationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/doctor/login', [DoctorAuthController::class, 'login']);

Route::middleware(['isDoctor', 'auth:sanctum'])->group(function () {
    Route::post('/doctor/logout', [DoctorAuthController::class, 'logout']);
    Route::post('/doctor/answers', [AnswerController::class, 'store']);
    Route::get('/doctor/consultations', [DoctorConsultationController::class, 'getAuthDoctorConsultations']);
});
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});

Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/doctors')->group(function () {
    Route::post('/store', [DoctorController::class, 'store']);
    Route::get('/', [DoctorController::class, 'index']);
    Route::get('/{id}', [DoctorController::class, 'show']);
    Route::put('/update/{id}', [DoctorController::class, 'update']);
    Route::delete('/delete/{id}', [DoctorController::class, 'destroy']);
});


Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/specialties')->group(function () {
    Route::post('/store', [SpecialtyController::class, 'store']);
    Route::get('/', [SpecialtyController::class, 'index']);
    Route::get('/{id}', [SpecialtyController::class, 'show']);

    Route::put('/update/{id}', [SpecialtyController::class, 'update']);
    Route::delete('/delete/{id}', [SpecialtyController::class, 'destroy']);
});


Route::prefix('patient')->group(function () {
    Route::post('/register', [PatientAuthController::class, 'register']);
    Route::post('/login', [PatientAuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
        Route::post('/logout', [PatientAuthController::class, 'logout']);
        Route::get('/doctors-by-specialty', [ConsultationController::class, 'getSpecialtyDoctors']);
    });
});


Route::prefix('donor')->group(function () {
    Route::post('/register', [DonorAuthController::class, 'register']);
    Route::post('/login', [DonorAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'isDonor'])->group(function () {
        Route::post('/logout', [DonorAuthController::class, 'logout']);
    });
});
Route::middleware(['auth:sanctum', 'IsAdmin'])->prefix('admin/patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);
    Route::get('/{id}', [PatientController::class, 'show']);
    Route::delete('/{id}', [PatientController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'IsAdmin'])->prefix('admin/donors')->group(function () {
    Route::get('/', [DonorController::class, 'index']);
    Route::get('/{id}', [DonorController::class, 'show']);
    Route::delete('/{id}', [DonorController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
    Route::get('/patient/consultations', [ConsultationController::class, 'getAuthPatientConsultations']);
    Route::post('patient/consultations', [ConsultationController::class, 'store']);
});
Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
    // طلبات المواعيد من المريض
    Route::post('/appointment-requests', [AppointmentRequestController::class, 'store']);
    Route::get('/appointment-requests', [AppointmentRequestController::class, 'index']);
    Route::get('/appointment-requests/{id}', [AppointmentRequestController::class, 'show']);
    Route::put('/appointment-requests/{id}', [AppointmentRequestController::class, 'update']);
    Route::delete('/appointment-requests/{id}', [AppointmentRequestController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // مواعيد يتم جدولتها من قبل الأدمن
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
    Route::post('/diseases', [DiseaseController::class, 'store']);
    Route::get('/diseases', [DiseaseController::class, 'index']);
    Route::get('/diseases/{id}', [DiseaseController::class, 'show']);
    Route::put('/diseases/{id}', [DiseaseController::class, 'update']);
    Route::delete('/diseases/{id}', [DiseaseController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->put('/diseases/{id}/admin', [DiseaseController::class, 'adminUpdate']);
Route::middleware(['auth:sanctum', 'is_donor'])->group(function () {
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/donations', [DonationController::class, 'index']);
    Route::get('/donations/{id}', [DonationController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::put('/donations/{id}/admin', [DonationController::class, 'adminUpdate']);
    Route::delete('/donations/{id}', [DonationController::class, 'destroy']);
});
