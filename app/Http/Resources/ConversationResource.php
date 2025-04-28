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
            'title' => $this->title??$this->business->name,
            'business' => $this->when($this->business, BusinessResource::make($this->business)),
            'business_id' => $this->business_id,
            'user' => $this->when($this->user, UserResource::make($this->user)),
            'user_id' => $this->user_id,
            'vendor' => $this->when($this->vendor, $this->vendor),
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
