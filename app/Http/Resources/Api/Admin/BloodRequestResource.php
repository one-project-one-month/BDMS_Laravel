<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blood_request_code' => $this->blood_request_code,
            'patient_name' => $this->patient_name,
            'blood_group' => $this->blood_group?->value,
            'units_required' => $this->units_required,
            'urgency' => $this->urgency?->value,
            'status' => $this->status?->value,
            'required_date' => $this->required_date,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
        ];
    }
}