<?php

use App\Http\Controllers\Api\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\Admin\AppointmentRequestController as AdminAppointmentRequestController;
use App\Http\Controllers\Api\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Api\Admin\DiseaseController as AdminDiseaseController;
use App\Http\Controllers\Api\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Api\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Api\Admin\DonorController as AdminDonorController;
use App\Http\Controllers\Api\Admin\PatientController as AdminPatientController;
use App\Http\Controllers\Api\Admin\SpecialityController as AdminSpecialityController;
use App\Http\Controllers\Api\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Api\Doctor\ConsultationController as DoctorConsultationController;
use App\Http\Controllers\Api\Donor\DiseaseController as DonorDiseaseController;
use App\Http\Controllers\Api\Donor\DonationController as DonorDonationController;
use App\Http\Controllers\Api\Donor\DonorController as DonorDonorController;
use App\Http\Controllers\Api\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Api\Patient\AppointmentRequestController as PatientAppointmentRequestController;
use App\Http\Controllers\Api\Patient\ConsultationController as PatientConsultationController;
use App\Http\Controllers\Api\Patient\DiseaseController as PatientDiseaseController;
use App\Http\Controllers\Api\Patient\PatientController as PatientPatientController;

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\Doctor\ProfileController;
use App\Http\Controllers\Api\DoctorAuthController;
use App\Http\Controllers\Api\DonorAuthController;
use App\Http\Controllers\Api\Patient\DoctorBySpecialityController;
use App\Http\Controllers\Api\PatientAuthController;

use Illuminate\Support\Facades\Route;


Route::post('/doctor/login', [DoctorAuthController::class, 'login']);

Route::middleware(['isDoctor', 'auth:sanctum'])->group(function () {
    Route::post('/doctor/logout', [DoctorAuthController::class, 'logout']);
    Route::get('/doctor/consultations', [DoctorConsultationController::class, 'getAuthDoctorConsultations']);
});
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});

Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/specialties')->group(function () {
    Route::post('/store', [AdminSpecialityController::class, 'store']);
    Route::get('/', [AdminSpecialityController::class, 'index']);
    Route::get('/{id}', [AdminSpecialityController::class, 'show']);

    Route::put('/update/{id}', [AdminSpecialityController::class, 'update']);
    Route::delete('/delete/{id}', [AdminSpecialityController::class, 'destroy']);
});


Route::prefix('patient')->group(function () {
    Route::post('/register', [PatientAuthController::class, 'register']);
    Route::post('/login', [PatientAuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
        Route::post('/logout', [PatientAuthController::class, 'logout']);
    });
});


