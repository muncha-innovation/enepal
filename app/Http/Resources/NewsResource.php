<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'url' => $this->url,
            'image' => $this->image,
            'published_at' => $this->published_at,
            'is_active' => $this->is_active,
            'source' => [
                'id' => $this->source->id,
                'name' => $this->source->name,
                'logo' => $this->source->logo,
            ],
            'categories' => $this->categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                ];
            }),
            'tags' => $this->tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            }),
            'locations' => $this->locations->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'radius' => $location->radius,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 