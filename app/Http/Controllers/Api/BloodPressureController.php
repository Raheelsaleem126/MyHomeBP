<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodPressureReading;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Blood Pressure",
 *     description="Blood pressure recording and management"
 * )
 */
class BloodPressureController extends Controller
{
    /**
     * @OA\Post(
     *     path="/blood-pressure/record",
     *     summary="Record blood pressure reading",
     *     tags={"Blood Pressure"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reading_date","session_type","reading_1_systolic","reading_1_diastolic","reading_1_pulse","reading_2_systolic","reading_2_diastolic","reading_2_pulse"},
     *             @OA\Property(property="reading_date", type="string", format="date-time", example="2024-01-15T09:30:00Z"),
     *             @OA\Property(property="session_type", type="string", enum={"am","pm"}, example="am"),
     *             @OA\Property(property="reading_1_systolic", type="integer", example=120),
     *             @OA\Property(property="reading_1_diastolic", type="integer", example=80),
     *             @OA\Property(property="reading_1_pulse", type="integer", example=72),
     *             @OA\Property(property="reading_2_systolic", type="integer", example=118),
     *             @OA\Property(property="reading_2_diastolic", type="integer", example=78),
     *             @OA\Property(property="reading_2_pulse", type="integer", example=70),
     *             @OA\Property(property="reading_3_systolic", type="integer", example=115),
     *             @OA\Property(property="reading_3_diastolic", type="integer", example=75),
     *             @OA\Property(property="reading_3_pulse", type="integer", example=68)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Blood pressure reading recorded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Blood pressure reading recorded successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="reading", type="object"),
     *                 @OA\Property(property="system_response", type="string", example="Thank you for submitting today's reading."),
     *                 @OA\Property(property="requires_third_reading", type="boolean", example=false)
     *             )
     *         )
     *     )
     * )
     */
    public function recordReading(Request $request): JsonResponse
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'reading_date' => 'required|date',
            'session_type' => 'required|in:am,pm',
            'reading_1_systolic' => 'required|integer|min:50|max:300',
            'reading_1_diastolic' => 'required|integer|min:1|max:150',
            'reading_1_pulse' => 'required|integer|min:20|max:300',
            'reading_2_systolic' => 'required|integer|min:50|max:300',
            'reading_2_diastolic' => 'required|integer|min:1|max:150',
            'reading_2_pulse' => 'required|integer|min:20|max:300',
            'reading_3_systolic' => 'nullable|integer|min:50|max:300',
            'reading_3_diastolic' => 'nullable|integer|min:1|max:150',
            'reading_3_pulse' => 'nullable|integer|min:20|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate averages for all readings (including third if provided)
        $systolicReadings = array_filter([
            $request->reading_1_systolic,
            $request->reading_2_systolic,
            $request->reading_3_systolic
        ]);
        $diastolicReadings = array_filter([
            $request->reading_1_diastolic,
            $request->reading_2_diastolic,
            $request->reading_3_diastolic
        ]);
        $pulseReadings = array_filter([
            $request->reading_1_pulse,
            $request->reading_2_pulse,
            $request->reading_3_pulse
        ]);

        $avgSystolic = round(array_sum($systolicReadings) / count($systolicReadings));
        $avgDiastolic = round(array_sum($diastolicReadings) / count($diastolicReadings));
        $avgPulse = round(array_sum($pulseReadings) / count($pulseReadings));

        // Check if high reading (≥180/≥110)
        $isHighReading = $avgSystolic >= 180 || $avgDiastolic >= 110;
        $requiresUrgentAdvice = $avgSystolic >= 180 && $avgDiastolic >= 110;

        // Determine system response
        $systemResponse = $this->getSystemResponse($avgSystolic, $avgDiastolic, $request->reading_3_systolic, $request->reading_3_diastolic);
        $requiresThirdReading = $isHighReading && !$request->reading_3_systolic;

        // Determine reading category
        $readingCategory = $this->getReadingCategory($avgSystolic, $avgDiastolic);

        // If high reading and no third reading provided, return error
        if ($requiresThirdReading) {
            return response()->json([
                'status' => 'error',
                'message' => 'Third reading required due to high blood pressure',
                'data' => [
                    'system_response' => $systemResponse,
                    'requires_third_reading' => true,
                    'average_systolic' => $avgSystolic,
                    'average_diastolic' => $avgDiastolic
                ]
            ], 422);
        }

