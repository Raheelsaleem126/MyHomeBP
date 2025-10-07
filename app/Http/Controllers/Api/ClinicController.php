<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Clinic",
 *     type="object",
 *     title="Clinic",
 *     description="Clinic model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="NHS Health Centre"),
 *     @OA\Property(property="address", type="string", example="123 Main Street, London"),
 *     @OA\Property(property="postcode", type="string", example="SW1A 1AA"),
 *     @OA\Property(property="phone", type="string", example="+44 20 7123 4567"),
 *     @OA\Property(property="email", type="string", format="email", example="info@clinic.com"),
 *     @OA\Property(property="type", type="string", example="NHS"),
 *     @OA\Property(property="latitude", type="number", format="float", example=51.5074),
 *     @OA\Property(property="longitude", type="number", format="float", example=-0.1278),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="doctors_count", type="integer", example=10),
 *     @OA\Property(property="patients_count", type="integer", example=500),
 *     @OA\Property(property="doctors", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="patients", type="array", @OA\Items(type="object"))
 * )
 */

/**
 * @OA\Tag(
 *     name="Clinic",
 *     description="Clinic search and management"
 * )
 */
class ClinicController extends Controller
{
    /**
     * @OA\Get(
     *     path="/clinics/search",
     *     summary="Search clinics",
     *     tags={"Clinic"},
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         description="Search by postcode",
     *         @OA\Schema(type="string", example="SW1A 1AA")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search by clinic name",
     *         @OA\Schema(type="string", example="NHS Health Centre")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by clinic type",
     *         @OA\Schema(type="string", enum={"NHS","Private","Mixed"})
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results to return",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinics", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total", type="integer", example=5)
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'postcode' => 'nullable|string|max:10',
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|in:NHS,Private,Mixed',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Clinic::active();

        if ($request->postcode) {
            $query->byPostcode($request->postcode);
        }

        if ($request->name) {
            $query->byName($request->name);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $limit = $request->get('limit', 10);
        $clinics = $query->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'clinics' => $clinics,
                'total' => $clinics->count()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/clinics/{id}",
     *     summary="Get clinic details",
     *     tags={"Clinic"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Clinic ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinic details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinic", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Clinic not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Clinic not found")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $clinic = Clinic::active()->find($id);

        if (!$clinic) {
            return response()->json([
                'status' => 'error',
                'message' => 'Clinic not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'clinic' => $clinic
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/clinics/nearby",
     *     summary="Find nearby clinics",
     *     tags={"Clinic"},
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         required=true,
     *         description="Postcode to search from",
     *         @OA\Schema(type="string", example="SW1A 1AA")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search radius in miles",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results to return",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nearby clinics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinics", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total", type="integer", example=3)
     *             )
     *         )
     *     )
     * )
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'postcode' => 'required|string|max:10',
            'radius' => 'nullable|integer|min:1|max:50',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // For now, we'll do a simple postcode-based search
        // In a real implementation, you'd use a geocoding service to get coordinates
        // and calculate distances using the Haversine formula
        $query = Clinic::active()->byPostcode($request->postcode);

        $limit = $request->get('limit', 5);
        $clinics = $query->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'clinics' => $clinics,
                'total' => $clinics->count(),
                'search_postcode' => $request->postcode,
                'radius_miles' => $request->get('radius', 10)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/clinics",
     *     summary="Get all clinics",
     *     tags={"Clinic"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by clinic type",
     *         @OA\Schema(type="string", enum={"NHS","Private","Mixed"})
     *     ),
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
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clinics", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:NHS,Private,Mixed',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Clinic::active();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 20);
        $clinics = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'clinics' => $clinics->items(),
                'pagination' => [
                    'current_page' => $clinics->currentPage(),
                    'last_page' => $clinics->lastPage(),
                    'per_page' => $clinics->perPage(),
                    'total' => $clinics->total(),
                    'from' => $clinics->firstItem(),
                    'to' => $clinics->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/clinics/{id}/doctors",
     *     summary="Get doctors by clinic",
     *     tags={"Clinic"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Clinic ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctors retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="doctors", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Clinic not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Clinic not found")
     *         )
     *     )
     * )
     */
    public function doctors($id): JsonResponse
    {
        $clinic = Clinic::find($id);

        if (!$clinic) {
            return response()->json([
                'status' => 'error',
                'message' => 'Clinic not found'
            ], 404);
        }

        $doctors = $clinic->doctors()
            ->wherePivot('status', 'active')
            ->active()
            ->available()
            ->with('specialities')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'full_name' => $doctor->full_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'gmc_number' => $doctor->gmc_number,
                    'specialities' => $doctor->specialities->map(function ($speciality) {
                        return [
                            'id' => $speciality->id,
                            'name' => $speciality->name,
                            'description' => $speciality->description,
                        ];
                    }),
                    'years_of_experience' => $doctor->years_of_experience,
                    'bio' => $doctor->bio,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'doctors' => $doctors,
                'total' => $doctors->count()
            ]
        ]);
    }
}