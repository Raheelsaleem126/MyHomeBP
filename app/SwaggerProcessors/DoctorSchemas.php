<?php

namespace App\SwaggerProcessors;

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
 *     @OA\Property(
 *         property="specialities",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Speciality")
 *     ),
 *     @OA\Property(
 *         property="primary_speciality",
 *         ref="#/components/schemas/Speciality"
 *     ),
 *     @OA\Property(
 *         property="clinics",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Clinic")
 *     ),
 *     @OA\Property(property="patients_count", type="integer", example=25)
 * )
 */

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
 *     @OA\Property(
 *         property="doctors",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Doctor")
 *     )
 * )
 */

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
 *     @OA\Property(
 *         property="doctors",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Doctor")
 *     ),
 *     @OA\Property(
 *         property="patients",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Patient")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="Patient",
 *     type="object",
 *     title="Patient",
 *     description="Patient model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="surname", type="string", example="Doe"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="address", type="string", example="123 Main Street"),
 *     @OA\Property(property="mobile_phone", type="string", example="+44 7123 456789"),
 *     @OA\Property(property="home_phone", type="string", example="+44 20 7123 4567"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="clinic_id", type="integer", example=1),
 *     @OA\Property(property="doctor_id", type="integer", example=1),
 *     @OA\Property(property="terms_accepted", type="boolean", example=true),
 *     @OA\Property(property="data_sharing_consent", type="boolean", example=true),
 *     @OA\Property(property="notifications_consent", type="boolean", example=true),
 *     @OA\Property(property="last_login_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01 12:00:00")
 * )
 */
