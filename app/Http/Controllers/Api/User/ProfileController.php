<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\ProfileRequest;
use App\Http\Resources\Api\User\ProfileResource;
use App\Models\User;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/users/{userId}",
     * summary="Get user profile details",
     * tags={"Profile For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="ID of the user",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Profile retrieved successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Profile retrieved successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/ProfileUserResource")
     * )
     * ),
     * @OA\Response(response=403, description="Unauthorized access to this profile"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show($userId)
    {
        if ((int) auth()->id() !== (int) $userId) {
            return $this->errorResponse('Unauthorized access to this profile.', 403);
        }

        $user = User::find($userId);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        $user->load('donor');

        return $this->successResponse(
            'Profile retrieved successfully',
            new ProfileResource($user),
            200
        );
    }
    /**
     * @OA\Put(
     * path="/api/v1/users/{userId}",
     * summary="Update user profile information",
     * tags={"Profile For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="ID of the user",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="userName", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", example="johndoe@example.com"),
     * @OA\Property(property="hospitalId", type="integer", example=1)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Profile updated successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Profile updated successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/ProfileUserResource")
     * )
     * ),
     * @OA\Response(response=403, description="Unauthorized access to this profile"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(ProfileRequest $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            if ((int) auth()->id() !== (int) $user->id) {
                return $this->errorResponse('Unauthorized access.', 403);
            }

            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);

            return $this->successResponse(
                'Account information updated successfully.',
                new ProfileResource($user),
                200
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Update failed: " . $e->getMessage(), 500);
        }
    }
}
