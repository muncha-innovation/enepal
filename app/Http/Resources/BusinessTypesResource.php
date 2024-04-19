<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTypesResource extends JsonResource
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
            'description' => $this->description,
            'icon' => getImage($this->icon),
            'businesses' => $this->getBusinesses()
        ];
    }

    private function getBusinesses() {
        return $this->businesses->map(function($business) {
            return [
                'id' => $business->id,
                'title' => $business->title,
                'description' => $business->description,
                'image' => getImage($business->image, 'business/'),
                'address' => $this->getAddress(),
                'phone' => $business->phone,
                'email' => $business->email,
                'website' => $business->website,
                'facilities' => $business->facilities->map(function($facility) {
                    return [
                        'id' => $facility->id,
                        'title' => $facility->title,
                        'icon' => getImage($facility->icon)
                    ];
                })
            ];
        });
    }

    private function getAddress() {
        return [
            'country' => $this->address->country?->name,
            'city' => $this->address->city,
            'state' => $this->address->state?->name,
            'postal_code' => $this->address->postal_code,
            'address_line_1' => $this->address->address_line_1,
            'address_line_2' => $this->address->address_line_2,
        ];
    }
}
