<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Thread;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Thread channel - authorization
Broadcast::channel('thread.{threadId}', function ($user, $threadId) {
    $thread = Thread::find($threadId);
    
    if (!$thread) {
        return false;
    }
    
    // Business users can access their business's threads
    if ($user->hasRole('business_admin') || $user->hasRole('business_user')) {
        return $thread->conversation->business_id === $user->business_id;
    }
    
    // Regular users can access their own threads
    return $thread->conversation->user_id === $user->id;
});

// Conversation channel - authorization
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // Business users can access their business's conversations
    if ($user->hasRole('business_admin') || $user->hasRole('business_user')) {
        return $conversation->business_id === $user->business_id;
    }
    
    // Regular users can access their own conversations
    return $conversation->user_id === $user->id;
});
