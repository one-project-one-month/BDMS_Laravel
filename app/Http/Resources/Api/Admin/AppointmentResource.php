<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * @OA\Schema(
     * schema="AppointmentResource",
     * title="Appointment Resource",
     * description="Appointment model data wrapper",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="userId", type="integer", example=1),
     * @OA\Property(property="hospitalId", type="integer", example=1),
     * @OA\Property(property="bloodRequestId", type="integer", example=1),
     * @OA\Property(property="appointmentDate", type="string", format="date", example="2026-03-01"),
     * @OA\Property(property="appointmentTime", type="string", example="10:00 AM"),
     * @OA\Property(property="status", type="boolean", example=true),
     * @OA\Property(property="remark", type="string", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid incidunt voluptatum."),
     * @OA\Property(property="createdAt", type="string", format="date-time", example="2026-03-01T10:00:00Z"),
     * @OA\Property(property="updatedAt", type="string", format="date-time", example="2026-03-01T10:00:00Z"),
     * @OA\Property(property="deletedAt", type="string", format="date-time", nullable=true, example=null)
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'hospitalId' => $this->hospital_id,
            'donationId' => $this->donation_id,
            'bloodRequestId' => $this->blood_request_id,

            'appointmentDate' => $this->appointment_date?->toDateString(),
            'appointmentTime' => $this->appointment_time?->format('H:i'),

            'status' => $this->status,
            'remarks' => $this->remarks,

            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
            'deletedAt' => $this->deleted_at?->toDateTimeString(),
    ];
    }
}