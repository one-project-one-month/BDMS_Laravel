<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * title="Blood Donation Management System API",
 * version="1.0.0",
 * description="API documentation"
 * )
 * @OA\Components(
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 * )
 */
abstract class Controller
{
    //
}
