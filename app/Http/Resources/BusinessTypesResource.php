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
                'address' => $this->getAddress($business),
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

    private function getAddress($business) {
        return [
            'country' => $business->address?->country?->name,
            'city' => $business->address?->city,
            'state' => $business->address?->state?->name,
            'postal_code' => $business->address->postal_code,
            'address_line_1' => $business->address->address_line_1,
            'address_line_2' => $business->address->address_line_2,
        ];
    }
}
