<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comorbidity;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Comorbidities",
 *     description="Medical comorbidities for patient clinical data"
 * )
 */
class ComorbidityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/comorbidities",
     *     summary="Get all comorbidities",
     *     tags={"Comorbidities"},
     *     @OA\Response(
     *         response=200,
     *         description="Comorbidities retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="comorbidities", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $comorbidities = Comorbidity::active()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'comorbidities' => $comorbidities
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/comorbidities/{id}",
     *     summary="Get comorbidity by ID",
     *     tags={"Comorbidities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Comorbidity ID",
     *         required=true,
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comorbidity retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="comorbidity", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comorbidity not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Comorbidity not found")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $comorbidity = Comorbidity::active()->find($id);

        if (!$comorbidity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comorbidity not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'comorbidity' => $comorbidity
            ]
        ]);
    }
}
