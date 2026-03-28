<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\ProfileDonorRequest;
use App\Http\Resources\Api\User\ProfileDonorResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileDonorController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/users/{userId}/donors",
     * summary="Retrieve the donor profile for a specific user",
     * tags={"Donor Profile For Client"},
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
     * description="Donor profile retrieved successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="User donor profile retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/ProfileDonorResource")
     * )
     * )
     * ),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index(Request $request, $userId)
    {
        try {
            $user = User::with('donor.user')->findOrFail($userId);

            if (!$user->donor) {
                return $this->errorResponse("Donor profile not found for this user.", 404);
            }

            return $this->successResponse(
                'User donor profile retrieved successfully',
                new ProfileDonorResource($user->donor),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/users/{userId}/donors",
     * summary="Create a new donor profile for a user",
     * tags={"Donor Profile For Client"},
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
     * required={"nrcNo", "dateOfBirth", "gender", "bloodGroup", "weight"},
     * @OA\Property(property="nrcNo", type="string", example="12/XXX(C)123456"),
     * @OA\Property(property="dateOfBirth", type="string", format="date", example="1995-05-20"),
     * @OA\Property(property="gender", type="string", example="male"),
     * @OA\Property(property="bloodGroup", type="string", example="O+"),
     * @OA\Property(property="weight", type="integer", example=65),
     * @OA\Property(property="emergencyContact", type="string", example="Daw Mya"),
     * @OA\Property(property="emergencyPhone", type="string", example="09777123456"),
     * @OA\Property(property="address", type="string", example="No. 12, Pyay Road, Yangon"),
     * @OA\Property(property="remarks", type="string", example="Regular donor")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Donor profile created successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="data", ref="#/components/schemas/ProfileDonorResource")
     * )
     * ),
     * @OA\Response(response=422, description="Donor profile already exists for this user"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function store(ProfileDonorRequest $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            if ((int) auth()->id() !== (int) $user->id) {
                return $this->errorResponse('Unauthorized action.', 403);
            }

            if ($user->donor()->exists()) {
                return $this->errorResponse('Donor profile already exists for this user.', 422);
            }

            $donor = $user->donor()->create($request->validated());

            return $this->successResponse(
                'Donor profile created successfully',
                new ProfileDonorResource($donor->load('user')),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse("Error: " . $e->getMessage(), 500);
        }
    }
}
