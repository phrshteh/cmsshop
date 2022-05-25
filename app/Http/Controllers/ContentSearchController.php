<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContentCollection;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $contents = Content::query();

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

        $data = $contents->where('description' , 'like' , '%'.\request()->input('search').'%')
            ->paginate(\request()->input('per_page') ?? 15);

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
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
