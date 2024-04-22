<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->content,
            'created_at' => $this->created_at,
            'image' => getImage($this->image, 'posts/'),
            'business' => BusinessResource::make($this->business),
            'likes' => $this->likes->count(),
            'comments_count' => $this->comments->count(), 
            'user' => UserResource::make($this->user),
        ];
    }
}
