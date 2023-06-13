<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ImageManipulationResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="ImageManipulationResource",
     *     type="object",
     *     title="ImageManipulationResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="original", type="string", format="url"),
     *     @OA\Property(property="output", type="string", format="url"),
     *     @OA\Property(property="album", ref="#/components/schemas/AlbumResource"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'original' => URL::to($this->path),
            'output' => URL::to($this->output_path),
            'album' => new AlbumResource($this->album),
            'created_at' => $this->created_at,
        ];
    }
}
