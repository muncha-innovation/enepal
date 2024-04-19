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
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'cover_image' => getImage($this->image, 'business/'),
            'profile_image' => getImage($this->logo, 'business/'),
            'phone' => $this->phone1,
            'email' => $this->email,
            'website' => $this->website,
            'facilities' => $this->facilities->map(function($facility) {
                return [
                    'id' => $facility->id,
                    'title' => $facility->title,
                    'icon' => getImage($facility->icon)
                ];
            }),
            'address' => AddressResource::make($this->address),
        ];
    }
}
