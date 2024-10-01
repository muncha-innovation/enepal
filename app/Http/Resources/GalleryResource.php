<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'title' => $this->title,
            'cover_image' => getImage($this->cover_image, '/'),
            'images' => GalleryImageResource::collection($this->images),
        ];
    }
}
