<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\BloodPressureController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SpecialityController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\EthnicityController;
use App\Http\Controllers\Api\ComorbidityController;
use App\Http\Controllers\Api\MedicationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Admin authentication routes (public)
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
});

// Clinic search (public)
Route::prefix('clinics')->group(function () {
    Route::get('search', [ClinicController::class, 'search']);
    Route::get('nearby', [ClinicController::class, 'nearby']);
    Route::get('/', [ClinicController::class, 'index']);
    Route::get('{id}', [ClinicController::class, 'show']);
    Route::get('{id}/doctors', [ClinicController::class, 'doctors']);
});

// Specialities (public)
Route::prefix('specialities')->group(function () {
    Route::get('/', [SpecialityController::class, 'index']);
    Route::get('{id}', [SpecialityController::class, 'show']);
    Route::get('{id}/doctors', [SpecialityController::class, 'doctors']);
});

// Doctors (public)
Route::prefix('doctors')->group(function () {
    Route::get('/', [DoctorController::class, 'index']);
    Route::get('{id}', [DoctorController::class, 'show']);
});

// Ethnicity structure (public) - Hierarchical UK ONS ethnicity codes
Route::prefix('ethnicity')->group(function () {
    Route::get('categories', [EthnicityController::class, 'categories']);
    Route::get('categories/{id}', [EthnicityController::class, 'showCategory']);
    Route::get('subcategories', [EthnicityController::class, 'subcategories']);
    Route::get('subcategories/{id}', [EthnicityController::class, 'showSubcategory']);
    Route::get('categories/{categoryId}/subcategories', [EthnicityController::class, 'categorySubcategories']);
    Route::get('hierarchy', [EthnicityController::class, 'hierarchy']);
});

// Comorbidities (public)
Route::prefix('comorbidities')->group(function () {
    Route::get('/', [ComorbidityController::class, 'index']);
    Route::get('{id}', [ComorbidityController::class, 'show']);
});

// Medications (public)
Route::prefix('medications')->group(function () {
    Route::get('/', [MedicationController::class, 'index']);
    Route::get('frequency-options', [MedicationController::class, 'frequencyOptions']);
    Route::get('{id}', [MedicationController::class, 'show']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Patient profile routes
    Route::prefix('patient')->group(function () {
        Route::get('profile', [PatientController::class, 'getProfile']);
        Route::put('profile', [PatientController::class, 'updateProfile']);
        Route::get('dashboard', [PatientController::class, 'getDashboard']);
        Route::post('clinical-data', [PatientController::class, 'saveClinicalData']);
        Route::get('clinical-data', [PatientController::class, 'getClinicalData']);
    });

    // Blood pressure routes
    Route::prefix('blood-pressure')->group(function () {
        Route::post('record', [BloodPressureController::class, 'recordReading']);
        Route::get('readings', [BloodPressureController::class, 'getReadings']);
        Route::get('averages', [BloodPressureController::class, 'getAverages']);
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::post('generate', [ReportController::class, 'generateReport']);
        Route::get('summary', [ReportController::class, 'getReportSummary']);
        Route::get('history', [ReportController::class, 'getReportHistory']);
        Route::get('download/{filename}', [ReportController::class, 'downloadReport']);
    });

    // Admin authentication routes (protected)
    Route::prefix('admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('me', [AdminAuthController::class, 'me']);
    });

    // Admin routes (protected by admin middleware)
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Speciality management
        Route::prefix('specialities')->group(function () {
            Route::post('/', [SpecialityController::class, 'store']);
            Route::put('{id}', [SpecialityController::class, 'update']);
            Route::delete('{id}', [SpecialityController::class, 'destroy']);
        });

        // Doctor management
        Route::prefix('doctors')->group(function () {
            Route::post('/', [DoctorController::class, 'store']);
            Route::put('{id}', [DoctorController::class, 'update']);
            Route::delete('{id}', [DoctorController::class, 'destroy']);
            Route::post('{id}/specialities', [DoctorController::class, 'attachSpecialities']);
            Route::post('{id}/clinics', [DoctorController::class, 'attachClinics']);
        });
    });
});

// Health check endpoint
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'MyHomeBP API',
        'version' => '1.0.0'
    ]);
});


// Database test endpoint
Route::get('db-test', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        
        // Check if tables exist
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        return response()->json([
            'status' => 'connected',
            'database' => DB::connection()->getDatabaseName(),
            'tables' => $tableNames,
            'table_count' => count($tables)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'database' => config('database.default')
        ], 500);
    }
});
