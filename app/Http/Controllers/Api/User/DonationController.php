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
     *  GET - View lists of donations (History)
     */
    public function index(User $user)
    {
        if (auth()->id() !== $user->id) {
            return $this->errorResponse("Unauthorized access.", 403);
        }

        $donations = Donation::with('hospital')
            ->where('donor_id', $user->donor?->id)
            ->latest()
            ->get();

        return $this->successResponse("Success", DonationResource::collection($donations));
    }

    /**
     * GET - Show specific donation
     */
    public function show(User $user, Donation $donation)
    {
        if ($donation->donor_id !== $user->donor?->id) {
            return $this->errorResponse("Donation record not found for this user.", 404);
        }

        return $this->successResponse("Success", new DonationResource($donation));
    }

    /**
     * POST - Create donation
     */
    public function store(DonationRequest $request, User $user)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $donation = Donation::create($data);

        return $this->successResponse("Donation created.", new DonationResource($donation), 201);
    }

    /**
     * PATCH - Cancel donation
     */
    public function cancel(User $user, Donation $donation)
    {
        if ($donation->donor_id !== $user->donor?->id) {
            return $this->errorResponse("Unauthorized.", 403);
        }

        if ($donation->status !== DonationStatus::PENDING->value) {
            return $this->errorResponse("Cannot cancel a non-pending donation.", 400);
        }

        $donation->update(['status' => DonationStatus::CANCELLED->value]);

        return $this->successResponse("Donation cancelled.", new DonationResource($donation));
    }
}
