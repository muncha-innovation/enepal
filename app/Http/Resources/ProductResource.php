<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'title' => $this->getTranslation('name', $lang),
            'description' => $this->getTranslation('description', $lang),
            'price' => $this->currency.' '.$this->price,
            'image' => getImage($this->image, 'products/'),
            'business' => new BusinessResource($this->whenLoaded('business')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
