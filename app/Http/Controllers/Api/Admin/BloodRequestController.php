<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\BloodRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\BloodRequest as BloodRequestRequest;
use App\Http\Resources\Api\Admin\BloodRequestResource;
use App\Models\BloodRequest;
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
            if (in_array($action, ['approve', 'reject']) && $bloodRequest->status !== BloodRequestStatus::PENDING) {
                return $this->errorResponse("Action failed. Current status is " . $bloodRequest->status->value, 400);
            }

            // Approved -> Fullfill
            if ($action === 'fulfill' && $bloodRequest->status !== BloodRequestStatus::APPROVED) {
                return $this->errorResponse("Only approved requests can be fulfilled.", 400);
            }

            $updateData = match ($action) {
                'approve' => [
                    'status' => BloodRequestStatus::APPROVED,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ],
                'reject' => [
                    'status' => BloodRequestStatus::REJECTED,
                ],
                'cancel' => [
                    'status' => BloodRequestStatus::CANCELLED,
                ],
                'fulfill' => [
                    'status' => BloodRequestStatus::FULFILLED,
                ],
            };

            $bloodRequest->update($updateData);

            $messages = [
                'approve' => 'Blood request has been approved.',
                'reject' => 'Blood request has been rejected.',
                'cancel' => 'Blood request has been cancelled.',
                'fulfill' => 'Blood request has been marked as fulfilled.',
            ];

            return $this->successResponse(
                $messages[$action],
                new BloodRequestResource($bloodRequest->load(['hospital', 'user'])),
                200
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
