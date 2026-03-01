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
            'user_id' => $this->user_id,
            'hospital_id' => $this->hospital_id,
            'donation_id' => $this->donation_id,
            'blood_request_id' => $this->blood_request_id,
            'appointment_date' => $this->appointment_date->toDateString(),
            'appointment_time' => $this->appointment_time->format('H:i:s'),
            'status' => $this->status,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
        ];
    }
}
