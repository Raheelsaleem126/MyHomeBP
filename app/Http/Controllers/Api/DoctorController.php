<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Doctor",
 *     type="object",
 *     title="Doctor",
 *     description="Doctor model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="Dr. Sarah"),
 *     @OA\Property(property="last_name", type="string", example="Johnson"),
 *     @OA\Property(property="full_name", type="string", example="Dr. Sarah Johnson"),
 *     @OA\Property(property="email", type="string", format="email", example="sarah.johnson@example.com"),
 *     @OA\Property(property="phone", type="string", example="+44 20 7123 4567"),
 *     @OA\Property(property="gmc_number", type="string", example="GMC123456"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1980-05-15"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
 *     @OA\Property(property="qualifications", type="string", example="MBBS, MRCP, PhD in Cardiology"),
 *     @OA\Property(property="years_of_experience", type="integer", example=15),
 *     @OA\Property(property="bio", type="string", example="Experienced cardiologist..."),
 *     @OA\Property(property="profile_image", type="string", example="path/to/image.jpg"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_available", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="specialities", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="primary_speciality", type="object"),
 *     @OA\Property(property="clinics", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="patients_count", type="integer", example=25)
 * )
 */
class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/doctors",
     *     summary="Get all doctors",
     *     description="Retrieve a paginated list of all doctors with optional filtering",
     *     tags={"Doctors"},
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
     *         description="Search by doctor name",
     *         required=false,
     *         @OA\Schema(type="string", example="Dr. Sarah")
     *     ),
     *     @OA\Parameter(
     *         name="speciality_id",
     *         in="query",
     *         description="Filter by speciality ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="clinic_id",
     *         in="query",
     *         description="Filter by clinic ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="is_available",
     *         in="query",
     *         description="Filter by availability",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relationships (specialities,clinics,primarySpeciality)",
     *         required=false,
     *         @OA\Schema(type="string", example="specialities,clinics")
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
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->byName($request->search);
        }

        if ($request->filled('speciality_id')) {
            $query->bySpeciality($request->speciality_id);
        }

        if ($request->filled('clinic_id')) {
            $query->byClinic($request->clinic_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Include relationships
        $includes = $request->get('include', '');
        if ($includes) {
            $relations = array_filter(explode(',', $includes));
            $query->with($relations);
        }

        $doctors = $query->paginate($request->get('per_page', 15));

        return response()->json(DoctorResource::collection($doctors));
    }

    /**
     * @OA\Post(
     *     path="/admin/doctors",
     *     summary="Create a new doctor",
     *     description="Create a new doctor with specialities and clinic assignments",
     *     tags={"Doctors"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "gmc_number"},
     *             @OA\Property(property="first_name", type="string", example="Dr. Sarah"),
     *             @OA\Property(property="last_name", type="string", example="Johnson"),
     *             @OA\Property(property="email", type="string", format="email", example="sarah.johnson@example.com"),
     *             @OA\Property(property="phone", type="string", example="+44 20 7123 4567"),
     *             @OA\Property(property="gmc_number", type="string", example="GMC123456"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1980-05-15"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
     *             @OA\Property(property="qualifications", type="string", example="MBBS, MRCP, PhD in Cardiology"),
     *             @OA\Property(property="years_of_experience", type="integer", example=15),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist..."),
     *             @OA\Property(property="profile_image", type="string", example="path/to/image.jpg"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="is_available", type="boolean", example=true),
     *             @OA\Property(property="speciality_ids", type="array", @OA\Items(type="integer"), example={1, 2}),
     *             @OA\Property(property="clinic_ids", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Doctor created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Doctor")
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'phone' => 'nullable|string|max:20',
            'gmc_number' => 'required|string|unique:doctors,gmc_number',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'speciality_ids' => 'array',
            'speciality_ids.*' => 'exists:specialities,id',
            'clinic_ids' => 'array',
            'clinic_ids.*' => 'exists:clinics,id',
        ]);

        $doctorData = collect($validated)->except(['speciality_ids', 'clinic_ids'])->toArray();
        $doctor = Doctor::create($doctorData);

        // Attach specialities
        if (isset($validated['speciality_ids'])) {
            $specialityData = [];
            foreach ($validated['speciality_ids'] as $index => $specialityId) {
                $specialityData[$specialityId] = [
                    'certification_date' => now()->subYears(rand(1, 10)),
                    'certification_body' => 'GMC',
                    'is_primary' => $index === 0, // First speciality is primary
                ];
            }
            $doctor->specialities()->attach($specialityData);
        }

        // Attach clinics
        if (isset($validated['clinic_ids'])) {
            $clinicData = [];
            foreach ($validated['clinic_ids'] as $clinicId) {
                $clinicData[$clinicId] = [
                    'start_date' => now(),
                    'status' => 'active',
                ];
            }
            $doctor->clinics()->attach($clinicData);
        }

        $doctor->load(['specialities', 'clinics']);

        return response()->json(new DoctorResource($doctor), 201);
    }

    /**
     * @OA\Get(
     *     path="/doctors/{id}",
     *     summary="Get doctor by ID",
     *     description="Retrieve a specific doctor by ID with optional relationships",
     *     tags={"Doctors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relationships (specialities,clinics,primarySpeciality)",
     *         required=false,
     *         @OA\Schema(type="string", example="specialities,clinics")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Doctor")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found"
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        $doctor = Doctor::find($id);
        
        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found', 'id' => $id], 404);
        }

        $includes = $request->get('include', '');
        if ($includes) {
            $relations = array_filter(explode(',', $includes));
            $doctor->load($relations);
        }

        return response()->json(new DoctorResource($doctor));
    }

    /**
     * @OA\Put(
     *     path="/admin/doctors/{id}",
     *     summary="Update doctor",
     *     description="Update an existing doctor's information",
     *     tags={"Doctors"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="Dr. Sarah"),
     *             @OA\Property(property="last_name", type="string", example="Johnson"),
     *             @OA\Property(property="email", type="string", format="email", example="sarah.johnson@example.com"),
     *             @OA\Property(property="phone", type="string", example="+44 20 7123 4567"),
     *             @OA\Property(property="gmc_number", type="string", example="GMC123456"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1980-05-15"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
     *             @OA\Property(property="qualifications", type="string", example="MBBS, MRCP, PhD in Cardiology"),
     *             @OA\Property(property="years_of_experience", type="integer", example=15),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist..."),
     *             @OA\Property(property="profile_image", type="string", example="path/to/image.jpg"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="is_available", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Doctor")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('doctors')->ignore($doctor->id)],
            'phone' => 'nullable|string|max:20',
            'gmc_number' => ['sometimes', 'string', Rule::unique('doctors')->ignore($doctor->id)],
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
        ]);

        $doctor->update($validated);
        $doctor->load(['specialities', 'clinics']);

        return response()->json(new DoctorResource($doctor));
    }

    /**
     * @OA\Delete(
     *     path="/admin/doctors/{id}",
     *     summary="Delete doctor",
     *     description="Delete a doctor (soft delete by setting is_active to false)",
     *     tags={"Doctors"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Doctor deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found"
     *     )
     * )
     */
    public function destroy(Doctor $doctor): JsonResponse
    {
        // Soft delete by setting is_active to false
        $doctor->update(['is_active' => false]);

        return response()->json(['message' => 'Doctor deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/admin/doctors/{id}/specialities",
     *     summary="Attach specialities to doctor",
     *     description="Attach one or more specialities to a doctor",
     *     tags={"Doctors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"speciality_ids"},
     *             @OA\Property(property="speciality_ids", type="array", @OA\Items(type="integer"), example={1, 2}),
     *             @OA\Property(property="certification_date", type="string", format="date", example="2020-01-15"),
     *             @OA\Property(property="certification_body", type="string", example="GMC"),
     *             @OA\Property(property="is_primary", type="integer", example=1, description="Speciality ID that should be primary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specialities attached successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Doctor")
     *     )
     * )
     */
    public function attachSpecialities(Request $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validate([
            'speciality_ids' => 'required|array',
            'speciality_ids.*' => 'exists:specialities,id',
            'certification_date' => 'nullable|date',
            'certification_body' => 'nullable|string',
            'is_primary' => 'nullable|integer|exists:specialities,id',
        ]);

        $specialityData = [];
        foreach ($validated['speciality_ids'] as $specialityId) {
            $specialityData[$specialityId] = [
                'certification_date' => $validated['certification_date'] ?? now(),
                'certification_body' => $validated['certification_body'] ?? 'GMC',
                'is_primary' => $specialityId === $validated['is_primary'],
            ];
        }

        $doctor->specialities()->syncWithoutDetaching($specialityData);
        $doctor->load('specialities');

        return response()->json(new DoctorResource($doctor));
    }

    /**
     * @OA\Post(
     *     path="/admin/doctors/{id}/clinics",
     *     summary="Attach clinics to doctor",
     *     description="Attach one or more clinics to a doctor",
     *     tags={"Doctors"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"clinic_ids"},
     *             @OA\Property(property="clinic_ids", type="array", @OA\Items(type="integer"), example={1, 2}),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clinics attached successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Doctor")
     *     )
     * )
     */
    public function attachClinics(Request $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validate([
            'clinic_ids' => 'required|array',
            'clinic_ids.*' => 'exists:clinics,id',
            'start_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $clinicData = [];
        foreach ($validated['clinic_ids'] as $clinicId) {
            $clinicData[$clinicId] = [
                'start_date' => $validated['start_date'] ?? now(),
                'status' => $validated['status'] ?? 'active',
            ];
        }

        $doctor->clinics()->syncWithoutDetaching($clinicData);
        $doctor->load('clinics');

        return response()->json(new DoctorResource($doctor));
    }
}