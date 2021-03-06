<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
use App\Http\Resources\Admin\ContentCollection;
use App\Http\Resources\Admin\ContentResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NullResource;
use App\Models\Content;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contents = Content::query();

        if (\request()->filled('category_slug')) {
            $slug = \request()->input('category_slug');
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

        if (\request()->filled('slug')) {
            $contents->where('slug', \request()->input('slug'));
        }

        if (\request()->filled('category_id')) {
            $contents->where('category_id', request()->input('category_id'));
        }

        //desc , asc
        if (\request()->filled('direction')) {
            $contents->orderBy(
                'created_at',
                \request()->input('direction') ?? 'desc'
            );
        } else {
            $contents->orderBy('created_at', 'desc');
        }

        if (\request()->filled('dropdown')) {
            $data = $contents->get(['id', 'title']);
        } else {
            $data = $contents->with('category')
                ->paginate(\request()->input('per_page') ?? 15);
        }

        return (new ContentCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ContentRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ContentRequest $request)
    {
        $contentAttributes     =
            $request->safe(['category_id', 'title', 'slug', 'description', 'intro', 'cover_id']);
        $extraValuesAttributes = $request->safe(['fields']);
        $tagAttributes         = $request->safe(['tags']);

        DB::beginTransaction();
        try {
            $content = Content::create($contentAttributes);

            foreach ($extraValuesAttributes['fields'] as $attribute) {
                $content->extraValues()->create(
                    [
                        'extra_field_id' => $attribute['field_id'],
                        'value'          => $attribute['value'],
                    ]
                );
            }

            if ($tagAttributes['tags']) {
                foreach ($tagAttributes['tags'] as $attribute) {
                    $tag = Tag::firstOrCreate(
                        [
                            'title' => $attribute,
                        ]
                    );
                    $content->tags()->attach($tag);
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new ContentResource($content))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Content  $content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Content $content)
    {
        $data = $content->load([
            'category',
            'tags',
            'cover',
            'relatedContents',
        ]);

        return (new ContentResource($data))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ContentRequest  $request
     * @param  \App\Models\Content  $content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ContentRequest $request, Content $content)
    {
        $contentAttributes     =
            $request->safe(['category_id', 'title', 'slug', 'description', 'intro', 'cover_id']);
        $extraValuesAttributes = $request->safe(['fields']);
        $tagAttributes         = $request->safe(['tags']);

        DB::beginTransaction();
        try {
            $content->update($contentAttributes);

            $content->extraValues()->delete();
            foreach ($extraValuesAttributes['fields'] as $attribute) {
                $content->extraValues()->create(
                    [
                        'extra_field_id' => $attribute['field_id'],
                        'value'          => $attribute['value'],
                    ]
                );
            }

            $content->tags()->delete();
            foreach ($tagAttributes['tags'] as $attribute) {
                $tag = Tag::firstOrCreate(
                    [
                        'title' => $attribute,
                    ]
                );
                $content->tags()->attach($tag);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new ContentResource($content))->response()->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Content  $content
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Content $content)
    {
        try {
            $content->delete();
        } catch (Exception $exception) {
            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
