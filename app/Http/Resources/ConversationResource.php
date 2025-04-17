<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'business' => $this->when($this->business, $this->business->name),
            'business_id' => $this->business_id,
            'user' => $this->when($this->user, $this->user->name),
            'user_id' => $this->user_id,
            'vendor' => $this->when($this->vendor, $this->vendor->name),
            'vendor_id' => $this->vendor_id,
            'unread_count' => $this->unreadMessagesCount(),
            'has_unread' => $this->hasUnreadMessages(),
            'threads' => ThreadResource::collection($this->whenLoaded('threads')),
            'default_thread' => new ThreadResource($this->whenLoaded('defaultThread')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
