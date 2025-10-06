<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="MyHomeBP API",
 *     version="1.0.0",
 *     description="Blood Pressure Management API for NHS and Private GP Surgery Use in the UK",
 *     @OA\Contact(
 *         email="support@247meditech.com",
 *         name="247 Meditech Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://api.myhomebp.com/api",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter the token with the `Bearer: ` prefix, e.g. `Bearer abc123`"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Patient authentication endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Patient",
 *     description="Patient profile and clinical data management"
 * )
 * 
 * @OA\Tag(
 *     name="Blood Pressure",
 *     description="Blood pressure recording and management"
 * )
 * 
 * @OA\Tag(
 *     name="Clinic",
 *     description="Clinic search and management"
 * )
 * 
 * @OA\Tag(
 *     name="Reports",
 *     description="Blood pressure report generation and management"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}