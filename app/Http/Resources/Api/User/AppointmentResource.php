<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Admin\UserResource;
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
            "id" => $this->id,
            "userId" => $this->user_id,
            "user" => new UserResource($this->whenLoaded("user")),
            "hospitalId" => $this->hospital_id,
            "bloodRequestId" => $this->blood_request_id,
            "appointmentDate" => $this->appointment_date,
            "appointmentTime" => $this->appointment_time,
            "status" => $this->status,
            "remarks" => $this->remarks
        ];
    }
}
