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
            'user' => new UserResource($this->whenLoaded('user')),
            'blood_group' => $this->blood_group,
            'gender' => $this->gender,
            'age' => \Carbon\Carbon::parse($this->date_of_birth)->age,
            'last_donation' => $this->last_donation_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
        ];
    }
}
