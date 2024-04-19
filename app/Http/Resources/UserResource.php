<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lang = $request->query('lang');
        $lang = $lang ?? 'en';
        return [
            'id' => $this->id,
            'token' => $this->createToken('enepal')->plainText,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => getImage($this->image, 'profile/'),
            'address' => $this->getAddress()
        ];
    }

    private function getAddress()
    {
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