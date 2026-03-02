<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Api\Admin\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Appointments",
 * description="API Endpoints for managing appointments"
 * )
 */
class AppointmentsController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/appointments",
     * summary="Get list of all appointments",
     * description="Returns a collection of appointments.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful appointments",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/AppointmentResource")
     * ),
     * @OA\Property(property="message", type="string", example="Appointments retrieved successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden"
     * )
     * )
     */
    public function index()
    {
        $appointments = Appointment::with(['user', 'hospital'])
            ->latest()
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Appointments Index', AppointmentResource::collection($appointments));
    }

    /**
     * @OA\Put(
     * path="/api/v1/appointments/{id}",
     * summary="Update an existing appointment",
     * description="Update the date, time, or status of an appointment.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the appointment to update",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="hospitalId", type="integer", example=1),
     * @OA\Property(property="bloodRequestId", type="integer", example=2),
     * @OA\Property(property="appointmentDate", type="string", format="date", example="2026-03-10"),
     * @OA\Property(property="appointmentTime", type="string", example="02:30 PM"),
     * @OA\Property(property="remark", type="string", example="Updating the time due to schedule change."),
     * @OA\Property(property="status", type="boolean", example=true)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Appointment updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Appointment updated successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     * )
     * ),
     * @OA\Response(response=400, description="Bad Request"),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Appointment not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled, cancelled, confirmed, completed',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Validation Error", 422);
        }

        try {

            $appointment->update($validator->validated());

            return $this->successResponse("Appointment Updated Successfully!", new AppointmentResource($appointment));

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
