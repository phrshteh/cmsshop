<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Content;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
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
                'name'       => 'required|string',
                'body'       => 'required|string',
                'content_id' => 'required|exists:contents,id',
            ]
        );

        $attributes['ip'] = $request->ip();

        if (Comment::where('ip', $attributes['ip'])->where('body', $attributes['body'])->first()) {
            $data = [
                'message' => trans('messages.comments.failed.already_exists'),
            ];

            return (new CommentResource($data))->response()->setStatusCode(423);
        }

        $content = Content::findOrFail($attributes['content_id']);

        $comment = $content->comments()->create($attributes);

        return (new CommentResource($comment))->response()->setStatusCode(201);
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
     * @param  Comment  $comment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
