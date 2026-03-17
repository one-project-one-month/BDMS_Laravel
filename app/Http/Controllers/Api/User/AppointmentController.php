<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\User\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;
    public function index($userId)
    {
        try {
            $appointments = Appointment::with(["users", "hospitals", "donation", "blood_requests"])->where("user_id", $userId)->paginate(config("pagnation.perPage"));

            return $this->successResponse("Appointment retrieved successfully", $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointments), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($userId, $id)
    {
        try {
            $appointment = Appointment::with(["users", "hospitals", "donation", "blood_requests"])->where([
                "user_id" => $userId,
                "id" => $id
            ])->get();

            if (!$appointment) {
                return $this->errorResponse("Appointment not found", 404);
            }

            return $this->successResponse("Appointment retrieved successfully", new AppointmentResource($appointment), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update($userId, $id, Request $request)
    {
        try {
            $appointment = Appointment::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$appointment) {
                return $this->errorResponse("Appointment not found", 404);
            }

            $appointment->load(['user', 'hospital', "donation", "blood_request"]);

            $appointment->update($request->all());

            return $this->successResponse("Appointment cancel successfully", new AppointmentResource($appointment), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
