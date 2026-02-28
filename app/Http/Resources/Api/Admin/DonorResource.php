<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
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
            'nrcNo' => $this->nrc_no,
            'bloodGroup' => $this->blood_group,
            'address' => $this->address,
            'dateOfBirth' => $this->date_of_birth,
            'lastDonationDate' => $this->last_donation_date,
            'totalDonation' => $this->total_donations,
            'medicalNote' => $this->medical_notes,
            'emergencyContact' => $this->emergency_contact,
            'emergencyPhone' => $this->emergency_phone,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
