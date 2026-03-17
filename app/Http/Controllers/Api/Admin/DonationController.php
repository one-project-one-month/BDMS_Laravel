<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\DonationRequest;
use App\Http\Resources\Api\Admin\DonationResource;
use App\Models\Donation;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Donations",
 * description="API Endpoints for managing blood donations"
 * )
 */

class DonationController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/donations",
     * summary="Get list of all blood donations",
     * description="Returns a paginated list of donations with optional filtering by status or blood group.",
     * tags={"Donations"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="status",
     * in="query",
     * description="Filter by donation status (e.g., pending, approved, completed)",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="blood_group",
     * in="query",
     * description="Filter by blood group (e.g., A+, B-, O+)",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/DonationResource")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Forbidden - Admin access required")
     * )
     */
    public function index(Request $request)
    {
        $bloodGroup = $request->query('blood_group');
        $status = $request->query('status');
        $query = Donation::with(['donor', 'hospital', 'creator', 'approver', 'bloodRequest']);


        if ($status) {
            $query->where('status', $status);
        }

        if ($bloodGroup) {
            $query->where('blood_group', $bloodGroup);
        }

        $donations = $query->paginate(config('pagination.perPage'));

        return $this->successResponse('Donations retrieved successfully', $this->buildPaginatedResourceResponse(DonationResource::class, $donations), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/donations",
     * summary="Record a new blood donation",
     * tags={"Donations"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"donorId", "hospitalId", "createdBy", "bloodGroup", "unitsDonated", "donationDate", "status"},
     * @OA\Property(property="donorId", type="integer", example=1),
     * @OA\Property(property="hospitalId", type="integer", example=2),
     * @OA\Property(property="bloodRequestId", type="integer", example=3, nullable=true),
     * @OA\Property(property="createdBy", type="integer", example=1),
     * @OA\Property(property="bloodGroup", type="string", example="O+"),
     * @OA\Property(property="unitsDonated", type="integer", example=1),
     * @OA\Property(property="donationDate", type="string", format="date", example="2026-03-01"),
     * @OA\Property(property="status", type="string", example="pending"),
     * @OA\Property(property="approvedBy", type="integer", example=1, nullable=true),
     * @OA\Property(property="approvedAt", type="string", format="date-time", nullable=true),
     * @OA\Property(property="remarks", type="string", example="First time donor", nullable=true)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Donation recorded successfully",
     * @OA\JsonContent(ref="#/components/schemas/DonationResource")
     * )
     * )
     */
    public function store(DonationRequest $request)
    {
        try {
            $donation = Donation::create($request->validated());

            return $this->successResponse('Donation recorded successfully', new DonationResource($donation->load(['donor', 'hospital', 'creator', 'approver'])), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/donations/{id}",
     * summary="Display a specific donation",
     * tags={"Donations"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of donation to return",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/DonationResource")
     * ),
     * @OA\Response(response=404, description="Donation not found")
     * )
     */
    public function show($id)
    {
        try {
            $donation = Donation::with(['donor', 'hospital', 'creator', 'approver', 'bloodRequest', 'medicalRecord'])->findOrFail($id);

            return $this->successResponse('Donation details retrieved', new DonationResource($donation));
        } catch (\Exception $e) {
            return $this->errorResponse('Donation not found', 404);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/donations/{id}",
     * summary="Update an existing donation record",
     * tags={"Donations"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="approved"),
     * @OA\Property(property="unitsDonated", type="integer", example=2),
     * @OA\Property(property="approvedBy", type="integer", example=1),
     * @OA\Property(property="approvedAt", type="string", format="date-time", example="2026-03-02T10:00:00Z"),
     * @OA\Property(property="remarks", type="string", example="Approved after screening")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Donation updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/DonationResource")
     * )
     * )
     */
    public function update(DonationRequest $request, $id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $donation->update($request->validated());

            return $this->successResponse('Donation updated successfully', new DonationResource($donation->load(['donor', 'hospital', 'creator', 'approver'])));
        } catch (\Exception $e) {
            return $this->errorResponse('Update failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/donations/{id}",
     * summary="Delete a donation record",
     * tags={"Donations"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Donation deleted successfully",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Donation record deleted."))
     * )
     * )
     */
    public function destroy($id)
    {
        try {
            $donation = Donation::findOrFail($id);
            $donation->delete();

            return $this->successResponse('Donation record deleted successfully', null);
        } catch (\Exception $e) {
            return $this->errorResponse('Delete failed', 500);
        }
    }

    public function restore($id)
    {
        try {
            $donation = Donation::onlyTrashed()->findOrFail($id);
            $donation->restore();

            return $this->successResponse('Donation restored successfully', new DonationResource($donation));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $donation = Donation::withTrashed()->findOrFail($id);
            $donation->forceDelete();

            return $this->successResponse('Donation permanently deleted', null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
