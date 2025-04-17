<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'conversation_id',
        'status',
        'description',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the thread.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get all messages of the thread.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the latest message of the thread.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Check if the thread has unread messages.
     */
    public function hasUnreadMessages()
    {
        return $this->unreadMessagesCount() > 0;
    }

    /**
     * Count unread messages in the thread.
     */
    public function unreadMessagesCount()
    {
        // Get the current user
        $user = auth()->user();
        
        // Base query to filter messages not sent by the current user
        $query = $this->messages()->where('is_read', false);
        
        // Filter based on user type
        $userClass = get_class($user);
        $query->where('sender_type', '!=', $userClass)
              ->orWhere(function($q) use ($userClass, $user) {
                  $q->where('sender_type', $userClass)
                    ->where('sender_id', '!=', $user->id);
              });
              
        return $query->count();
    }
}
