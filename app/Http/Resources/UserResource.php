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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => getImage($this->profile_picture, 'profile/'),
            'primaryAddress' => AddressResource::make($this->primaryAddress),
            'birthAddress' => AddressResource::make($this->birthAddress),
            'addresses' => AddressResource::collection($this->addresses),
        ];
    }
}