<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\BloodRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\BloodRequestRequest;
use App\Http\Resources\Api\User\BloodRequestResource;
use App\Models\BloodRequest;
use Illuminate\Support\Facades\Auth;
use Log;

class BloodRequestController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/blood-requests",
     * summary="List all blood request for user",
     * tags={"Blood Request For Client"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Blood Requests retrieved successfully"),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserBloodRequestResource"))
     * )
     * ),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index()
    {
        try {
            $bloodRequests = BloodRequest::where('user_id', auth()->id())
                ->with(["user", "hospital"])
                ->orderBy('created_at', 'desc')
                ->paginate(config("pagnation.perPage"));

            return $this->successResponse(
                "Your Blood Requests retrieved successfully",
                $this->buildPaginatedResourceResponse(BloodRequestResource::class, $bloodRequests),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/{userId}/blood-requests",
     * summary="Create new blood request",
     * tags={"Blood Request For Client"},
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"hospitalId", "patientName", "bloodGroup", "unitsRequired", "contactPhone", "urgency", "requiredDate"},
     * @OA\Property(property="hospitalId", type="integer", example=1),
     * @OA\Property(property="patientName", type="string", example="Mg Mg"),
     * @OA\Property(property="bloodGroup", type="string", example="A+"),
     * @OA\Property(property="unitsRequired", type="integer", example=2),
     * @OA\Property(property="contactPhone", type="string", example="09123456789"),
     * @OA\Property(property="urgency", type="string", example="urgent"),
     * @OA\Property(property="requiredDate", type="string", format="date", example="2026-03-25"),
     * @OA\Property(property="reason", type="string", example="Emergency Surgery")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Created successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserBloodRequestResource")
     * ),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function store(BloodRequestRequest $request)
    {
        try {
            $data = $request->validated();

            $data['user_id'] = auth()->id();

            $bloodRequest = BloodRequest::create($data);

            return $this->successResponse(
                "Blood Request created successfully",
                new BloodRequestResource($bloodRequest->load(['user', 'hospital'])),
                201
            );
        } catch (\Exception $e) {
            Log::error("Blood Request Creation Failed: " . $e->getMessage());
            return $this->errorResponse("Something went wrong while creating the request.", 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/v1/{userId}/blood-requests/{id}/cancel",
     * summary="Cancel for blood request",
     * tags={"Blood Request For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Blood Request ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Cancelled successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserBloodRequestResource")
     * ),
     * @OA\Response(response=403, description="Unauthorized"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function cancel($id)
    {
        try {
            $bloodRequest = BloodRequest::findOrFail($id);

            if ($bloodRequest->user_id !== Auth::id()) {
                return $this->errorResponse("Unauthorized to cancel this request.", 403);
            }

            if ($bloodRequest->status !== BloodRequestStatus::PENDING->value) {
                return $this->errorResponse("Cannot cancel a request that is already " . $bloodRequest->status, 422);
            }


            $bloodRequest->status = BloodRequestStatus::CANCELLED->value;
            $bloodRequest->save();

            return $this->successResponse(
                "Blood Request cancelled successfully",
                new BloodRequestResource($bloodRequest),
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to cancel request: " . $e->getMessage(), 500);
        }
    }
}
