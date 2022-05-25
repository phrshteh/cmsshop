<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NullResource;
use App\Models\Comment;
use App\Models\Content;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comments = Comment::query()->latest();

        if (\request()->input('approved') == 'true') {
            $comments->where('approved_at', '<>', null);
        } elseif (\request()->input('approved') == 'false') {
            $comments->where('approved_at', null);
        }

        if (\request()->input('content_id')) {
            $comments->where('content_id', \request()->input('content_id'));
        }

        $data = $comments->paginate(\request()->input('per_page') ?? 15);

        return (new CommentCollection($data))->response()->setStatusCode(200);
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
                'parent_id'  => 'nullable|exists:comments,id',
                'content_id' => 'required|exists:contents,id',
            ]
        );

        $content = Content::findOrFail($attributes['content_id']);

        $comment = $content->comments()->create($attributes);

        return (new CommentResource($comment))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
        $data = $comment->load('content');

        return (new CommentResource($data))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Comment $comment)
    {
        $attributes = $request->validate([
            'approved_at' => 'required|boolean',
            'body'        => 'nullable|string',
            'name'        => 'required|string',
        ]);


        $comment->update([
            'approved_at' => $attributes['approved_at'] ? now() : null,
        ]);

        if ($attributes['body']) {
            $content_id = $comment->content()->first()?->id;

            Comment::create([
                'name'        => $attributes['name'],
                'body'        => $attributes['body'],
                'approved_at' => now(),
                'parent_id'   => $comment->id,
                'content_id'  => $content_id,
                'ip'          => \request()->ip(),
            ]);
        }

        $comment->refresh();

        return (new CommentResource($comment->refresh()))->response()
            ->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment->delete();

        } catch (Exception $exception) {

            return (new ExceptionResource($exception))->response()
                ->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
