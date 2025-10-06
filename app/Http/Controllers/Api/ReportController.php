<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="Blood pressure report generation and management"
 * )
 */
class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @OA\Post(
     *     path="/reports/generate",
     *     summary="Generate blood pressure report",
     *     tags={"Reports"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-01-07"),
     *             @OA\Property(property="include_clinical_data", type="boolean", example=true),
     *             @OA\Property(property="email_to_clinic", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Report generated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="report", type="object"),
     *                 @OA\Property(property="pdf_url", type="string", example="/storage/reports/report_123.pdf"),
     *                 @OA\Property(property="email_sent", type="boolean", example=true)
     *             )
     *         )
     *     )
     * )
     */
    public function generateReport(Request $request): JsonResponse
    {
        $patient = $request->user();

        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today',
            'include_clinical_data' => 'nullable|boolean',
            'email_to_clinic' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Default to last 7 days if no dates provided
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subDays(7);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        // Check if patient has enough readings (minimum 4 days as per NICE guidelines)
        $readingsCount = $patient->bloodPressureReadings()
            ->whereBetween('reading_date', [$startDate, $endDate])
            ->count();

        if ($readingsCount < 4) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient readings. Please complete at least 4 days of readings before generating a report.',
                'data' => [
                    'current_readings' => $readingsCount,
                    'minimum_required' => 4
                ]
            ], 422);
        }

        try {
            $report = $this->reportService->generateReport(
                $patient,
                $startDate,
                $endDate,
                $request->include_clinical_data ?? true,
                $request->email_to_clinic ?? false
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Report generated successfully',
                'data' => [
                    'report' => $report,
                    'pdf_url' => $report['pdf_url'] ?? null,
                    'email_sent' => $report['email_sent'] ?? false,
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'days' => $startDate->diffInDays($endDate) + 1
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/summary",
     *     summary="Get report summary",
     *     tags={"Reports"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to include in summary",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report summary retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="summary", type="object"),
     *                 @OA\Property(property="can_generate_report", type="boolean", example=true),
     *                 @OA\Property(property="readings_count", type="integer", example=14)
     *             )
     *         )
     *     )
     * )
     */
    public function getReportSummary(Request $request): JsonResponse
    {
        $patient = $request->user();
        $days = $request->get('days', 7);

        $startDate = now()->subDays($days);
        $endDate = now();

        $readings = $patient->bloodPressureReadings()
            ->whereBetween('reading_date', [$startDate, $endDate])
            ->orderBy('reading_date', 'asc')
            ->get();

        $summary = $this->reportService->getReportSummary($patient, $readings, $startDate, $endDate);

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => $summary,
                'can_generate_report' => $readings->count() >= 4,
                'readings_count' => $readings->count(),
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'days' => $days
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/download/{filename}",
     *     summary="Download report PDF",
     *     tags={"Reports"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         required=true,
     *         description="Report filename",
     *         @OA\Schema(type="string", example="report_123.pdf")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report downloaded successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Report not found")
     *         )
     *     )
     * )
     */
    public function downloadReport(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filePath = storage_path('app/reports/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'Report not found');
        }

        return response()->download($filePath, $filename);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/history",
     *     summary="Get report history",
     *     tags={"Reports"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="reports", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getReportHistory(Request $request): JsonResponse
    {
        $patient = $request->user();
        $perPage = $request->get('per_page', 10);

        // In a real implementation, you'd have a reports table to track generated reports
        // For now, we'll return a placeholder response
        $reports = collect([
            [
                'id' => 1,
                'generated_at' => now()->subDays(1)->format('Y-m-d H:i:s'),
                'period_start' => now()->subDays(7)->format('Y-m-d'),
                'period_end' => now()->subDays(1)->format('Y-m-d'),
                'readings_count' => 14,
                'average_systolic' => 125,
                'average_diastolic' => 82,
                'status' => 'completed',
                'pdf_url' => '/api/reports/download/report_1.pdf'
            ]
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'reports' => $reports,
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => $reports->count(),
                    'from' => 1,
                    'to' => $reports->count(),
                ]
            ]
        ]);
    }
}