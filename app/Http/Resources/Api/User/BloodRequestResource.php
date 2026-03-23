<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Admin\HospitalResource;
use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserBloodRequestResource",
 * title="User Blood Request Resource (User Only)",
 * @OA\Property(
 * property="id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="userId",
 * type="integer",
 * example=123
 * ),
 * @OA\Property(
 * property="user",
 * ref="#/components/schemas/UserResource"
 * ),
 * @OA\Property(
 * property="hospitalId",
 * type="integer",
 * example=45
 * ),
 * @OA\Property(
 * property="hospital",
 * ref="#/components/schemas/HospitalResource"
 * ),
 * @OA\Property(
 * property="bloodType",
 * type="string",
 * example="O+"
 * ),
 * @OA\Property(
 * property="unitsRequired",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="reason",
 * type="string",
 * example="Emergency Surgery"
 * ),
 * @OA\Property(
 * property="status",
 * type="string",
 * example="pending"
 * )
 * )
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
        return
            [
                "id" => $this->id,
                "userId" => $this->user_id,
                "user" => new UserResource($this->whenLoaded("user")),
                "hospitalId" => $this->hospital_id,
                "hospital" => new HospitalResource($this->whenLoaded("hospital")),
                "bloodType" => $this->blood_type,
                "unitsRequired" => $this->units_required,
                "reason" => $this->reason,
                "status" => $this->status
            ];

    }
}
