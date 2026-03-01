<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * title="Blood Donation Management System API",
 * version="1.0.0",
 * description="API documentation for Blood Donation Management System"
 * )
 *
 * @OA\Server(
 * url="http://localhost:8000",
 * description="Local Server"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * in="header",
 * name="Authorization"
 * )
 */
abstract class Controller
{
    //
}
