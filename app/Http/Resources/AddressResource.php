<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'country' => $this->country?->name,
            'city' => $this->city,
            'country_id'=> $this->country?->id,
            'state_id' => $this->state?->id,
            'state' => $this->state?->name,
            'postal_code' => $this->postal_code,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'latitude' => $this->location?->getLat(),
            'longitude' => $this->location?->getLng(),
            'type' => $this->address_type
        ];
    }
}
