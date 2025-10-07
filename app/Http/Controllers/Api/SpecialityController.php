<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\SpecialityResource;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Speciality",
 *     type="object",
 *     title="Speciality",
 *     description="Medical speciality model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cardiology"),
 *     @OA\Property(property="description", type="string", example="Heart and cardiovascular system specialist"),
 *     @OA\Property(property="code", type="string", example="CARD"),
 *     @OA\Property(property="full_name", type="string", example="Cardiology (CARD)"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="doctors_count", type="integer", example=5),
 *     @OA\Property(property="doctors", type="array", @OA\Items(type="object"))
 * )
 */
class SpecialityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/specialities",
     *     summary="Get all specialities",
     *     description="Retrieve a paginated list of all medical specialities with optional filtering",
     *     tags={"Specialities"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by speciality name",
     *         required=false,
     *         @OA\Schema(type="string", example="Cardiology")
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relationships (doctors)",
     *         required=false,
     *         @OA\Schema(type="string", example="doctors")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Speciality")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Speciality::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->byName($request->search);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Include relationships
        $includes = $request->get('include', '');
        if ($includes) {
            $relations = array_filter(explode(',', $includes));
            $query->with($relations);
        }

        $specialities = $query->paginate($request->get('per_page', 15));

        return response()->json(SpecialityResource::collection($specialities));
    }

    /**
     * @OA\Post(
     *     path="/admin/specialities",
     *     summary="Create a new speciality",
     *     description="Create a new medical speciality",
     *     tags={"Specialities"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Cardiology"),
     *             @OA\Property(property="description", type="string", example="Heart and cardiovascular system specialist"),
     *             @OA\Property(property="code", type="string", example="CARD"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Speciality created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Speciality")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialities,name',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:10|unique:specialities,code',
            'is_active' => 'boolean',
        ]);

        $speciality = Speciality::create($validated);

        return response()->json(new SpecialityResource($speciality), 201);
    }

    /**
     * @OA\Get(
     *     path="/specialities/{id}",
     *     summary="Get speciality by ID",
     *     description="Retrieve a specific speciality by ID with optional relationships",
     *     tags={"Specialities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Speciality ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relationships (doctors)",
     *         required=false,
     *         @OA\Schema(type="string", example="doctors")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Speciality")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Speciality not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        $speciality = Speciality::find($id);
        
        if (!$speciality) {
            return response()->json(['error' => 'Speciality not found', 'id' => $id], 404);
        }

        $includes = $request->get('include', '');
        if ($includes) {
            $relations = array_filter(explode(',', $includes));
            $speciality->load($relations);
        }

        return response()->json(new SpecialityResource($speciality));
    }

    /**
     * @OA\Put(
     *     path="/admin/specialities/{id}",
     *     summary="Update speciality",
     *     description="Update an existing speciality's information",
     *     tags={"Specialities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Speciality ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Cardiology"),
     *             @OA\Property(property="description", type="string", example="Heart and cardiovascular system specialist"),
     *             @OA\Property(property="code", type="string", example="CARD"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Speciality updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Speciality")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Speciality not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $speciality = Speciality::find($id);
        
        if (!$speciality) {
            return response()->json(['error' => 'Speciality not found', 'id' => $id], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:specialities,name,' . $speciality->id,
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:10|unique:specialities,code,' . $speciality->id,
            'is_active' => 'boolean',
        ]);

        $speciality->update($validated);

        return response()->json(new SpecialityResource($speciality));
    }

    /**
     * @OA\Delete(
     *     path="/admin/specialities/{id}",
     *     summary="Delete speciality",
     *     description="Delete a speciality (soft delete by setting is_active to false)",
     *     tags={"Specialities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Speciality ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Speciality deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Speciality deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Speciality not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $speciality = Speciality::find($id);
        
        if (!$speciality) {
            return response()->json(['error' => 'Speciality not found', 'id' => $id], 404);
        }

        // Soft delete by setting is_active to false
        $speciality->update(['is_active' => false]);

        return response()->json(['message' => 'Speciality deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/specialities/{id}/doctors",
     *     summary="Get doctors by speciality",
     *     description="Retrieve all doctors who have a specific speciality",
     *     tags={"Specialities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Speciality ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="is_primary",
     *         in="query",
     *         description="Filter by primary speciality",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Doctor")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function doctors(Request $request, $id): JsonResponse
    {
        $speciality = Speciality::find($id);
        
        if (!$speciality) {
            return response()->json(['error' => 'Speciality not found', 'id' => $id], 404);
        }

        $query = $speciality->doctors();

        if ($request->filled('is_primary')) {
            $query->wherePivot('is_primary', $request->boolean('is_primary'));
        }

        $doctors = $query->paginate($request->get('per_page', 15));

        return response()->json(DoctorResource::collection($doctors));
    }
}