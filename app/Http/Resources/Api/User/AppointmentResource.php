<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Admin\HospitalResource;
use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserAppointmentResource",
 * title="User Appointment details for blood donation/request (User Only)",
 * @OA\Property(
 * property="id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="userId",
 * type="integer",
 * example=10
 * ),
 * @OA\Property(
 * property="user",
 * ref="#/components/schemas/UserResource"
 * ),
 * @OA\Property(
 * property="hospitalId",
 * type="integer",
 * example=5
 * ),
 * @OA\Property(
 * property="hospital",
 * ref="#/components/schemas/HospitalResource"
 * ),
 * @OA\Property(
 * property="bloodRequestId",
 * type="integer",
 * example=101
 * ),
 * @OA\Property(
 * property="appointmentDate",
 * type="string",
 * format="date",
 * example="2026-03-25"
 * ),
 * @OA\Property(
 * property="appointmentTime",
 * type="string",
 * example="10:30 AM"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * example="scheduled"
 * ),
 * @OA\Property(
 * property="remarks",
 * type="string",
 * example="Please fast for 4 hours before the appointment."
 * )
 * )
 */
class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "userId" => $this->user_id,
            "user" => new UserResource($this->whenLoaded("user")),
            "hospitalId" => $this->hospital_id,
            "hospital" => new HospitalResource($this->whenLoaded("hospital")),
            "bloodRequestId" => $this->blood_request_id,
            "appointmentDate" => $this->appointment_date,
            "appointmentTime" => $this->appointment_time,
            "status" => $this->status,
            "remarks" => $this->remarks
        ];
    }
}
