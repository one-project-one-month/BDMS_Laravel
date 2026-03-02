<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Admin\AppointmentRequest;
use App\Http\Resources\Api\Admin\AppointmentResource;
use App\Models\Appointment;

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

        return $this->successResponse('Appointment Lists', AppointmentResource::collection($appointments));
    }

    /**
     * @OA\Post(
     * path="/api/v1/appointments",
     * summary="Create a new appointment",
     * description="Creates a new appointment for a blood donation.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="hospitalId", type="integer", example=1),
     * @OA\Property(property="bloodRequestId", type="integer", example=2),
     * @OA\Property(property="appointmentDate", type="string", format="date", example="2026-03-10"),
     * @OA\Property(property="appointmentTime", type="string", example="02:30 PM"),
     * @OA\Property(property="remark", type="string", example="First appointment for this request.")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Appointment created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Appointment created successfully"),
     * @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     * )
     * ),
     * @OA\Response(response=400, description="Bad Request"),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(AppointmentRequest $request)
    {
        try {
            $appointment = Appointment::create($request->validated());
            return $this->successResponse("Appointment Created Successfully!", new AppointmentResource($appointment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/appointments/{id}",
     * summary="Get an appointment by ID",
     * description="Returns the details of a specific appointment.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the appointment to retrieve",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Appointment details",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Appointment details"),
     * @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function show(Appointment $appointment)
    {
        try {
            return $this->successResponse("Appointment Details", new AppointmentResource($appointment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        try {

            $appointment->update($request->validated());

            return $this->successResponse("Appointment Updated Successfully!", new AppointmentResource($appointment));

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/appointments/{id}/toggle-status",
     *     summary="Toggle appointment status",
     *     description="Change appointment status (scheduled, confirmed, cancelled, completed).",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"scheduled","confirmed","cancelled","completed"},
     *                 example="confirmed"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment Status Updated Successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Appointment not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
    */
    
    public function toggleStatus(AppointmentRequest $request, Appointment $appointment)
    {
        try{
            $appointment->status = $request->status;
            $appointment->save();

            return $this->successResponse("Appointment Status Updated Successfully", new AppointmentResource($appointment));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
