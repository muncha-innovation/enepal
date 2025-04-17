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
                if (method_exists($this->sender, 'name')) {
                    return $this->sender->name;
                }
                return null;
            }),
            'content' => $this->content,
            'attachments' => $this->attachments,
            'is_notification' => $this->is_notification,
            'is_read' => $this->is_read,
            'opened_at' => $this->opened_at,
            'has_attachments' => $this->hasAttachments(),
            'notification_content' => $this->when($this->is_notification, fn() => $this->getNotificationContent()),
            'attachment_details' => $this->when($this->hasAttachments(), function() {
                return collect($this->attachments)->map(function($attachment) {
                    return [
                        'name' => $attachment['name'] ?? null,
                        'path' => $attachment['path'] ?? null,
                        'url' => $this->getAttachmentUrl($attachment),
                        'extension' => $this->getAttachmentExtension($attachment),
                        'is_image' => $this->isAttachmentImage($attachment),
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
