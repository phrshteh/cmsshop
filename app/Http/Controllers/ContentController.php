<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContentCollection;
use App\Http\Resources\ContentResource;
use App\Models\Category;
use App\Models\Content;
use App\Models\ExtraValue;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contents = Content::query()->with(['cover', 'tags'])->latest('created_at');

        if (\request()->filled('slug')) {
            $slug = \request()->input('slug');
            if (is_array($slug)) {
                $contents->whereHas('category', function ($query) use ($slug) {
                    $query->whereIn('slug', $slug);
                });
            } else {
                $contents->whereHas('category', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                });
            }
        }

        if (\request()->filled('tags')) {
            $contents->whereHas('tags', function ($query) {
                $query->whereIn('title', \request()->input('tags'));
            });
        }

        //desc , asc
        if (\request()->filled('direction')) {
            $contents->orderBy(
                'created_at',
                \request()->input('direction') ?? 'desc'
            );
        }

        $data = $contents->paginate(\request()->input('per_page') ?? 15);

        return (new ContentCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Content  $content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Content $content)
    {
        $content = $content->load([
            'tags',
            'cover',
            'comments',
        ])->append('related_content');

        return (new ContentResource($content))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *k
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
