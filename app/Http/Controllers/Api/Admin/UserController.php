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
     * description="Creates a new user account.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"", "email", "password", "role"},
     * @OA\Property(property="userName", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="Password123"),
     * @OA\Property(property="role", type="string", enum={"Admin", "Tenant", "Staff"}, example="Tenant"),
     * @OA\Property(property="tenantId", type="integer", description="Required if role is Tenant", example=1)
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
