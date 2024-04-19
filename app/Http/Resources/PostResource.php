<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'body' => $this->content,
            'created_at' => $this->created_at,
            'image' => getImage($this->image, 'posts/'),
            'business' => $this->getBusiness(),
            'user' => $this->getUser(),
        ];
    }

    private function getBusiness() {
        return [
            'id' => $this->business->id,
            'title' => $this->business->title,
            'description' => $this->business->description,
            'image' => getImage($this->business->image, 'business/'),
            'address' => $this->getAddress($this->business),
            'phone' => $this->business->phone,
            'email' => $this->business->email,
            'website' => $this->business->website,
            'facilities' => $this->business->facilities->map(function($facility) {
                return [
                    'id' => $facility->id,
                    'title' => $facility->title,
                    'icon' => getImage($facility->icon)
                ];
            })
        ];
    }

    private function getUser() {
        return [
            'id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'image' => getImage($this->user->image, 'profile/'),
        ];
    }
}
