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
            'content' => $this->description,
            'url' => $this->url,
            'image' => $this->image,
            'published_at' => $this->published_at,
            'is_active' => $this->is_active,
            'source' => [
                'id' => $this->sourceable?->id,
                'name' => $this->sourceable?->name,
                'logo' => $this->sourceable?->logo,
            ],
            'categories' => $this->categories->map(function($category) {
                return [
                    'id' => $category?->id,
                    'name' => $category->name,
                    'type' => $category->type,
                ];
            }),
            'tags' => $this->tags->map(function($tag) {
                return [
                    'id' => $tag?->id,
                    'name' => $tag->name,
                ];
            }),
            'locations' => $this->locations->map(function($location) {
                return [
                    'id' => $location?->id,
                    'name' => $location?->name,
                    'coordinates' => [
                        'lat' => $location->location ? $location->location->getLat() : null,
                        'lng' => $location->location ? $location->location->getLng() : null,
                    ],
                    'country' => $location->country?->name,
                    'state' => $location->state?->name,
                ];
            }),
            'subnews' => NewsResource::collection($this->whenLoaded('childNews')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}