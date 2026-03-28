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
    public function index()
    {
        try {
            $user = auth()->user();

            if (!$user->donor) {
                return $this->successResponse("No donation history found.", [
                    'items' => [],
                    'pagination' => null
                ]);
            }

            $donations = Donation::where('donor_id', $user->donor->id)
                ->with('hospital')
                ->orderBy('created_at', 'desc')
                ->paginate(config("pagnation.perPage"));

            return $this->successResponse(
                "Donation history retrieved successfully",
                $this->buildPaginatedResourceResponse(DonationResource::class, $donations),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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
    public function show(User $userId, Donation $id)
    {
        if ((int) auth()->id() !== (int) $userId->id) {
            return $this->errorResponse("Unauthorized access.", 403);
        }

        if (!$userId->donor || (int) $id->donor_id !== (int) $userId->donor->id) {
            return $this->errorResponse("Donation record not found for this user.", 404);
        }

        $id->load(['hospital']);

        return $this->successResponse(
            "Donation record retrieved successfully",
            new DonationResource($id)
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
    public function store(DonationRequest $request, User $userId)
    {
        try {
            if ((int) auth()->id() !== (int) $userId->id) {
                return $this->errorResponse("Unauthorized access. Token ID and User ID mismatch.", 403);
            }

            if (!$userId->donor) {
                return $this->errorResponse("Donor profile not found for this user.", 422);
            }

            $lastDonation = $userId->donor->last_donation_date;

            if ($lastDonation) {
                $daysSinceLastDonation = $lastDonation->diffInDays(now());

                if ($daysSinceLastDonation < 90) {
                    $eligibleDate = $lastDonation->copy()->addDays(90)->format('d-m-Y');

                    return $this->errorResponse(
                        "You are not eligible to donate blood yet. The earliest date you can donate is {$eligibleDate}.",
                        422
                    );
                }
            }

            $data = $request->validated();
            $data['donor_id'] = $userId->donor->id;
            $data['created_by'] = auth()->id();
            $data['blood_group'] = $userId->donor->blood_group;
            $data['status'] = 'pending';

            $donation = Donation::create($data);
            $userId->donor->update(['last_donation_date' => $donation->donation_date]);

            return $this->successResponse(
                "Donation record created successfully.",
                new DonationResource($donation->load(['hospital'])),
                201
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Failed: " . $e->getMessage(), 500);
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
    public function cancel(User $userId, Donation $id)
    {
        try {
            if ((int) auth()->id() !== (int) $userId->id) {
                return $this->errorResponse("Unauthorized access. ID mismatch.", 403);
            }

            if (!$userId->donor || (int) $id->donor_id !== (int) $userId->donor->id) {
                return $this->errorResponse("Donation record not found.", 404);
            }

            if ($id->status !== \App\Enums\DonationStatus::PENDING) {
                return $this->errorResponse("Only pending donations can be cancelled.", 400);
            }

            $id->update([
                'status' => 'cancelled',
                'remarks' => $id->remarks . " (Cancelled by user at " . now()->toDateTimeString() . ")"
            ]);

            return $this->successResponse(
                "Donation record has been cancelled.",
                new DonationResource($id)
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Cancellation failed: " . $e->getMessage(), 500);
        }
    }
}
