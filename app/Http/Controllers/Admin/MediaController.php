<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Category;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->validate(
            [
                'media' => 'required|file',
                'alt'   => 'nullable',
                'key'   => 'required',
            ]
        );

        $paths = $this->makeThumbnail($attributes['key'], $request->file('media'));

        $media = Media::create(
            [
                'name'          => basename($paths['media_url']),
                'extension'     => $attributes['media']->extension(),
                'url'           => Storage::disk()->url($paths['media_url']),
                'alt'           => isset($attributes['alt']) ? $attributes['alt'] : null,
                'thumbnail_url' => Storage::disk()->url($paths['thumbnail_url']),
            ]
        );

        return (new MediaResource($media))->response()->setStatusCode(201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media  $media
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media  $media
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $key
     * @param $file
     *
     * @return array
     */
    protected function makeThumbnail($key, $file)
    {
        $categoryKey = explode('_', $key);
        $key         = $categoryKey[0];
        $categoryId  = $categoryKey[1];

        $category = Category::find($categoryId);

        $media_path = $file->store($key);

        $thumbnail_name = 'thumbnail_'.basename($media_path);
        $thumbnail_path = storage_path('app/public/')."{$key}/".$thumbnail_name;

        $thumbnail_width  = $category->thumbnail_width ?? Setting::where('key', 'thumbnail_width')->first()?->thumbnail_width;
        $thumbnail_height = $category->thumbnail_height ?? Setting::where('key', 'thumbnail_height')->first()?->thumbnail_height;

        Image::make(Storage::disk()->get($media_path))
            ->fit($thumbnail_width ?? 100, $thumbnail_height ?? 100)
            ->save($thumbnail_path);


        return [
            'media_url'     => $media_path,
            'thumbnail_url' => "{$key}/".$thumbnail_name,
        ];
    }
}
