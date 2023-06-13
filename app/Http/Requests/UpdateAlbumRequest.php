<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * @OA\Schema(
     *     schema="UpdateAlbumRequest",
     *     title="Update Album Request",
     *     description="Update album request body parameters",
     *     @OA\Property(property="name", type="string", example="Updated Album 1"),
     * )
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255'
        ];
    }
}
