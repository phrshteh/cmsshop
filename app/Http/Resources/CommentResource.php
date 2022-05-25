<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id'          => $this->resource->id,
            'name'        => $this->resource->name,
            'body'        => $this->resource->body,
            'ip'          => $this->resource->ip,
            'content_id'  => $this->resource->content_id,
            'parent_id'   => $this->resource->parent_id,
            'approved_at' => $this->resource->approved_at,
            'created_at'  => $this->resource->created_at,
            'content'     => $this->resource->content()->first()?->title,
            'category'    => $this->resource->content()->first()?->category()->first()?->title,
        ];
    }
}
