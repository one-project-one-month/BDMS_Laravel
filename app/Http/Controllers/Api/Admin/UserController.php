<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Api\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $role = $request->query('role');

        $query = User::with('hospital');

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->paginate(config('pagination.perPage'));

        return $this->successResponse('User retrieved successfully', $this->buildPaginatedResourceResponse(UserResource::class, $users), 200);
    }

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

            $user->load('hospital');

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

            $user->delete();

            return $this->successResponse('User deleted successfully', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Delete failed', 500);
        }
    }
}
