<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\User\ProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->errorResponse('User not found.' , 404);
        }

        if (Auth::id() !== $user->id) {
            return $this->errorResponse('Unauthorized access to this profile.', 403);
        }

        return $this->successResponse(
            'Profile retrieved successfully',
            new ProfileResource($user),
            200
        );
    }

    public function update($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        if (Auth::id() !== $user->id) {
            return $this->errorResponse('Unauthorized access to this profile.', 403);
        }

        return $this->successResponse(
            'Profile updated successfully',
            new ProfileResource($user),
            200
        );
    }
}
