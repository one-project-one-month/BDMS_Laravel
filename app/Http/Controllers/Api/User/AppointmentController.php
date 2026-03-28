<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\User\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/appointments",
     * summary="Retrieve all appointments for a specific user",
     * tags={"Appointment For Client"},
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
     * @OA\Property(property="message", type="string", example="Appointment retrieved successfully"),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/UserAppointmentResource")
     * )
     * )
     * ),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index($userId)
    {
        try {
            if ((int) auth()->id() !== (int) $userId) {
                return $this->errorResponse("Unauthorized access to this data.", 403);
            }

            $perPage = config("pagination.perPage");

            $appointments = Appointment::with(["user", "hospital", "donation", "blood_request"])
                ->where("user_id", $userId)
                ->latest()
                ->paginate($perPage);

            return $this->successResponse(
                "Appointments retrieved successfully",
                $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointments),
                200
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to fetch appointments: " . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/{userId}/appointments/{id}",
     * summary="Get details of a specific appointment",
     * tags={"Appointment For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="User ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Appointment ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful retrieval",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Appointment retrieved successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/UserAppointmentResource")
     * )
     * ),
     * @OA\Response(response=404, description="Appointment not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function show($userId, $id)
    {
        try {
            if ((int) auth()->id() !== (int) $userId) {
                return $this->errorResponse("Unauthorized access.", 403);
            }

            $appointment = Appointment::with(["user", "hospital", "donation", "blood_request"])
                ->where([
                    "user_id" => $userId,
                    "id" => $id
                ])
                ->first();

            if (!$appointment) {
                return $this->errorResponse("Appointment not found", 404);
            }

            return $this->successResponse(
                "Appointment retrieved successfully",
                new AppointmentResource($appointment),
                200
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/{userId}/appointments/{id}",
     * summary="Update or cancel an appointment",
     * tags={"Appointment For Client"},
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="userId",
     * in="path",
     * description="User ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Appointment ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="cancelled"),
     * @OA\Property(property="remarks", type="string", example="User cannot attend due to emergency")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Appointment updated successfully",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="data", ref="#/components/schemas/UserAppointmentResource")
     * )
     * ),
     * @OA\Response(response=404, description="Appointment not found"),
     * @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(Request $request, $userId, $id)
    {
        try {
            $appointment = Appointment::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$appointment) {
                return $this->errorResponse("Appointment not found", 404);
            }

            if ((int) auth()->id() !== (int) $userId) {
                return $this->errorResponse("Unauthorized action.", 403);
            }

            $appointment->update([
                'status' => AppointmentStatus::CANCELLED,
                'remarks' => $request->remarks ?? $appointment->remarks
            ]);

            $appointment->load(['user', 'hospital', "donation", "blood_request"]);

            return $this->successResponse(
                "Appointment cancelled successfully",
                new AppointmentResource($appointment),
                200
            );

        } catch (\Exception $e) {
            return $this->errorResponse("Update failed: " . $e->getMessage(), 500);
        }
    }
}
