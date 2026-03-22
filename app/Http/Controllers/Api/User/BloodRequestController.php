<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\User\BloodRequestRequest;
use App\Http\Resources\Api\User\BloodRequestResource;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    use ApiResponse;
    //view lists of blood requests
    public function index()
    {
        try {
            $bloodRequests = BloodRequest::with(["user", "hospital"])->paginate(config("pagnation.perPage"));

            return $this->successResponse("Blood Requests retrieved successfully", $this->buildPaginatedResourceResponse(BloodRequestResource::class, $bloodRequests), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    //create a blood request
    public function store(BloodRequestRequest $request)
    {
        try {
            $bloodRequest = BloodRequest::create([
                "user_id" => Auth::id(),
                "hospital_id" => $request->hospital_id,
                "patient_name" => $request->patient_name,
                "blood_group" => $request->blood_group,
                "units_required" => $request->units_required,
                "contact_phone" => $request->contact_phone,
                "urgency" => $request->urgency,
                "required_date" => $request->required_date,
                "reason" => $request->reason
            ]);

            return $this->successResponse("Blood Request created successfully", new BloodRequestResource($bloodRequest), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    //cancel a blood request
    public function cancel($id)
    {
        try {
            $bloodRequest = BloodRequest::findOrFail($id);

            // Check if the authenticated user is the owner of the blood request
            if ($bloodRequest->user_id !== Auth::id()) {
                return $this->errorResponse("Unauthorized", 403);
            }

            $bloodRequest->status = "cancelled";
            $bloodRequest->save();

            return $this->successResponse("Blood Request cancelled successfully", new BloodRequestResource($bloodRequest), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
