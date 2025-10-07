<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Medications",
 *     description="BNF-linked medications"
 * )
 */
class MedicationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/medications",
     *     summary="Get all medications",
     *     tags={"Medications"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search medications by name",
     *         required=false,
     *         @OA\Schema(type="string", example="amlodipine")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medications retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="medications", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total", type="integer", example=18)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Medication::active();

        if ($request->search) {
            $query->search($request->search);
        }

        $medications = $query->orderBy('generic_name')
            ->orderBy('strength')
            ->get()
            ->map(function ($medication) {
                return [
                    'id' => $medication->id,
                    'bnf_code' => $medication->bnf_code,
                    'generic_name' => $medication->generic_name,
                    'brand_name' => $medication->brand_name,
                    'form' => $medication->form,
                    'strength' => $medication->strength,
                    'description' => $medication->description,
                    'display_name' => $medication->display_name,
                    'full_name' => $medication->full_name,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'medications' => $medications,
                'total' => $medications->count()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/medications/{id}",
     *     summary="Get medication by ID",
     *     tags={"Medications"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Medication ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="medication", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Medication not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Medication not found")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $medication = Medication::find($id);

        if (!$medication) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medication not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'medication' => [
                    'id' => $medication->id,
                    'bnf_code' => $medication->bnf_code,
                    'generic_name' => $medication->generic_name,
                    'brand_name' => $medication->brand_name,
                    'form' => $medication->form,
                    'strength' => $medication->strength,
                    'description' => $medication->description,
                    'display_name' => $medication->display_name,
                    'full_name' => $medication->full_name,
                    'dose_options' => $medication->dose_options,
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/medications/frequency-options",
     *     summary="Get medication frequency options",
     *     tags={"Medications"},
     *     @OA\Response(
     *         response=200,
     *         description="Frequency options retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="frequency_options", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function frequencyOptions(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'frequency_options' => Medication::getFrequencyOptions()
            ]
        ]);
    }
}