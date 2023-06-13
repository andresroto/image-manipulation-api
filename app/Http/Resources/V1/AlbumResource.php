<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="AlbumResource",
     *     title="Album Resource",
     *     description="Album resource representation",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="name", type="string", example="Album 1"),
     *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-06-01 10:30:00"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-06-02 15:45:00"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
