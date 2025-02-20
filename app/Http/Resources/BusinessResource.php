<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
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
            'title' => $this->name,
            'description' => $this->getTranslation('description', $lang),
            'cover_image' => getImage($this->cover_image, 'business/cover_image/'),
            'profile_image' => getImage($this->logo, 'business/logo/'),
            'phone' => $this->phone1,
            'email' => $this->email,
            'website' => $this->website,
            'has_followed' => $this->has_followed,
            'is_admin' => $this->is_admin || $this->is_owner,
            'facilities' => $this->facilities->map(function($facility) {
                return [
                    'id' => $facility->id,
                    'title' => $facility->title,
                    'icon' => getImage($facility->icon)
                ];
            }),
            'address' => new AddressResource($this->whenLoaded('address')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'type' => BusinessTypesResource::make($this->whenLoaded('type')),
            'distance' => $this->when(isset($this->distance), function() {
                return round($this->distance, 2); // Returns distance in kilometers, rounded to 2 decimal places
            }),
        ];
    }
}
