<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
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
            'image' => getImage($this->image, '/'),
            'thumbnail' => $this->getThumbnailAttribute(),
        ];
    }
}