Route::prefix('donor')->group(function () {
    Route::post('/register', [DonorAuthController::class, 'register']);
    Route::post('/login', [DonorAuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isDonor'])->group(function () {
        Route::post('/logout', [DonorAuthController::class, 'logout']);
    });
});

Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/consultations')->group(function () {
    Route::get('/', [AdminConsultationController::class, 'index']);
    Route::delete('/delete/{id}', [AdminConsultationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('/admin/appointment-requests')->group(function () {
    Route::get('/', [AdminAppointmentRequestController::class, 'index']);
    Route::get('/{id}', [AdminAppointmentRequestController::class, 'show']);
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/appointments')->group(function () {
    Route::post('/store', [AdminAppointmentController::class, 'store']);
    Route::get('/', [AdminAppointmentController::class, 'index']);
    Route::get('/{id}', [AdminAppointmentController::class, 'show']);
    Route::put('/update/{id}', [AdminAppointmentController::class, 'update']);
    Route::delete('/delete/{id}', [AdminAppointmentController::class, 'destroy']);
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/diseases')->group(function () {
    Route::get('/', [AdminDiseaseController::class, 'index']);
    Route::get('/{id}', [AdminDiseaseController::class, 'show']);
    Route::put('/update/{id}', [AdminDiseaseController::class, 'update']);
    Route::delete('/delete/{id}', [AdminDiseaseController::class, 'destroy']);
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/patients')->group(function () {
    Route::post('/store', [AdminPatientController::class, 'store']);
    Route::get('/', [AdminPatientController::class, 'index']);
    Route::get('/{id}', [AdminPatientController::class, 'show']);
    Route::put('/update/{id}', [AdminPatientController::class, 'update']);
    Route::delete('/delete/{id}', [AdminPatientController::class, 'destroy']);
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/donors')->group(function () {
    Route::get('/', [AdminDonorController::class, 'index']);
    Route::get('/{id}', [AdminDonorController::class, 'show']);
    Route::put('/update/{id}', [AdminDonorController::class, 'update']);
    Route::delete('/delete/{id}', [AdminDonorController::class, 'destroy']);
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/donations')->group(function () {
    Route::get('/', [AdminDonationController::class, 'index']);
    Route::get('/{id}', [AdminDonationController::class, 'show']);
    Route::put('/update/{id}', [AdminDonationController::class, 'update']);
    Route::delete('/delete/{id}', [AdminDonationController::class, 'destroy']);
    Route::post('/{donation}/accept', [AdminDonationController::class, 'acceptDonation']);  // Accept
    Route::post('/{donation}/reject', [AdminDonationController::class, 'rejectDonation']);  // Reject
});
Route::middleware(['isAdmin', 'auth:sanctum'])->prefix('/admin/doctors')->group(function () {
    Route::post('/store', [AdminDoctorController::class, 'store']);
    Route::get('/', [AdminDoctorController::class, 'index']);
    Route::get('/{id}', [AdminDoctorController::class, 'show']);
    Route::put('/update/{id}', [AdminDoctorController::class, 'update']);
    Route::delete('/delete/{id}', [AdminDoctorController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'isDonor'])->prefix('donor/diseases')->group(function () {
    Route::get('/', [DonorDiseaseController::class, 'index']);
    Route::get('/{id}', [DonorDiseaseController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'isDonor'])->prefix('donor/donations')->group(function () {
    Route::get('/', [DonorDonationController::class, 'index']);
    Route::post('store/', [DonorDonationController::class, 'store']);
    Route::get('/{id}', [DonorDonationController::class, 'show']);
    Route::put('update/{id}', [DonorDonationController::class, 'update']);
    Route::delete('delete/{id}', [DonorDonationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isDonor'])->prefix('donor')->group(function () {
    Route::get('/profile', [DonorDonorController::class, 'show']);
    Route::put('/profile', [DonorDonorController::class, 'update']);
});



Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
    Route::get('/patients/profile', [PatientPatientController::class, 'show']);
    Route::put('/patients/profile', [PatientPatientController::class, 'update']);
});
Route::middleware(['auth:sanctum', 'isPatient'])->prefix('patient/appointment-requests')->group(function () {
    Route::get('/', [PatientAppointmentRequestController::class, 'index']);
    Route::post('/store', [PatientAppointmentRequestController::class, 'store']);
    Route::get('/{id}', [PatientAppointmentRequestController::class, 'show']);
    Route::put('/{id}', [PatientAppointmentRequestController::class, 'update']);
    Route::delete('/{id}', [PatientAppointmentRequestController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'isPatient'])->prefix('patient/appointments')->group(function () {
    Route::get('/', [PatientAppointmentController::class, 'index']);
    Route::get('/{id}', [PatientAppointmentController::class, 'show']);
    Route::put('/{id}/accept', [PatientAppointmentController::class, 'acceptAppointment']);
    Route::put('/{id}/reject', [PatientAppointmentController::class, 'rejectAppointment']);
});
Route::middleware(['auth:sanctum', 'isPatient'])->prefix('patient/diseases')->group(function () {
    Route::get('/', [PatientDiseaseController::class, 'index']);
    Route::post('/store', [PatientDiseaseController::class, 'store']);
    Route::get('/{id}', [PatientDiseaseController::class, 'show']);
});

Route::middleware(['isPatient', 'auth:sanctum'])->prefix('/patient/consultations')->group(function () {
    Route::post('/store', [PatientConsultationController::class, 'store']);
    Route::get('/my-consultations', [PatientConsultationController::class, 'myConsultations']);
    Route::get('/', [PatientConsultationController::class, 'index']);
    Route::get('/{id}', [PatientConsultationController::class, 'show']);
    Route::put('/update/{id}', [PatientConsultationController::class, 'update']);
    Route::delete('/delete/{id}', [PatientConsultationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'isDoctor'])->prefix('doctor')->group(function () {
    Route::get('/consultations-by-specialty', [DoctorConsultationController::class, 'consultationsBySpecialty']);
    Route::post('/consultations/{id}/answer', [DoctorConsultationController::class, 'answerConsultation']);
    Route::get('/consultations/{id}/show', [DoctorConsultationController::class, 'show']);
    Route::delete('/answers/{id}', [DoctorConsultationController::class, 'deleteAnswer']);
});
Route::middleware(['auth:sanctum', 'isDoctor'])->group(function () {
    Route::get('/doctor/appointments', [DoctorAppointmentController::class, 'indexForCurrentDoctor']);
});
Route::middleware(['isDoctor', 'auth:sanctum'])->prefix('/doctor/specialties')->group(function () {

    Route::get('/', [AdminSpecialityController::class, 'index']);
});
Route::middleware(['isPatient', 'auth:sanctum'])->prefix('/patient/specialties')->group(function () {

    Route::get('/', [AdminSpecialityController::class, 'index']);
});
Route::middleware(['auth:sanctum', 'isDoctor'])->group(function () {
    Route::get('/doctor/profile', [ProfileController::class, 'profile']);
});
Route::middleware(['auth:sanctum', 'isPatient'])->group(function () {
    Route::get('/patient/doctorbyspeciality/{id}', [DoctorBySpecialityController::class, 'show']);
});
