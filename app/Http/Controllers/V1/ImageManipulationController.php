<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResizeImageRequest;
use App\Http\Resources\V1\ImageManipulationResource;
use App\Models\Album;
use App\Models\ImageManipulation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageManipulationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/images",
     *     operationId="getImageManipulations",
     *     tags={"Images"},
     *     summary="Get a list of image manipulations",
     *     description="Returns a paginated list of image manipulations for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ImageManipulationResource")
     *         ),
     *     ),
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $userId = auth()->user()->id;
        $imageManipulation = ImageManipulation::whereUserId($userId)->paginate();

        return ImageManipulationResource::collection($imageManipulation);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/images/by-album/{album}",
     *     operationId="getImagesByAlbum",
     *     tags={"Images"},
     *     summary="Get images by album",
     *     description="Returns a paginated list of images belonging to a specific album for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="album",
     *         in="path",
     *         description="ID of the album",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ImageManipulationResource")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function byAlbum(Album $album): AnonymousResourceCollection
    {
        $userId = auth()->user()->id;

        if ($album->user_id != $userId) {
            return abort(403, 'Unauthorized action.');
        }

        $imageManipulation = ImageManipulation::where([
            'user_id' => $userId,
            'album_id' => $album->id
        ])->paginate();

        return ImageManipulationResource::collection($imageManipulation);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/images/{image}",
     *     operationId="getImage",
     *     tags={"Images"},
     *     summary="Get image details",
     *     description="Returns the details of a specific image for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="image",
     *         in="path",
     *         description="ID of the image",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ImageManipulationResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function show(ImageManipulation $image): ImageManipulationResource
    {
        $userId = auth()->user()->id;

        if ($image->user_id != $userId) {
            return abort(403, 'Unauthorized action.');
        }

        return new ImageManipulationResource($image);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/images/resize",
     *     operationId="resizeImage",
     *     tags={"Images"},
     *     summary="Resize an image",
     *     description="Resizes an image and stores the manipulated image for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResizeImageRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ImageManipulationResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function resize(ResizeImageRequest $request): ImageManipulationResource
    {
        $all = $request->validated();

        $userId = auth()->user()->id;

        $image = $all['image'];
        unset($all['image']);
        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all), // Convert data structure into a json
            'user_id' => $userId,
        ];

        $album = Album::find($all['album_id']);
        if ($album->user_id != $userId) {
            return abort(403, 'Unauthorized');
        }

        $data['album_id'] = $all['album_id'];

        $dir = 'images/' . Str::random() . '/';
        $absolutePath = public_path($dir);
        if (!File::makeDirectory($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }

        if ($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName();

            $filename = pathinfo($data['name'], PATHINFO_BASENAME);

            $extension = $image->getClientOriginalExtension();

            $originalPath = $absolutePath . $data['name'];

            $data['path'] = $dir . $data['name'];

            $image->move($absolutePath, $data['name']);
        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);

            $filename = pathinfo($image, PATHINFO_FILENAME);

            $extension = pathinfo($image, PATHINFO_EXTENSION);

            $originalPath = $absolutePath . $data['name'];

            copy($image, $originalPath);

            $data['path'] = $dir . $data['name'];
        }

        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($image, $with, $height) = $this->getImageWidthAndHeight($w, $h, $originalPath);

        $resizedFilename = $filename . '-resized.' . $extension;
        $image->resize($with, $height)->save($absolutePath . $resizedFilename);

        $data['output_path'] = $dir . $resizedFilename;

        $imageManipulation = ImageManipulation::create($data);

        return new ImageManipulationResource($imageManipulation);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/images/{image}",
     *     operationId="destroyImage",
     *     summary="Delete an image manipulation",
     *     tags={"Images"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="image",
     *         in="path",
     *         required=true,
     *         description="ID of the image manipulation",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image manipulation deleted",
     *         @OA\JsonContent(ref="#/components/schemas/ImageManipulationResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image manipulation not found"
     *     )
     * )
     */
    public function destroy(ImageManipulation $image): ImageManipulationResource
    {
        $userId = auth()->user()->id;

        if ($image->user_id != $userId) {
            return abort(403, 'Unauthorized');
        }

        $image->delete();

        return new ImageManipulationResource($image);
    }

    /**
     * @param mixed $w
     * @param mixed $h
     * @param string $originalPath
     * @return array
     */
    protected function getImageWidthAndHeight(mixed $w, mixed $h, string $originalPath): array
    {
        $image = Image::make($originalPath);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if (str_ends_with($w, '%')) {
            $ratioW = (float)str_replace('%', '', $w);
            $ratioH = $h ? (float)str_replace('%', '', $h) : $ratioW;

            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;
        } else {
            $newWidth = (float)$w;
            $newHeight = $h ? (float)$h : $originalHeight * $newWidth / $originalWidth;
        }

        return [$image, $newWidth, $newHeight];
    }
}
