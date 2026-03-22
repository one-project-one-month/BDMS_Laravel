<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;
    public function index()
    {
        try {
            $roles = Role::all();

            return response()->json([
                "data" => $roles,
                "message" => "Role retrieved successfully!"
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