        // Create the reading
        $reading = BloodPressureReading::create([
            'patient_id' => $patient->id,
            'reading_date' => $request->reading_date,
            'session_type' => $request->session_type,
            'reading_1_systolic' => $request->reading_1_systolic,
            'reading_1_diastolic' => $request->reading_1_diastolic,
            'reading_1_pulse' => $request->reading_1_pulse,
            'reading_2_systolic' => $request->reading_2_systolic,
            'reading_2_diastolic' => $request->reading_2_diastolic,
            'reading_2_pulse' => $request->reading_2_pulse,
            'reading_3_systolic' => $request->reading_3_systolic,
            'reading_3_diastolic' => $request->reading_3_diastolic,
            'reading_3_pulse' => $request->reading_3_pulse,
            'average_systolic' => $avgSystolic,
            'average_diastolic' => $avgDiastolic,
            'average_pulse' => $avgPulse,
            'reading_category' => $readingCategory,
            'is_high_reading' => $isHighReading,
            'requires_urgent_advice' => $requiresUrgentAdvice,
            'system_response' => $systemResponse,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Blood pressure reading recorded successfully',
            'data' => [
                'reading' => $reading,
                'system_response' => $systemResponse,
                'requires_third_reading' => false,
                'average_systolic' => $reading->average_systolic,
                'average_diastolic' => $reading->average_diastolic,
                'average_pulse' => $reading->average_pulse,
                'reading_category' => $reading->reading_category
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/blood-pressure/readings",
     *     summary="Get blood pressure readings",
     *     tags={"Blood Pressure"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to retrieve readings for",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\Parameter(
     *         name="session_type",
     *         in="query",
     *         description="Filter by session type",
     *         @OA\Schema(type="string", enum={"am","pm"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blood pressure readings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="readings", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="seven_day_average", type="object"),
     *                 @OA\Property(property="am_average", type="object"),
     *                 @OA\Property(property="pm_average", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getReadings(Request $request): JsonResponse
    {
        $patient = $request->user();
        $days = $request->get('days', 7);
        $sessionType = $request->get('session_type');

        $query = $patient->bloodPressureReadings()
            ->where('reading_date', '>=', now()->subDays($days))
            ->orderBy('reading_date', 'desc');

        if ($sessionType) {
            $query->where('session_type', $sessionType);
        }

        $readings = $query->get();

        // Calculate averages
        $sevenDayAverage = $this->calculateSevenDayAverage($patient);
        $amAverage = $this->calculateSessionAverage($patient, 'am', $days);
        $pmAverage = $this->calculateSessionAverage($patient, 'pm', $days);

        return response()->json([
            'status' => 'success',
            'data' => [
                'readings' => $readings,
                'seven_day_average' => $sevenDayAverage,
                'am_average' => $amAverage,
                'pm_average' => $pmAverage,
                'total_readings' => $readings->count()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/blood-pressure/averages",
     *     summary="Get blood pressure averages and trends",
     *     tags={"Blood Pressure"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Time period for averages",
     *         @OA\Schema(type="string", enum={"7","30","90"}, example="7")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blood pressure averages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="overall_average", type="object"),
     *                 @OA\Property(property="am_average", type="object"),
     *                 @OA\Property(property="pm_average", type="object"),
     *                 @OA\Property(property="trend_data", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function getAverages(Request $request): JsonResponse
    {
        $patient = $request->user();
        $period = $request->get('period', 7);

        $overallAverage = $this->calculateSevenDayAverage($patient);
        $amAverage = $this->calculateSessionAverage($patient, 'am', $period);
        $pmAverage = $this->calculateSessionAverage($patient, 'pm', $period);
        $trendData = $this->getTrendData($patient, $period);

        return response()->json([
            'status' => 'success',
            'data' => [
                'overall_average' => $overallAverage,
                'am_average' => $amAverage,
                'pm_average' => $pmAverage,
                'trend_data' => $trendData,
                'period_days' => $period
            ]
        ]);
    }

    /**
     * Get reading category based on NICE guidelines
     */
    private function getReadingCategory(int $systolic, int $diastolic): string
    {
        if ($systolic >= 180 || $diastolic >= 110) {
            return 'Hypertensive Crisis';
        } elseif ($systolic >= 160 || $diastolic >= 100) {
            return 'Stage 2 Hypertension';
        } elseif ($systolic >= 140 || $diastolic >= 90) {
            return 'Stage 1 Hypertension';
        } elseif ($systolic >= 135 || $diastolic >= 85) {
            return 'High Normal';
        } elseif ($systolic >= 120 || $diastolic >= 80) {
            return 'Normal';
        } else {
            return 'Optimal';
        }
    }

    /**
     * Get system response based on blood pressure readings
     */
    private function getSystemResponse(int $systolic, int $diastolic, ?int $thirdSystolic, ?int $thirdDiastolic): string
    {
        // If third reading is provided, use it for final assessment
        if ($thirdSystolic && $thirdDiastolic) {
            $finalSystolic = round(($systolic + $thirdSystolic) / 2);
            $finalDiastolic = round(($diastolic + $thirdDiastolic) / 2);
            
            if ($finalSystolic >= 180 || $finalDiastolic >= 110) {
                return "Your blood pressure remains very high. Please contact NHS 111, your GP, or attend your nearest A&E for urgent advice.";
            } else {
                return "Thank you for submitting today's reading.";
            }
        }

        // Initial assessment
        if ($systolic >= 180 || $diastolic >= 110) {
            return "Please wait 5 minutes and recheck (Reading 3).";
        }

        return "Thank you for submitting today's reading.";
    }

    /**
     * Calculate 7-day average excluding day 1 (NICE guidelines)
     */
    private function calculateSevenDayAverage($patient): ?array
    {
        $readings = $patient->bloodPressureReadings()
            ->where('reading_date', '>=', now()->subDays(7))
            ->orderBy('reading_date', 'asc')
            ->get();

        if ($readings->count() < 4) {
            return null;
        }

        // Remove day 1 readings
        $day1Date = $readings->first()->reading_date->format('Y-m-d');
        $filteredReadings = $readings->filter(function ($reading) use ($day1Date) {
            return $reading->reading_date->format('Y-m-d') !== $day1Date;
        });

        if ($filteredReadings->count() < 3) {
            return null;
        }

        return [
            'systolic' => round($filteredReadings->avg('average_systolic')),
            'diastolic' => round($filteredReadings->avg('average_diastolic')),
            'pulse' => round($filteredReadings->avg('average_pulse')),
            'total_readings' => $filteredReadings->count(),
            'days_with_readings' => $filteredReadings->groupBy(function ($reading) {
                return $reading->reading_date->format('Y-m-d');
            })->count()
        ];
    }

    /**
     * Calculate average for specific session type
     */
    private function calculateSessionAverage($patient, string $sessionType, int $days): ?array
    {
        $readings = $patient->bloodPressureReadings()
            ->where('session_type', $sessionType)
            ->where('reading_date', '>=', now()->subDays($days))
            ->get();

        if ($readings->count() < 2) {
            return null;
        }

        return [
            'systolic' => round($readings->avg('average_systolic')),
            'diastolic' => round($readings->avg('average_diastolic')),
            'pulse' => round($readings->avg('average_pulse')),
            'total_readings' => $readings->count()
        ];
    }

    /**
     * Get trend data for graphing
     */
    private function getTrendData($patient, int $days): array
    {
        $readings = $patient->bloodPressureReadings()
            ->where('reading_date', '>=', now()->subDays($days))
            ->orderBy('reading_date', 'asc')
            ->get();

        $trendData = [];
        $groupedReadings = $readings->groupBy(function ($reading) {
            return $reading->reading_date->format('Y-m-d');
        });

        foreach ($groupedReadings as $date => $dayReadings) {
            $trendData[] = [
                'date' => $date,
                'systolic' => round($dayReadings->avg('average_systolic')),
                'diastolic' => round($dayReadings->avg('average_diastolic')),
                'pulse' => round($dayReadings->avg('average_pulse')),
                'readings_count' => $dayReadings->count()
            ];
        }

        return $trendData;
    }
}