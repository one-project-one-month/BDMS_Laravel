<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserCertificateResource",
 * title="User Certificate Resource (User Only)",
 * description="Certificate details mapping",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="userId", type="integer", example=10),
 * @OA\Property(
 * property="user",
 * ref="#/components/schemas/UserResource"
 * ),
 * @OA\Property(property="certificateTitle", type="string", example="Blood Donation Hero"),
 * @OA\Property(property="certificateDescription", type="string", example="Awarded for 5th donation"),
 * @OA\Property(property="certificateImage", type="string", format="uri", example="https://bdms.cloud/storage/cert.jpg"),
 * @OA\Property(property="certificateDate", type="string", format="date", example="2026-03-23"),
 * @OA\Property(property="createdAt", type="string", example="2026-03-23 10:41:58")
 * )
 */
class CertificateResource extends JsonResource
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
            'userId' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'certificateTitle' => $this->certificate_title,
            'certificateDescription' => $this->certificate_description,
            'certificateImage' => asset('storage/' . $this->certificate_image),
            'certificateDate' => $this->certificate_date,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
