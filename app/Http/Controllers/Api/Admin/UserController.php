<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Api\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 * name="Users",
 * description="API Endpoints for managing users"
 * )
 */

class UserController extends Controller
{
    use ApiResponse;


    /**
     * @OA\Get(
     * path="/api/v1/users",
     * summary="Get list of all users",
     * description="Returns a collection of users. Can be filtered by role.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="role_id",
     * in="query",
     * description="Filter users by role (e.g., 1 for Admin, 2 for Staff, 3 for user)",
     * required=false,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/UserResource")
     * ),
     * @OA\Property(property="message", type="string", example="Users retrieved successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden"
     * )
     * )
     */
    public function index(Request $request)
    {
        try {
            $roleName = $request->query('role');

            $query = User::with(['hospital', 'role']);

            if ($roleName) {
                $query->whereHas('role', function ($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
            }

            $users = $query->paginate(config('pagination.perPage'));

            return $this->successResponse(
                'User retrieved successfully',
                $this->buildPaginatedResourceResponse(UserResource::class, $users),
                200
            );
        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/users",
     * summary="Create a new user",
     *
     * description="Creates a new user account.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"hospitalId", "roleId", "userName", "email", "password", "is_active"},
     * @OA\Property(property="hospitalId", type="number", example="1"),
     * @OA\Property(property="roleId", type="number", example="3"),
     * @OA\Property(property="userName", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="Password123"),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User created successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="User creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(UserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($request->password);

            $user = User::create($validated);

            return $this->successResponse('User created successfully', new UserResource($user), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/users/{id}",
     * summary="Get specific user details",
     * description="Returns the data of a single user by their ID.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to retrieve",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="User found",
     * @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/UserResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="User not found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User not found.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
     */
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $user->load('hospital', 'role');

            return $this->successResponse(
                'User details retrieved successfully',
                new UserResource($user),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/users/{id}",
     * summary="Update an existing user",
     * description="Updates the user details for the given ID. Only provided fields will be updated.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to update",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Updated user data",
     * @OA\JsonContent(
     * @OA\Property(property="hospitalId", type="integer", example=1),
     * @OA\Property(property="roleId", type="integer", example=2),
     * @OA\Property(property="userName", type="string", example="minko_updated"),
     * @OA\Property(property="email", type="string", format="email", example="updated@bloodlink.com"),
     * @OA\Property(property="isActive", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User updated successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/UserResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="User not found"
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error"
     * )
     * )
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $validated = $request->validated();

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($request->password);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return $this->successResponse(
                'User updated successfully',
                new UserResource($user),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/users/{id}",
     * summary="Delete a user",
     * description="Deletes a specific user record from the system.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to delete",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="User deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User deleted successfully")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="User not found"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $user->delete();

            return $this->successResponse('User deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Delete failed', 500);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            return $this->successResponse(
                'User restored successfully',
                new UserResource($user),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('User not found in trash or restore failed', 404);
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->forceDelete();

            return $this->successResponse('User permanently deleted', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Permanent delete failed', 500);
        }
    }

    public function trashedIndex()
    {
        $users = User::onlyTrashed()->with('hospital')->paginate(config('pagination.perPage'));

        return $this->successResponse(
            'Trashed users retrieved successfully',
            $this->buildPaginatedResourceResponse(UserResource::class, $users),
            200
        );
    }

    /**
     * @OA\Patch(
     * path="/api/v1/users/{id}/deactivate",
     * summary="Deactivate a user account",
     * description="Sets the user's status to inactive.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to deactivate",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="User deactivated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User account has been deactivated."),
     * @OA\Property(property="data", ref="#/components/schemas/UserResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="User not found"
     * ),
     * @OA\Response(
     * response=403,
     * description="Unauthorized"
     * )
     * )
     */
    public function deactivate($id)
    {
        try {
            $user = User::with('donor')->find($id);

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $user->update(['is_active' => false]);

            if ($user->donor) {
                $user->donor->update(['is_active' => false]);
            }

            return $this->successResponse(
                'User account has been deactivated successfully',
                new UserResource($user->load('donor')),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to deactivate user', 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/v1/users/{id}/activate",
     * summary="Activate a user account",
     * description="Sets the user's status to active, allowing them to access the system again.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to activate",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="User activated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="User account has been activated."),
     * @OA\Property(property="data", ref="#/components/schemas/UserResource")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="User not found"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
     */
    public function activate($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        $user->update(['is_active' => true]);

        return $this->successResponse('User account is now active', new UserResource($user), 200);
    }
}
