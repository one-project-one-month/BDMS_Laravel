<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\RoleResource;
use App\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;
    public function index()
    {
        try {
            $roles = Role::all();

            $this->successResponse("Role retrived successfully", RoleResource::collection($roles), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
