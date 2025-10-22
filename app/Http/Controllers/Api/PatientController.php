<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ClinicalData;
use App\Models\Comorbidity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
     *             @OA\Property(property="height", type="number", format="float", example=175.0, description="Height in cm"),
     *             @OA\Property(property="weight", type="number", format="float", example=70.0, description="Weight in kg"),
     *             @OA\Property(property="ethnicity_code", type="string", example="C", description="UK ONS ethnicity code - Get available codes from /api/ethnicity/categories"),
     *             @OA\Property(property="ethnicity_description", type="string", example="Pakistani", description="Ethnicity description - Get from /api/ethnicity/categories"),
     *             @OA\Property(property="smoking_status", type="string", enum={"never_smoked","current_smoker","ex_smoker","vaping","occasional_smoker"}, example="never_smoked", description="Smoking Status Options: never_smoked, current_smoker, ex_smoker, vaping, occasional_smoker - Get options from /api/smoking-status/options"),
     *             @OA\Property(property="last_blood_test_date", type="string", format="date", example="2024-01-15", description="Date of last blood test (YYYY-MM-DD format)"),
     *             @OA\Property(property="urine_protein_creatinine_ratio", type="number", format="float", example=0.5, description="Urine Protein:Creatinine Ratio"),
     *             @OA\Property(property="comorbidities", type="array", @OA\Items(type="string", enum={"stroke","diabetes_type_1","diabetes_type_2","atrial_fibrillation","transient_ischaemic_attack","chronic_kidney_disease","others"}), example={"diabetes_type_2"}, description="Comorbidity Options: stroke, diabetes_type_1, diabetes_type_2, atrial_fibrillation, transient_ischaemic_attack, chronic_kidney_disease, others"),
     *             @OA\Property(property="hypertension_diagnosis", type="string", enum={"yes","no","dont_know"}, example="yes", description="Hypertension Diagnosis Options: yes, no, dont_know"),
     *             @OA\Property(property="medications", type="array", @OA\Items(
     *                 @OA\Property(property="bnf_code", type="string", example="0205050A0AA", description="BNF medication code - Get available codes from /api/medications"),
     *                 @OA\Property(property="dose", type="string", example="5mg", description="Medication dose (e.g., 5mg, 10mg, 2.5ml)"),
     *                 @OA\Property(property="frequency", type="string", example="once_daily", description="Medication frequency - Get options from /api/medications/frequency-options")
     *             ), example={{"bnf_code": "0205050A0AA", "dose": "5mg", "frequency": "once_daily"}}, description="Array of medication objects (only required if hypertension_diagnosis is 'yes')")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinical data saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Clinical data saved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinical_data", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="patient_id", type="integer", example=21),
     *                     @OA\Property(property="height", type="number", format="float", example=175.0, description="Height in cm"),
     *                     @OA\Property(property="weight", type="number", format="float", example=70.0, description="Weight in kg"),
     *                     @OA\Property(property="bmi", type="number", format="float", example=22.9, description="Calculated BMI (automatically calculated from height and weight)"),
     *                     @OA\Property(property="ethnicity_code", type="string", example="C", description="UK ONS ethnicity code"),
     *                     @OA\Property(property="ethnicity_description", type="string", example="Pakistani", description="Ethnicity description"),
     *                     @OA\Property(property="smoking_status", type="string", example="never_smoked", description="Smoking Status: never_smoked, current_smoker, ex_smoker, vaping, occasional_smoker"),
     *                     @OA\Property(property="last_blood_test_date", type="string", format="date", example="2024-01-15", description="Date of last blood test"),
     *                     @OA\Property(property="urine_protein_creatinine_ratio", type="number", format="float", example=0.5, description="Urine Protein:Creatinine Ratio"),
     *                     @OA\Property(property="comorbidities", type="array", @OA\Items(type="string"), example={"diabetes_type_2"}, description="Comorbidities: stroke, diabetes_type_1, diabetes_type_2, atrial_fibrillation, transient_ischaemic_attack, chronic_kidney_disease, others"),
     *                     @OA\Property(property="hypertension_diagnosis", type="string", example="yes", description="Hypertension diagnosis: yes, no, dont_know"),
     *                     @OA\Property(property="medications", type="array", @OA\Items(
     *                         @OA\Property(property="bnf_code", type="string", example="0205050A0AA", description="BNF medication code"),
     *                         @OA\Property(property="dose", type="string", example="5mg", description="Medication dose"),
     *                         @OA\Property(property="frequency", type="string", example="once_daily", description="Medication frequency")
     *                     ), example={{"bnf_code": "0205050A0AA", "dose": "5mg", "frequency": "once_daily"}}, description="Array of medication objects"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function saveClinicalData(Request $request): JsonResponse
    {
        $patient = $request->user();

        // Load allowed comorbidity codes dynamically from DB
        $allowedComorbidityCodes = Comorbidity::query()
            ->active()
            ->pluck('code')
            ->toArray();

        $validator = Validator::make($request->all(), [
            'height' => 'nullable|numeric|min:50|max:300',
            'weight' => 'nullable|numeric|min:10|max:500',
            'ethnicity_code' => 'nullable|string|max:10|exists:ethnicity_subcategories,code',
            'ethnicity_description' => 'nullable|string|max:255',
            'smoking_status' => 'nullable|in:never_smoked,current_smoker,ex_smoker,vaping,occasional_smoker',
            'last_blood_test_date' => 'nullable|date|before:today',
            'urine_protein_creatinine_ratio' => 'nullable|numeric|min:0|max:1000',
            'comorbidities' => 'nullable|array',
            'comorbidities.*' => [Rule::in($allowedComorbidityCodes)],
            'hypertension_diagnosis' => 'nullable|in:yes,no,dont_know',
            'medications' => 'required_if:hypertension_diagnosis,yes|array|min:1',
            'medications.*.bnf_code' => 'required_if:hypertension_diagnosis,yes|string|exists:medications,bnf_code',
            'medications.*.dose' => 'required_if:hypertension_diagnosis,yes|string|max:50',
            'medications.*.frequency' => 'required_if:hypertension_diagnosis,yes|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Prepare data for clinical data creation
        $clinicalDataInput = $request->only([
            'height',
            'weight',
            'ethnicity_code',
            'ethnicity_description',
            'smoking_status',
            'last_blood_test_date',
            'urine_protein_creatinine_ratio',
            'comorbidities',
            'hypertension_diagnosis',
            'medications'
        ]);

        // If hypertension_diagnosis is "no", set medications to null
        if ($request->input('hypertension_diagnosis') === 'no') {
            $clinicalDataInput['medications'] = null;
        }

        $clinicalData = $patient->clinicalData()->updateOrCreate(
            ['patient_id' => $patient->id],
            $clinicalDataInput
        );

        // BMI will be automatically calculated by the model's boot method

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
     *                 @OA\Property(property="clinical_data", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="patient_id", type="integer", example=21),
     *                     @OA\Property(property="height", type="number", format="float", example=175.0, description="Height in cm"),
     *                     @OA\Property(property="weight", type="number", format="float", example=70.0, description="Weight in kg"),
     *                     @OA\Property(property="bmi", type="number", format="float", example=22.9, description="Calculated BMI (automatically calculated from height and weight)"),
     *                     @OA\Property(property="ethnicity_code", type="string", example="C", description="UK ONS ethnicity code - Get available codes from /api/ethnicity/categories"),
     *                     @OA\Property(property="ethnicity_description", type="string", example="Pakistani", description="Ethnicity description - Get from /api/ethnicity/categories"),
     *                     @OA\Property(property="smoking_status", type="string", example="never_smoked", description="Smoking Status: never_smoked, current_smoker, ex_smoker, vaping, occasional_smoker"),
     *                     @OA\Property(property="last_blood_test_date", type="string", format="date", example="2024-01-15", description="Date of last blood test"),
     *                     @OA\Property(property="urine_protein_creatinine_ratio", type="number", format="float", example=0.5, description="Urine Protein:Creatinine Ratio"),
     *                     @OA\Property(property="comorbidities", type="array", @OA\Items(type="string"), example={"diabetes_type_2"}, description="Comorbidities: stroke, diabetes_type_1, diabetes_type_2, atrial_fibrillation, transient_ischaemic_attack, chronic_kidney_disease, others"),
     *                     @OA\Property(property="hypertension_diagnosis", type="string", example="yes", description="Hypertension diagnosis: yes, no, dont_know"),
     *                     @OA\Property(property="medications", type="array", @OA\Items(
     *                         @OA\Property(property="bnf_code", type="string", example="0205050A0AA", description="BNF medication code"),
     *                         @OA\Property(property="dose", type="string", example="5mg", description="Medication dose"),
     *                         @OA\Property(property="frequency", type="string", example="once_daily", description="Medication frequency")
     *                     ), example={{"bnf_code": "0205050A0AA", "dose": "5mg", "frequency": "once_daily"}}, description="Array of medication objects"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
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