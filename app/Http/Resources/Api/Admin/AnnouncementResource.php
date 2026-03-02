<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="AnnouncementResource",
 * title="Announcement Resource",
 * description="Announcement model data wrapper",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="title", type="sting", example="Hello World"),
 * @OA\Property(property="content", type="string", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid incidunt voluptatum corrupti? Debitis aspernatur est magnam voluptatem! Ducimus, praesentium commodi numquam quis officiis accusantium, beatae dolor animi provident atque aliquam?"),
 * @OA\Property(property="isActive", type="boolean", example=true),
 * @OA\Property(property="expiredAt", type="boolean", example=false),
 * @OA\Property(property="createdAt", type="string", format="date-time", example="2026-03-01T10:00:00Z")
 * )
 */
class AnnouncementResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'isActive' => $this->is_active,
            'expiredAt' => $this->expired_at,
            'createdAt' => $this->created_at,
        ];
    }
}


