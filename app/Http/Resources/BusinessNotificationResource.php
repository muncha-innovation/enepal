<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessNotificationResource extends JsonResource
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
            'description' => $this->getTranslation('description', $lang),
            'image' => getImage($this->image, 'notices/'),
            'business' => new BusinessResource($this->whenLoaded('business')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
