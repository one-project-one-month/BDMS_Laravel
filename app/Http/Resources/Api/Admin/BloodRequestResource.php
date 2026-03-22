<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * Transform the resource into an array.
 *
 * @return array<string, mixed>
 */
class BloodRequestResource extends JsonResource
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
            'patientName' => $this->patient_name,
            'contactPhone' => $this->contact_phone,
            'bloodRequestCode' => $this->blood_request_code,
            'urgencyLevel' => $this->urgency,
            'bloodGroup' => $this->blood_group,
            'unitsRequired' => $this->units_required,
            'reason' => $this->reason,
            'status' => $this->status,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'approvedBy' => $this->whenLoaded('approvedBy'),
            'requiredDate' => $this->required_date?->format('Y-m-d'),
            'approvedAt' => $this->approved_at?->format('Y-m-d'),
            'createdAt' => $this->created_at?->format('Y-m-d'),
            'updatedAt' => $this->updated_at?->format('Y-m-d'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d'),
        ];
    }
}

