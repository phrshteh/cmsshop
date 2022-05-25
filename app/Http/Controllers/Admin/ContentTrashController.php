<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContentResource;
use App\Http\Resources\ContentCollection;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NullResource;
use App\Models\Content;
use Exception;
use Illuminate\Http\Request;

class ContentTrashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contents = Content::query()->onlyTrashed();

        if (\request()->filled('category_id')) {
            $contents->where('category_id', request()->input('category_id'));
        }

        $data = $contents->with('category')->latest()
            ->paginate(\request()->input('per_page') ?? 15);

        return (new ContentCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $content = Content::withTrashed()->findOrFail($id);

        $content->restore();
        $content->refresh();

        return (new ContentResource($content))->response()->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $content = Content::withTrashed()->findOrFail($id);
            $content->forceDelete();

        } catch (Exception $exception) {

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
