<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request):array
    {
        return [
            'id' => $this->id,
            'donationId' =>$this->donation_id,
            'hospitalID' =>$this->hospital_id,
            'hemoglobinLevel' => $this->hemoglobin_level,
            'hivResult' => $this->hiv_result,
            'hepatitisBResult' => $this->hepatitis_b_result,
            'hepatitisCResult' => $this->hepatitis_c_result,
            'malariaResult' => $this->syphilis_result,
            'bloodGroup' => $this->blood_group,
            'screeningStatus' => $this->screening_status,
            'screeningNotes' => $this->screening_notes,
            "screeningBy" => $this->screening_by,
            'screeningAt' => $this->screening_at,
            'createdAt' => $this->created_at
        ];
    }
}