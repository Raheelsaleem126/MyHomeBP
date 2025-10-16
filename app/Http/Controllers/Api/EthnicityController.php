<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EthnicityMainCategory;
use App\Models\EthnicitySubcategory;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Ethnicity",
 *     description="UK ONS ethnicity categories and subcategories"
 * )
 */
class EthnicityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ethnicity/categories",
     *     summary="Get all main ethnicity categories",
     *     tags={"Ethnicity"},
     *     @OA\Response(
     *         response=200,
     *         description="Main ethnicity categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = EthnicityMainCategory::active()
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity/categories/{id}",
     *     summary="Get main ethnicity category by ID",
     *     tags={"Ethnicity"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Main category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Main ethnicity category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Main ethnicity category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Main ethnicity category not found")
     *         )
     *     )
     * )
     */
    public function showCategory(int $id): JsonResponse
    {
        $category = EthnicityMainCategory::active()->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Main ethnicity category not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity/subcategories",
     *     summary="Get ethnicity subcategories",
     *     tags={"Ethnicity"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by main category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ethnicity subcategories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="subcategories", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function subcategories(): JsonResponse
    {
        $query = EthnicitySubcategory::active()->with('mainCategory')->ordered();

        // Filter by main category if provided
        if (request()->has('category_id')) {
            $query->byMainCategory(request('category_id'));
        }

        $subcategories = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'subcategories' => $subcategories
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity/subcategories/{id}",
     *     summary="Get ethnicity subcategory by ID",
     *     tags={"Ethnicity"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Subcategory ID",
     *         required=true,
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ethnicity subcategory retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="subcategory", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ethnicity subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Ethnicity subcategory not found")
     *         )
     *     )
     * )
     */
    public function showSubcategory(int $id): JsonResponse
    {
        $subcategory = EthnicitySubcategory::active()->with('mainCategory')->find($id);

        if (!$subcategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ethnicity subcategory not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'subcategory' => $subcategory
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity/categories/{categoryId}/subcategories",
     *     summary="Get subcategories for a specific main category",
     *     tags={"Ethnicity"},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="Main category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ethnicity subcategories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="subcategories", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function categorySubcategories(int $categoryId): JsonResponse
    {
        $subcategories = EthnicitySubcategory::active()
            ->byMainCategory($categoryId)
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'subcategories' => $subcategories
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/ethnicity/hierarchy",
     *     summary="Get complete ethnicity hierarchy (categories with their subcategories)",
     *     tags={"Ethnicity"},
     *     @OA\Response(
     *         response=200,
     *         description="Complete ethnicity hierarchy retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="hierarchy", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function hierarchy(): JsonResponse
    {
        $categories = EthnicityMainCategory::active()
            ->with(['subcategories' => function($query) {
                $query->active()->ordered();
            }])
            ->ordered()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'hierarchy' => $categories
            ]
        ]);
    }
}