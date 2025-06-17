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
        $lang = $request->query('lang')??'en';
        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $lang),
            'body' => $this->getTranslation('content', $lang),
            'created_at' => $this->created_at,
            'image' => getImage($this->image, 'posts/'),
            'business' => BusinessResource::make($this->whenLoaded('business')),
            'likes' => $this->likes->count(),
            'comments_count' => $this->comments->count(), 
            'user' => UserResource::make($this->user),
            'has_liked' => $this->has_liked,
        ];
    }
}
