<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ClinicalData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Patient",
 *     description="Patient profile and clinical data management"
 * )
 */
class PatientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/patient/profile",
     *     summary="Get patient profile",
     *     tags={"Patient"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Patient profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getProfile(Request $request): JsonResponse
    {
        $patient = $request->user()->load(['clinic', 'clinicalData']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'patient' => $patient
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/patient/profile",
     *     summary="Update patient profile",
     *     tags={"Patient"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="surname", type="string", example="Smith"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1980-03-15"),
     *             @OA\Property(property="address", type="string", example="123 Main Street, London"),
     *             @OA\Property(property="mobile_phone", type="string", example="03172650575"),
     *             @OA\Property(property="home_phone", type="string", example="02012345678"),
     *             @OA\Property(property="email", type="string", format="email", example="john.smith@example.com"),
     *             @OA\Property(property="clinic_id", type="integer", example=1),
     *             @OA\Property(property="notifications_consent", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'surname' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'address' => 'sometimes|required|string|max:1000',
            'mobile_phone' => 'sometimes|required|string|max:20',
            'home_phone' => 'nullable|string|max:20',
            'email' => 'sometimes|required|string|email|max:255|unique:patients,email,' . $patient->id,
            'clinic_id' => 'sometimes|required|exists:clinics,id',
            'notifications_consent' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient->update($request->only([
            'first_name', 'surname', 'date_of_birth', 'address',
            'mobile_phone', 'home_phone', 'email', 'clinic_id', 'notifications_consent'
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'patient' => $patient->load('clinic')
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/patient/clinical-data",
     *     summary="Save clinical data",
     *     tags={"Patient"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="height_cm", type="integer", example=175),
     *             @OA\Property(property="height_ft", type="integer", example=5),
     *             @OA\Property(property="height_inches", type="integer", example=9),
     *             @OA\Property(property="weight_kg", type="number", format="float", example=75.5),
     *             @OA\Property(property="weight_stones", type="integer", example=11),
     *             @OA\Property(property="weight_lbs", type="number", format="float", example=13.5),
     *             @OA\Property(property="ethnicity", type="string", example="Asian"),
     *             @OA\Property(property="smoking_status", type="string", enum={"never_smoked","current_smoker","ex_smoker","vaping","occasional_smoker"}),
     *             @OA\Property(property="hypertension_diagnosis", type="boolean", example=true),
     *             @OA\Property(property="medications", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="comorbidities", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="last_blood_test", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="urine_protein_creatinine_ratio", type="number", format="float", example=0.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinical data saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Clinical data saved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinical_data", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function saveClinicalData(Request $request): JsonResponse
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'height_cm' => 'nullable|integer|min:50|max:300',
            'height_ft' => 'nullable|integer|min:1|max:10',
            'height_inches' => 'nullable|integer|min:0|max:11',
            'weight_kg' => 'nullable|numeric|min:10|max:500',
            'weight_stones' => 'nullable|integer|min:1|max:50',
            'weight_lbs' => 'nullable|numeric|min:0|max:13.9',
            'ethnicity' => 'nullable|string|max:255',
            'smoking_status' => 'nullable|in:never_smoked,current_smoker,ex_smoker,vaping,occasional_smoker',
            'hypertension_diagnosis' => 'nullable|boolean',
            'medications' => 'nullable|array',
            'comorbidities' => 'nullable|array',
            'last_blood_test' => 'nullable|date|before:today',
            'urine_protein_creatinine_ratio' => 'nullable|numeric|min:0|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $clinicalData = $patient->clinicalData()->updateOrCreate(
            ['patient_id' => $patient->id],
            $request->all()
        );

        // Calculate BMI if height and weight are provided
        if ($request->height_cm && $request->weight_kg) {
            $bmi = $clinicalData->calculateBmi();
            $clinicalData->update(['bmi' => $bmi]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Clinical data saved successfully',
            'data' => [
                'clinical_data' => $clinicalData
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/patient/clinical-data",
     *     summary="Get clinical data",
     *     tags={"Patient"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Clinical data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinical_data", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getClinicalData(Request $request): JsonResponse
    {
        $patient = $request->user();
        $clinicalData = $patient->clinicalData;

        return response()->json([
            'status' => 'success',
            'data' => [
                'clinical_data' => $clinicalData
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/patient/dashboard",
     *     summary="Get patient dashboard data",
     *     tags={"Patient"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object"),
     *                 @OA\Property(property="last_reading", type="object"),
     *                 @OA\Property(property="recent_readings", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="seven_day_average", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $patient = $request->user();
        
        // Get last reading
        $lastReading = $patient->bloodPressureReadings()
            ->orderBy('reading_date', 'desc')
            ->first();

        // Get recent readings (last 7 days)
        $recentReadings = $patient->bloodPressureReadings()
            ->where('reading_date', '>=', now()->subDays(7))
            ->orderBy('reading_date', 'desc')
            ->get();

        // Calculate 7-day average (excluding day 1 as per NICE guidelines)
        $sevenDayAverage = $this->calculateSevenDayAverage($patient);

        return response()->json([
            'status' => 'success',
            'data' => [
                'patient' => $patient->load('clinic'),
                'last_reading' => $lastReading,
                'recent_readings' => $recentReadings,
                'seven_day_average' => $sevenDayAverage
            ]
        ]);
    }

    /**
     * Calculate 7-day average excluding day 1 (NICE guidelines)
     */
    private function calculateSevenDayAverage(Patient $patient): ?array
    {
        $readings = $patient->bloodPressureReadings()
            ->where('reading_date', '>=', now()->subDays(7))
            ->orderBy('reading_date', 'asc')
            ->get();

        if ($readings->count() < 4) {
            return null; // Need at least 4 days of readings
        }

        // Remove day 1 readings
        $day1Date = $readings->first()->reading_date->format('Y-m-d');
        $filteredReadings = $readings->filter(function ($reading) use ($day1Date) {
            return $reading->reading_date->format('Y-m-d') !== $day1Date;
        });

        if ($filteredReadings->count() < 3) {
            return null;
        }

        $avgSystolic = $filteredReadings->avg('average_systolic');
        $avgDiastolic = $filteredReadings->avg('average_diastolic');
        $avgPulse = $filteredReadings->avg('average_pulse');

        return [
            'systolic' => round($avgSystolic),
            'diastolic' => round($avgDiastolic),
            'pulse' => round($avgPulse),
            'total_readings' => $filteredReadings->count(),
            'days_with_readings' => $filteredReadings->groupBy(function ($reading) {
                return $reading->reading_date->format('Y-m-d');
            })->count()
        ];
    }
}