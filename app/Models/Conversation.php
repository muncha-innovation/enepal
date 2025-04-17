<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'business_id',
        'user_id',
        'vendor_id',
    ];

    /**
     * Get the business that owns the conversation.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user that owns the conversation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor that owns the conversation.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get all threads of the conversation.
     */
    public function threads()
    {
        return $this->hasMany(Thread::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the default thread of the conversation.
     */
    public function defaultThread()
    {
        return $this->hasOne(Thread::class)->oldest();
    }

    /**
     * Get all messages of the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Check if the conversation has unread messages.
     */
    public function hasUnreadMessages()
    {
        return $this->unreadMessagesCount() > 0;
    }

    /**
     * Count unread messages in the conversation.
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
