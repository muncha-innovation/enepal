<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'conversation_id' => $this->conversation_id,
            'thread_id' => $this->thread_id,
            'sender_id' => $this->sender_id,
            'sender_type' => $this->sender_type,
            'sender' => $this->when($this->sender, function() {
                if ($this->sender instanceof \App\Models\User) {
                    return [
                        'name' => $this->sender->first_name . ' ' . $this->sender->last_name,
                        'type' => 'user'
                    ];
                } else if ($this->sender instanceof \App\Models\Business) {
                    return [
                        'name' => $this->sender->name,
                        'type' => 'business'
                    ];
                }
                return null;
            }),
            'content' => $this->content,
            'attachments' => $this->attachments,
            'is_read' => $this->is_read,
            'opened_at' => $this->opened_at,
            'has_attachments' => $this->hasAttachments(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
