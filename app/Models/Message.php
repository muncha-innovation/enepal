<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'thread_id',
        'sender_id',
        'sender_type',
        'content',
        'attachments',
        'is_notification',
        'is_read',
        'opened_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_notification' => 'boolean',
        'is_read' => 'boolean',
        'opened_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the thread that owns the message.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Check if the message has attachments.
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * Get the notification content for notification messages.
     */
    public function getNotificationContent()
    {
        if (!$this->is_notification) {
            return null;
        }

        // Extract notification info from the content
        // For now, just return the content directly
        return $this->content;
    }

    /**
     * Get the URL for an attachment.
     */
    public function getAttachmentUrl($attachment)
    {
        if (isset($attachment['path'])) {
            return Storage::url($attachment['path']);
        }
        return null;
    }

    /**
     * Get the extension of an attachment.
     */
    public function getAttachmentExtension($attachment)
    {
        if (isset($attachment['name'])) {
            return pathinfo($attachment['name'], PATHINFO_EXTENSION);
        }
        return null;
    }

    /**
     * Check if an attachment is an image.
     */
    public function isAttachmentImage($attachment)
    {
        if (isset($attachment['mime'])) {
            return strpos($attachment['mime'], 'image/') === 0;
        }
        
        $extension = $this->getAttachmentExtension($attachment);
        if ($extension) {
            return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        }
        
        return false;
    }
}
