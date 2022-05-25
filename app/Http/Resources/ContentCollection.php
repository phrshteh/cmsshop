<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($content) {
            return [
                'id'           => $content->id,
                'title'        => $content->title,
                'slug'         => $content->slug,
                'category_id'  => $content->category_id,
                'cover_id'     => $content->cover_id,
                'intro'        => $content->intro,
                'created_at'   => $content->created_at,
                'extra_fields' => $content->extra_fields,
                'category'     => $content->category()->first(),
                'cover'        => $content->cover()->first(),
                'tags'         => $content->tags()->get(),
            ];
        });
    }
}
