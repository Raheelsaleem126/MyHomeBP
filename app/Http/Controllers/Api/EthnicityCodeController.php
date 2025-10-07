<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EthnicityCode;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Ethnicity Codes",
 *     description="UK ONS ethnicity codes"
 * )
 */
class EthnicityCodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ethnicity-codes",
     *     summary="Get all ethnicity codes",
     *     tags={"Ethnicity Codes"},
     *     @OA\Response(
     *         response=200,
     *         description="Ethnicity codes retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ethnicity_codes", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="grouped_by_category", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $ethnicityCodes = EthnicityCode::active()
            ->orderBy('category')
            ->orderBy('description')
            ->get();

        $groupedByCategory = EthnicityCode::getGroupedByCategory();

        return response()->json([
            'status' => 'success',
            'data' => [
                'ethnicity_codes' => $ethnicityCodes,
                'grouped_by_category' => $groupedByCategory
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity-codes/{code}",
     *     summary="Get ethnicity code by code",
     *     tags={"Ethnicity Codes"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Ethnicity code",
     *         required=true,
     *         @OA\Schema(type="string", example="A")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ethnicity code retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ethnicity_code", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ethnicity code not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Ethnicity code not found")
     *         )
     *     )
     * )
     */
    public function show(string $code): JsonResponse
    {
        $ethnicityCode = EthnicityCode::where('code', $code)->first();

        if (!$ethnicityCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ethnicity code not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'ethnicity_code' => $ethnicityCode
            ]
        ]);
    }
}