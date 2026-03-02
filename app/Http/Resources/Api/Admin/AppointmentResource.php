<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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