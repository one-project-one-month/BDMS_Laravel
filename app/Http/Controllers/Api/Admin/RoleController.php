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
            $roles = Role::paginate(config('pagnation.perPage'));

            $this->successResponse("Role retrieved successfully", $this->buildPaginatedResourceResponse(RoleResource::class, $roles), 200);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
