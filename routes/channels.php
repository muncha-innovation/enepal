<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Thread;
use App\Models\Conversation;
use App\Models\User;

Broadcast::channel('User-{id}', function ($user, $id) {
    return true;
});

Broadcast::channel('thread-{threadId}', function ($user, $threadId) {
   return true;
});

Broadcast::channel('conversation-{conversationId}', function ($user, $conversationId) {
    return true;
});

