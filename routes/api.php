
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\TreatmentCategoryController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StaffScheduleController;
use App\Http\Controllers\Api\BedAdmissionController;
use App\Http\Controllers\Api\HospitalBedController;
use App\Http\Controllers\Api\CombinedController;

// Public Routes

Route::get('/health', function () {
    return response()->json([
        'message' => 'Hospital API is running',
    ]);
});

// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected Routes

Route::middleware('jwt')->group(function () {

    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Users
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::match(['put', 'patch'], '/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])
        ->middleware('permission:patients.read');

    Route::post('/patients', [PatientController::class, 'store'])
        ->middleware('permission:patients.create');

    Route::get('/patients/{id}', [PatientController::class, 'show'])
        ->middleware('permission:patients.read');

    Route::match(['put', 'patch'], '/patients/{id}', [PatientController::class, 'update'])
        ->middleware('permission:patients.update');

    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])
        ->middleware('permission:patients.delete');


    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index'])
        ->middleware('permission:doctors.read');

    Route::post('/doctors', [DoctorController::class, 'store'])
        ->middleware('permission:doctors.create');

    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::match(['put', 'patch'], '/doctors/{id}', [DoctorController::class, 'update'])
        ->middleware('permission:staff.update');

    Route::delete('/doctors/{id}', [DoctorController::class, 'destroy'])
        ->middleware('permission:doctors.delete');


    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])
        ->middleware('permission:appointments.read');

    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->middleware('permission:appointments.create');

    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])
        ->middleware('permission:appointments.read');

    Route::match(['put', 'patch'], '/appointments/{id}', [AppointmentController::class, 'update'])
        ->middleware('permission:appointments.update');

    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])
        ->middleware('permission:appointments.delete');

    // Treatments
    Route::get('/treatments', [TreatmentController::class, 'index'])
        ->middleware('permission:treatments.read');

    Route::post('/treatments', [TreatmentController::class, 'store'])
        ->middleware('permission:treatments.create');

    Route::get('/treatments/{id}', [TreatmentController::class, 'show'])
        ->middleware('permission:treatments.read');

    Route::match(['put', 'patch'], '/treatments/{id}', [TreatmentController::class, 'update'])
        ->middleware('permission:treatments.update');

    Route::delete('/treatments/{id}', [TreatmentController::class, 'destroy'])
        ->middleware('permission:treatments.delete');

    // Treatment Categories
    Route::get('/treatment_categories', [TreatmentCategoryController::class, 'index'])
        ->middleware('permission:treatment_categories.read');

    Route::post('/treatment_categories', [TreatmentCategoryController::class, 'store'])
        ->middleware('permission:treatment_categories.create');

    Route::get('/treatment_categories/{id}', [TreatmentCategoryController::class, 'show'])
        ->middleware('permission:treatment_categories.read');

    Route::match(['put', 'patch'], '/treatment_categories/{id}', [TreatmentCategoryController::class, 'update'])
        ->middleware('permission:treatment_categories.update');

    Route::delete('/treatment_categories/{id}', [TreatmentCategoryController::class, 'destroy'])
        ->middleware('permission:treatment_categories.delete');

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])
        ->middleware('permission:billing.read');

    Route::post('/billing', [BillingController::class, 'store'])
        ->middleware('permission:billing.create');

    Route::get('/billing/{id}', [BillingController::class, 'show'])
        ->middleware('permission:billing.read');

    Route::match(['put', 'patch'], '/billing/{id}', [BillingController::class, 'update'])
        ->middleware('permission:billing.update');

    Route::delete('/billing/{id}', [BillingController::class, 'destroy'])
        ->middleware('permission:billing.delete');


    // Staff
    Route::middleware('role:admin')->group(function () {
        Route::get('/staff', [StaffController::class, 'index']);
        Route::post('/staff', [StaffController::class, 'store']);
        Route::delete('/staff/{id}', [StaffController::class, 'destroy']);
    });

    Route::get('/staff/{id}', [StaffController::class, 'show']);

    Route::match(['put', 'patch'], '/staff/{id}', [StaffController::class, 'update']);


    // Staff Schedules
    Route::get('/staff_schedules', [StaffScheduleController::class, 'index'])
        ->middleware('permission:staff_schedules.read');

    Route::post('/staff_schedules', [StaffScheduleController::class, 'store'])
        ->middleware('role:admin');

    Route::get('/staff_schedules/{id}', [StaffScheduleController::class, 'show'])
        ->middleware('permission:staff_schedules.read');

    Route::match(['put', 'patch'], '/staff_schedules/{id}', [StaffScheduleController::class, 'update'])
        ->middleware('permission:staff_schedules.update');

    Route::delete('/staff_schedules/{id}', [StaffScheduleController::class, 'destroy'])
        ->middleware('role:admin');

    // Bed Admissions
    Route::get('/bed_admissions', [BedAdmissionController::class, 'index'])
        ->middleware('permission:bed_admissions.read');

    Route::post('/bed_admissions', [BedAdmissionController::class, 'store'])
        ->middleware('permission:bed_admissions.create');

    Route::get('/bed_admissions/{id}', [BedAdmissionController::class, 'show'])
        ->middleware('permission:bed_admissions.read');

    Route::match(['put', 'patch'], '/bed_admissions/{id}', [BedAdmissionController::class, 'update'])
        ->middleware('permission:bed_admissions.update');

    Route::delete('/bed_admissions/{id}', [BedAdmissionController::class, 'destroy'])
        ->middleware('role:admin');

    // Hospital Beds
    Route::get('/hospital_beds', [HospitalBedController::class, 'index'])
        ->middleware('permission:hospital_beds.read');

    Route::post('/hospital_beds', [HospitalBedController::class, 'store'])
        ->middleware('role:admin');

    Route::get('/hospital_beds/{week}/{month}/{service}', [HospitalBedController::class, 'show'])
        ->middleware('permission:hospital_beds.read');

    Route::match(['put', 'patch'], '/hospital_beds/{week}/{month}/{service}', [HospitalBedController::class, 'update'])
        ->middleware('role:admin');

    Route::delete('/hospital_beds/{week}/{month}/{service}', [HospitalBedController::class, 'destroy'])
        ->middleware('role:admin');

    // Combined
    Route::get('/combined/patient/{patient_id}', [CombinedController::class, 'patient'])
        ->middleware('role:admin');

});
