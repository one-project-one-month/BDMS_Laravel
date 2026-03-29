<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\BloodInventoryStatus;
use App\Enums\BloodRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\BloodRequest as BloodRequestRequest;
use App\Http\Resources\Api\Admin\BloodRequestResource;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use DB;
use Illuminate\Support\Facades\Request;

class BloodRequestController extends Controller
{
    use ApiResponse;

    /**
     * Display all blood requests
     */
    public function index()
    {
        $bloodRequests = BloodRequest::with(['user', 'hospital', 'approver'])
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            'Blood Requests List',
            BloodRequestResource::collection($bloodRequests)
        );
    }

    /**
     * Store new blood request
     */
    public function store(BloodRequestRequest $request)
    {
        $bloodRequest = BloodRequest::create($request->validated());

        return $this->successResponse(
            'Blood Request Created Successfully',
            new BloodRequestResource($bloodRequest)
        );
    }

    /**
     * Show single blood request
     */
    public function show($id)
    {
        $bloodRequest = BloodRequest::with(['user', 'hospital', 'approver'])
            ->findOrFail($id);

        return $this->successResponse(
            'Blood Request Detail',
            new BloodRequestResource($bloodRequest)
        );
    }

    /**
     * Update blood request
     */
    public function update(BloodRequestRequest $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->update($request->validated());

        return $this->successResponse(
            'Blood Request Updated Successfully',
            new BloodRequestResource($bloodRequest)
        );
    }

    /**
     * Soft delete
     */
    public function destroy($id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->delete();

        return $this->successResponse(
            'Blood Request Deleted Successfully'
        );
    }

    /**
     * Update blood request status (approve, reject, cancel, fulfill)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|string|in:approve,reject,cancel,fulfill',
        ]);

        $bloodRequest = BloodRequest::findOrFail($id);
        $action = $request->action;

        try {
            // Validation Checks
            if (in_array($action, ['approve', 'reject']) && $bloodRequest->status->value !== BloodRequestStatus::PENDING->value) {
                return $this->errorResponse("Action failed. Current status is " . $bloodRequest->status->value, 400);
            }

            if ($action === 'fulfill' && $bloodRequest->status->value !== BloodRequestStatus::APPROVED->value) {
                return $this->errorResponse("Only approved requests can be fulfilled.", 400);
            }

            if ($action === 'fulfill') {
                return DB::transaction(function () use ($bloodRequest) {

                    $inventoryUnits = BloodInventory::where('hospital_id', $bloodRequest->hospital_id)
                        ->where('blood_group', $bloodRequest->blood_group)
                        ->where('status', BloodInventoryStatus::AVAILABLE->value)
                        ->where('expired_at', '>', now())
                        ->orderBy('expired_at', 'asc')
                        ->limit($bloodRequest->units_requested)
                        ->get();

                    if ($inventoryUnits->count() < $bloodRequest->units_requested) {
                        return $this->errorResponse("Insufficient blood units in inventory. Available: " . $inventoryUnits->count(), 422);
                    }

                    foreach ($inventoryUnits as $unit) {
                        $unit->update([
                            'status' => BloodInventoryStatus::USED->value,
                            'blood_request_id' => $bloodRequest->id
                        ]);
                    }

                    $bloodRequest->update(['status' => BloodRequestStatus::FULFILLED->value]);

                    return $this->successResponse(
                        "Blood request fulfilled. Inventory updated.",
                        new BloodRequestResource($bloodRequest->load(['hospital', 'user']))
                    );
                });
            }

            $updateData = match ($action) {
                'approve' => [
                    'status' => BloodRequestStatus::APPROVED->value,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ],
                'reject' => ['status' => BloodRequestStatus::REJECTED->value],
                'cancel' => ['status' => BloodRequestStatus::CANCELLED->value],
            };

            $bloodRequest->update($updateData);

            return $this->successResponse(
                "Blood request " . $action . "ed successfully.",
                new BloodRequestResource($bloodRequest->load(['hospital', 'user']))
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
