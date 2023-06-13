<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Http\Resources\V1\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="sanctum",
 * )
 */
/**
 * @OA\Info(
 *     title="Album API",
 *     version="1.0.0",
 *     description="Album API Crud"
 * )
 */
class AlbumController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/albums",
     *     operationId="getAlbumsList",
     *     tags={"Albums"},
     *     summary="Get a list of albums",
     *     description="Returns a paginated list of albums for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AlbumResource"),
     *     ),
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $userId = auth()->user()->id;
        $albums = Album::whereUserId($userId)->paginate();

        return AlbumResource::collection($albums);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/albums",
     *     operationId="storeAlbum",
     *     tags={"Albums"},
     *     summary="Create a new album",
     *     description="Creates a new album for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAlbumRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AlbumResource"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     * )
     */
    public function store(StoreAlbumRequest $request): AlbumResource
    {
        $data = $request->validated();

        $data['user_id'] = auth()->user()->id;

        $album = Album::create($data);

        return new AlbumResource($album);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/albums/{id}",
     *     operationId="getAlbumById",
     *     tags={"Albums"},
     *     summary="Get album details",
     *     description="Returns the details of a specific album for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the album",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AlbumResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function show(Album $album): AlbumResource
    {
        if ($album->user_id != auth()->user()->id) {
            return abort(403, 'Unauthorized action.');
        }

        return new AlbumResource($album);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/albums/{id}",
     *     operationId="updateAlbum",
     *     tags={"Albums"},
     *     summary="Update an album",
     *     description="Updates the details of a specific album for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the album",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAlbumRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AlbumResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function update(UpdateAlbumRequest $request, Album $album): AlbumResource
    {
        $data = $request->validated();

        if ($album->user_id != auth()->user()->id) {
            return abort(403, 'Unauthorized action.');
        }

        $album->update($data);

        return new AlbumResource($album);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/albums/{id}",
     *     operationId="deleteAlbum",
     *     tags={"Albums"},
     *     summary="Delete an album",
     *     description="Deletes a specific album for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the album",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AlbumResource"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function destroy(Album $album)
    {
        if ($album->user_id != auth()->user()->id) {
            return abort(403, 'Unauthorized action.');
        }

        $album->delete();

        return new AlbumResource($album);
    }
}
