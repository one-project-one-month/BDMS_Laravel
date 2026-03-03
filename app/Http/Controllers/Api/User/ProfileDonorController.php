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
    public function index(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            $query = $user->donor()->with('user');

            $donors = $query->paginate(config('pagination.perPage', 10));

            return $this->successResponse(
                'User donor profile retrieved successfully',
                $this->buildPaginatedResourceResponse(ProfileDonorResource::class, $donors),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(ProfileDonorRequest $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            if ($user->donor()->exists()) {
                return $this->errorResponse('Donor profile already exists for this user.', 422);
            }

            $donor = $user->donor()->create($request->all());

            return $this->successResponse(
                'Donor profile created successfully',
                new ProfileDonorResource($donor->load('user')),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
