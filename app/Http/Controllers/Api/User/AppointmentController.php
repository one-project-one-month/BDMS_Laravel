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
            $appointments = Appointment::with(["users", "hospitals", "donation", "blood_requests"])->where("user_id", $userId)->paginate(config("pagnation.perPage"));

            return $this->successResponse("Appointment retrieved successfully", $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointments), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
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
