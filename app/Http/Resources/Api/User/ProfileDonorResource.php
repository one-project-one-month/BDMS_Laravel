<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDonorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Data from the 'donors' table
            'donorId' => $this->id,
            'nrcNo' => $this->nrc_no,
            'dateOfBirth' => $this->date_of_birth,
            'gender' => $this->gender,
            'bloodGroup' => $this->blood_group,
            'weight' => $this->weight,
            'lastDonationDate' => $this->last_donation_date,
            'remarks' => $this->remarks,
            'emergencyContact' => $this->emergency_contact,
            'emergencyPhone' => $this->emergency_phone,
            'address' => $this->address,
            'isActive' => $this->is_active,

            // Data from the 'users' table
            'profile' => [
                'userId'       => $this->user->id,
                'fullName'     => $this->user->user_name,
                'email'        => $this->user->email,
            ],

            'created_at'       => $this->created_at->format('Y-m-d'),
        ];
    }
}
