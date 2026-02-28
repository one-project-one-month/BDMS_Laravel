<?php

namespace App\Http\Resources\Api\Admin;

use App\Http\Resources\Api\Admin\DonorResource;
use App\Http\Resources\Api\Admin\HospitalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'hospitalId' => $this->hospital_id,
            'user_name' => $this->user_name,
            'phone' => $this->phone,
            'role' => $this->role,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'donorInfo' => new DonorResource($this->whenLoaded('donor')),
        ];
    }
}
