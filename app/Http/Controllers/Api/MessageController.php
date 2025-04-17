<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Get messages for a thread with pagination
     */
    public function index(Request $request, Thread $thread)
    {
        $this->authorize('view', $thread->conversation);
        
        $messages = $thread->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 25));
            
        return MessageResource::collection($messages);
    }
    
    /**
     * Send a new message to a thread
     */
    public function store(Request $request, Thread $thread)
    {
        $this->authorize('send-message', $thread->conversation);
        
        $validated = $request->validate([
            'content' => 'required_without:attachments|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);
        
        $user = Auth::user();
        $attachments = [];
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat_attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }
        
        $message = new Message([
            'conversation_id' => $thread->conversation_id,
            'thread_id' => $thread->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'content' => $validated['content'] ?? '',
            'attachments' => $attachments,
            'is_notification' => false,
            'is_read' => false,
        ]);
        
        $message->save();
        
        // Update thread and conversation with last message time
        $thread->update([
            'last_message_at' => now(),
            'status' => 'open', // Reopen thread if it was closed
        ]);
        $thread->conversation->touch();
        
        // Broadcast the message event for real-time updates
        broadcast(new MessageSent($message))->toOthers();
        
        return new MessageResource($message);
    }
    
    /**
     * Get a specific message
     */
    public function show(Message $message)
    {
        $this->authorize('view', $message->conversation);
        
        return new MessageResource($message);
    }
    
    /**
     * Mark a message as read
     */
    public function markAsRead(Message $message)
    {
        $this->authorize('view', $message->conversation);
        
        if (!$message->is_read) {
            $message->update([
                'is_read' => true,
                'opened_at' => now(),
            ]);
        }
        
        return new MessageResource($message);
    }
    
    /**
     * Delete a message (soft delete)
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        
        // Delete attached files
        if ($message->hasAttachments()) {
            foreach ($message->attachments as $attachment) {
                if (isset($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }
        
        $message->delete();
        
        return response()->json(['message' => 'Message deleted successfully']);
    }
}
