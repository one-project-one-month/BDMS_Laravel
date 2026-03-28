<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="ProfileDonorResource",
 * title="Profile Donor Resource (User Only)",
 * @OA\Property(property="donorId", type="integer", example=1),
 * @OA\Property(property="nrcNo", type="string", example="12/xxx(C)xxxxxx"),
 * @OA\Property(property="dateOfBirth", type="date", example="01-01-19xx"),
 * @OA\Property(property="gender", type="string", example="male"),
 * @OA\Property(property="bloodGroup", type="string", example="A+"),
 * @OA\Property(property="weight", type="integer", example="56"),
 * @OA\Property(property="lastDonationDate", type="date", example="01-01-20xx"),
 * @OA\Property(property="remarks", type="string", example="Hello from BDMS!"),
 * @OA\Property(property="emergencyContact", type="string", example="U Mg Mg"),
 * @OA\Property(property="emergencyPhone", type="string", example="09xxxxxxxxx"),
 * @OA\Property(property="address", type="string", example="Yangon"),
 * @OA\Property(property="isActive", type="boolean", example="true"),
 * @OA\Property(property="created_at", type="boolean", example="true"),
 *
 * )
 */
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
            'dateOfBirth' => $this->date_of_birth?->format('d-m-Y'),
            'gender' => $this->gender,
            'bloodGroup' => $this->blood_group,
            'weight' => $this->weight,
            'lastDonationDate' => $this->last_donation_date?->format('d-m-Y'),
            'remarks' => $this->remarks,
            'emergencyContact' => $this->emergency_contact,
            'emergencyPhone' => $this->emergency_phone,
            'address' => $this->address,
            'isActive' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
