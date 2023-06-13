<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @OA\Schema(
 *     schema="ResizeImageRequest",
 *     required={"image", "w"},
 *     @OA\Property(property="image", type="string", description="The image to resize. Accepts either a file upload or a URL."),
 *     @OA\Property(property="w", type="string", description="The target width of the resized image. Accepts either a number in pixels or a percentage."),
 *     @OA\Property(property="h", type="string", description="The target height of the resized image. Accepts either a number in pixels or a percentage."),
 *     @OA\Property(property="album_id", type="integer", description="The ID of the album to associate the resized image with."),
 * )
 */
class ResizeImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'image' => 'required',
            'w' => ['required', 'regex:/^\d+(\.\d+)?%?$/'],
            'h' => 'regex:/^\d+(\.\d+)?%?$/',
            'album_id' => 'exists:albums,id',
        ];

        $image = $this->all()['image'] ?? false;
        if ($image && $image instanceof UploadedFile) {
            $rules['image'] = 'image';
        } else {
            $rules['image'] = 'url';
        }

        return $rules;
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'w.regex' => 'Please specify width as a valid number in pixels or in %',
            'h.regex' => 'Please specify height as a valid number in pixels or in %',
        ];
    }
}
