<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Patient authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new patient",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","surname","date_of_birth","address","mobile_phone","email","pin","clinic_id","doctor_id","terms_accepted","data_sharing_consent"},
     *             @OA\Property(property="first_name", type="string", example="Raheel"),
     *             @OA\Property(property="surname", type="string", example="Saleem"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1997-08-26"),
     *             @OA\Property(property="address", type="string", example="74 Woodford Avenue, E17 6LH"),
     *             @OA\Property(property="mobile_phone", type="string", example="03172650575"),
     *             @OA\Property(property="home_phone", type="string", example="03363860313"),
     *             @OA\Property(property="email", type="string", format="email", example="raheelsaleem.se@gmail.com"),
     *             @OA\Property(property="pin", type="string", example="1111"),
     *             @OA\Property(property="clinic_id", type="integer", example=4),
     *             @OA\Property(property="doctor_id", type="integer", example=1),
     *             @OA\Property(property="terms_accepted", type="boolean", example=true),
     *             @OA\Property(property="data_sharing_consent", type="boolean", example=true),
     *             @OA\Property(property="notifications_consent", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object"),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:1000',
            'mobile_phone' => 'required|string|max:20|unique:patients',
            'home_phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:patients',
            'pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
            'clinic_id' => 'required|exists:clinics,id',
            'doctor_id' => 'required|exists:doctors,id',
            'terms_accepted' => 'required|boolean|accepted',
            'data_sharing_consent' => 'required|boolean|accepted',
            'notifications_consent' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::create([
            'first_name' => $request->first_name,
            'surname' => $request->surname,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'mobile_phone' => $request->mobile_phone,
            'home_phone' => $request->home_phone,
            'email' => $request->email,
            'pin' => Hash::make($request->pin),
            'clinic_id' => $request->clinic_id,
            'doctor_id' => $request->doctor_id,
            'terms_accepted' => $request->terms_accepted,
            'data_sharing_consent' => $request->data_sharing_consent,
            'notifications_consent' => $request->notifications_consent ?? false,
        ]);

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Patient registered successfully',
            'data' => [
                'patient' => $patient->load(['clinic', 'doctor']),
                'token' => $token
            ]
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login patient",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile_phone","pin"},
     *             @OA\Property(property="mobile_phone", type="string", example="03172650575"),
     *             @OA\Property(property="pin", type="string", example="1111")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object"),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile_phone' => 'required|string',
            'pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('mobile_phone', $request->mobile_phone)->first();

        if (!$patient || !Hash::check($request->pin, $patient->pin)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $patient->update(['last_login_at' => now()]);
        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'patient' => $patient->load(['clinic', 'doctor']),
                'token' => $token
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout patient",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/auth/me",
     *     summary="Get current patient profile",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Patient profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $patient = $request->user()->load(['clinic', 'doctor', 'clinicalData']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'patient' => $patient
            ]
        ]);
    }
}