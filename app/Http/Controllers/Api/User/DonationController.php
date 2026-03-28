<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\DonationStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\DonationRequest;
use App\Http\Resources\Api\User\DonationResource;
use App\Models\Donation;
use App\Models\User;

class DonationController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/donations",
     * summary="Get donation history for a specific user",
     * tags={"Donation For Client"},
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
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/UserDonationResource")
     * )
     * )
     * ),
     * @OA\Response(response=403, description="Unauthorized access")
     * )
     */
    public function index(User $user)
    {
        if (auth()->id() !== $user->id) {
            return $this->errorResponse("Unauthorized access.", 403);
        }

        if (!$user->donor) {
            return $this->successResponse("No donation history found.", []);
        }

        $donations = Donation::with('hospital')
            ->where('donor_id', $user->donor->id)
            ->latest()
            ->get();

        return $this->successResponse(
            "Donation history retrieved successfully",
            DonationResource::collection($donations)
        );
    }

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/donations/{id}",
     * summary="Get details of a specific donation record",
     * tags={"Donation For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="id", in="path", description="Donation ID", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="data", ref="#/components/schemas/UserDonationResource")
     * )
     * ),
     * @OA\Response(response=404, description="Donation record not found")
     * )
     */
    public function show(User $user, Donation $donation)
    {
        if (auth()->id() !== $user->id) {
            return $this->errorResponse("Unauthorized access.", 403);
        }

        if (!$user->donor || $donation->donor_id !== $user->donor->id) {
            return $this->errorResponse("Donation record not found for this user.", 404);
        }

        $donation->load(['hospital', 'approvedBy']);

        return $this->successResponse(
            "Donation record retrieved successfully",
            new DonationResource($donation)
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/{userId}/donations",
     * summary="Create a new donation record",
     * tags={"Donation For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"hospital_id", "blood_group", "units"},
     * @OA\Property(property="hospital_id", type="integer", example=1),
     * @OA\Property(property="blood_group", type="string", example="B+"),
     * @OA\Property(property="units", type="integer", example=1),
     * @OA\Property(property="remarks", type="string", example="First time donor")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Donation created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="data", ref="#/components/schemas/UserDonationResource")
     * )
     * )
     * )
     */
    public function store(DonationRequest $request, User $user)
    {
        try {
            if (!$user->donor) {
                return $this->errorResponse("This user does not have a donor profile. Please create one first.", 422);
            }

            $data = $request->validated();

            $data['donor_id'] = $user->donor->id;
            $data['created_by'] = auth()->id();

            $data['blood_group'] = $user->donor->blood_group;

            $donation = Donation::create($data);
            $user->donor->update(['last_donation_date' => $donation->donation_date]);

            return $this->successResponse(
                "Donation record created successfully.",
                new DonationResource($donation->load(['donor', 'hospital'])),
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create donation: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/v1/{userId}/donations/{id}/cancel",
     * summary="Cancel a pending donation",
     * tags={"Donation For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Donation cancelled successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="data", ref="#/components/schemas/UserDonationResource")
     * )
     * ),
     * @OA\Response(response=400, description="Cannot cancel a non-pending donation"),
     * @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function cancel(User $user, Donation $donation)
    {
        try {
            if (auth()->id() !== $user->id) {
                return $this->errorResponse("Unauthorized access.", 403);
            }

            if (!$user->donor || $donation->donor_id !== $user->donor->id) {
                return $this->errorResponse("Donation record not found for this user.", 404);
            }

            if ($donation->status !== DonationStatus::PENDING->value) {
                return $this->errorResponse(
                    "Cannot cancel donation. Current status: " . $donation->status,
                    400
                );
            }

            $donation->update([
                'status' => DonationStatus::CANCELLED->value,
                'remarks' => $donation->remarks . " (Cancelled by user on " . now()->toDateTimeString() . ")"
            ]);

            return $this->successResponse(
                "Donation record has been cancelled.",
                new DonationResource($donation)
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to cancel donation: " . $e->getMessage(), 500);
        }
    }
}
