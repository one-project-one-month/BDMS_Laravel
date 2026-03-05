<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\BloodRequest;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\BloodRequest as BloodRequestRequest;
use App\Http\Resources\Api\Admin\BloodRequestResource;

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
     *  Approve Endpoint
     */
   
    public function approve($id)
    {
    $bloodRequest = BloodRequest::findOrFail($id);

    try {
        $bloodRequest->approve(auth()->id());

        return $this->successResponse(
            'Blood Request Approved Successfully',
            new BloodRequestResource($bloodRequest)
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
    }

    /**
     *  Reject Endpoint
     */
    public function reject($id)
    {
    $bloodRequest = BloodRequest::findOrFail($id);

    try {
        $bloodRequest->reject();

        return $this->successResponse(
            'Blood Request Rejected Successfully',
            new BloodRequestResource($bloodRequest)
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
    }

    /**
     *  Cancel Endpoint
     */
    public function cancel($id)
    {
    $bloodRequest = BloodRequest::findOrFail($id);

    try {
        $bloodRequest->cancel();

        return $this->successResponse(
            'Blood Request Cancelled Successfully',
            new BloodRequestResource($bloodRequest)
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
    }

    /**
     *  Fullfil Endpoint
     */
    public function fulfill($id)
    {
    $bloodRequest = BloodRequest::findOrFail($id);

    try {
        $bloodRequest->fulfill();

        return $this->successResponse(
            'Blood Request Fulfilled Successfully',
            new BloodRequestResource($bloodRequest)
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
    }
}