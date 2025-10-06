<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\BloodPressureReading;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ReportService
{
    /**
     * Generate a blood pressure report for a patient
     */
    public function generateReport(
        Patient $patient,
        Carbon $startDate,
        Carbon $endDate,
        bool $includeClinicalData = true,
        bool $emailToClinic = false
    ): array {
        // Get readings for the period
        $readings = $patient->bloodPressureReadings()
            ->whereBetween('reading_date', [$startDate, $endDate])
            ->orderBy('reading_date', 'asc')
            ->get();

        // Calculate averages and statistics
        $summary = $this->getReportSummary($patient, $readings, $startDate, $endDate);

        // Generate PDF
        $pdfData = $this->generatePdfData($patient, $readings, $summary, $includeClinicalData);
        $filename = $this->generatePdfFilename($patient, $startDate, $endDate);
        $pdfPath = $this->savePdf($pdfData, $filename);

        $result = [
            'filename' => $filename,
            'pdf_url' => '/api/reports/download/' . $filename,
            'summary' => $summary,
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'email_sent' => false
        ];

        // Send email to clinic if requested
        if ($emailToClinic && $patient->clinic && $patient->clinic->email) {
            $result['email_sent'] = $this->sendReportToClinic($patient, $pdfPath, $summary);
        }

        return $result;
    }

    /**
     * Get report summary with all necessary statistics
     */
    public function getReportSummary(Patient $patient, Collection $readings, Carbon $startDate, Carbon $endDate): array
    {
        if ($readings->isEmpty()) {
            return [
                'total_readings' => 0,
                'days_with_readings' => 0,
                'overall_average' => null,
                'am_average' => null,
                'pm_average' => null,
                'highest_reading' => null,
                'lowest_reading' => null,
                'high_readings_count' => 0,
                'urgent_readings_count' => 0,
                'compliance_status' => 'insufficient_data'
            ];
        }

        // Calculate overall average (excluding day 1 as per NICE guidelines)
        $overallAverage = $this->calculateOverallAverage($readings);
        
        // Calculate AM and PM averages
        $amAverage = $this->calculateSessionAverage($readings, 'am');
        $pmAverage = $this->calculateSessionAverage($readings, 'pm');

        // Find highest and lowest readings
        $highestReading = $readings->sortByDesc('average_systolic')->first();
        $lowestReading = $readings->sortBy('average_systolic')->first();

        // Count high and urgent readings
        $highReadingsCount = $readings->where('is_high_reading', true)->count();
        $urgentReadingsCount = $readings->where('requires_urgent_advice', true)->count();

        // Calculate compliance status
        $complianceStatus = $this->calculateComplianceStatus($readings, $startDate, $endDate);

        return [
            'total_readings' => $readings->count(),
            'days_with_readings' => $readings->groupBy(function ($reading) {
                return $reading->reading_date->format('Y-m-d');
            })->count(),
            'overall_average' => $overallAverage,
            'am_average' => $amAverage,
            'pm_average' => $pmAverage,
            'highest_reading' => $highestReading ? [
                'date' => $highestReading->reading_date->format('Y-m-d H:i'),
                'systolic' => $highestReading->average_systolic,
                'diastolic' => $highestReading->average_diastolic,
                'pulse' => $highestReading->average_pulse,
                'session_type' => $highestReading->session_type
            ] : null,
            'lowest_reading' => $lowestReading ? [
                'date' => $lowestReading->reading_date->format('Y-m-d H:i'),
                'systolic' => $lowestReading->average_systolic,
                'diastolic' => $lowestReading->average_diastolic,
                'pulse' => $lowestReading->average_pulse,
                'session_type' => $lowestReading->session_type
            ] : null,
            'high_readings_count' => $highReadingsCount,
            'urgent_readings_count' => $urgentReadingsCount,
            'compliance_status' => $complianceStatus,
            'nice_guidelines_compliant' => $this->isNiceGuidelinesCompliant($readings)
        ];
    }

    /**
     * Calculate overall average excluding day 1 (NICE guidelines)
     */
    private function calculateOverallAverage(Collection $readings): ?array
    {
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
            'readings_count' => $filteredReadings->count()
        ];
    }

    /**
     * Calculate average for specific session type
     */
    private function calculateSessionAverage(Collection $readings, string $sessionType): ?array
    {
        $sessionReadings = $readings->where('session_type', $sessionType);

        if ($sessionReadings->count() < 2) {
            return null;
        }

        return [
            'systolic' => round($sessionReadings->avg('average_systolic')),
            'diastolic' => round($sessionReadings->avg('average_diastolic')),
            'pulse' => round($sessionReadings->avg('average_pulse')),
            'readings_count' => $sessionReadings->count()
        ];
    }

    /**
     * Calculate compliance status
     */
    private function calculateComplianceStatus(Collection $readings, Carbon $startDate, Carbon $endDate): string
    {
        $expectedDays = $startDate->diffInDays($endDate) + 1;
        $actualDays = $readings->groupBy(function ($reading) {
            return $reading->reading_date->format('Y-m-d');
        })->count();

        $compliancePercentage = ($actualDays / $expectedDays) * 100;

        if ($compliancePercentage >= 80) {
            return 'excellent';
        } elseif ($compliancePercentage >= 60) {
            return 'good';
        } elseif ($compliancePercentage >= 40) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Check if readings comply with NICE guidelines
     */
    private function isNiceGuidelinesCompliant(Collection $readings): bool
    {
        // NICE guidelines require:
        // - At least 4 days of readings
        // - 2 readings per session (AM and PM)
        // - Day 1 readings excluded from average

        if ($readings->count() < 8) { // Minimum 4 days × 2 readings per day
            return false;
        }

        $daysWithReadings = $readings->groupBy(function ($reading) {
            return $reading->reading_date->format('Y-m-d');
        })->count();

        return $daysWithReadings >= 4;
    }

    /**
     * Generate PDF data
     */
    private function generatePdfData(Patient $patient, Collection $readings, array $summary, bool $includeClinicalData): array
    {
        return [
            'patient' => $patient->load('clinic'),
            'clinical_data' => $includeClinicalData ? $patient->clinicalData : null,
            'readings' => $readings,
            'summary' => $summary,
            'generated_at' => now(),
            'report_period' => [
                'start_date' => $readings->first()?->reading_date ?? now(),
                'end_date' => $readings->last()?->reading_date ?? now()
            ]
        ];
    }

    /**
     * Generate PDF filename
     */
    private function generatePdfFilename(Patient $patient, Carbon $startDate, Carbon $endDate): string
    {
        $patientId = $patient->id;
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');
        
        return "bp_report_{$patientId}_{$startDateStr}_to_{$endDateStr}.pdf";
    }

    /**
     * Save PDF to storage
     */
    private function savePdf(array $data, string $filename): string
    {
        // Ensure reports directory exists
        Storage::makeDirectory('reports');

        // Generate PDF
        $pdf = Pdf::loadView('reports.blood-pressure', $data);
        $pdf->setPaper('A4', 'portrait');

        // Save to storage
        $filePath = storage_path('app/reports/' . $filename);
        $pdf->save($filePath);

        return $filePath;
    }

    /**
     * Send report to clinic via email
     */
    private function sendReportToClinic(Patient $patient, string $pdfPath, array $summary): bool
    {
        try {
            $clinic = $patient->clinic;
            $subject = "Blood Pressure Report - {$patient->full_name}";
            
            $emailData = [
                'patient' => $patient,
                'clinic' => $clinic,
                'summary' => $summary,
                'report_period' => $summary['period'] ?? null
            ];

            Mail::send('emails.bp-report', $emailData, function ($message) use ($clinic, $subject, $pdfPath, $patient) {
                $message->to($clinic->email)
                    ->subject($subject)
                    ->attach($pdfPath, [
                        'as' => "BP_Report_{$patient->surname}_{$patient->first_name}.pdf",
                        'mime' => 'application/pdf'
                    ]);
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send BP report email: ' . $e->getMessage());
            return false;
        }
    }
}
