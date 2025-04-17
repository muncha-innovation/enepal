<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Models\Conversation;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{
    /**
     * List all threads in a conversation
     */
    public function index(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        
        $threads = $conversation->threads()
            ->with('latestMessage')
            ->orderBy('last_message_at', 'desc')
            ->paginate($request->input('per_page', 15));
            
        return ThreadResource::collection($threads);
    }
    
    /**
     * Get a specific thread with messages
     */
    public function show(Request $request, Thread $thread)
    {
        $this->authorize('view', $thread->conversation);
        
        // Mark messages as read if requested
        if ($request->has('mark_as_read') && $request->input('mark_as_read')) {
            $this->markThreadAsRead($thread);
        }
        
        // Load messages with pagination
        $thread->load(['latestMessage']);
        
        return new ThreadResource($thread);
    }
    
    /**
     * Create a new thread in a conversation
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:open,closed',
        ]);
        
        $validated['status'] = $validated['status'] ?? 'open';
        $validated['last_message_at'] = now();
        
        $thread = $conversation->threads()->create($validated);
        
        // Add initial message
        $user = Auth::user();
        $thread->messages()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'content' => 'Thread created: ' . $thread->title,
            'is_notification' => true,
        ]);
        
        return new ThreadResource($thread);
    }
    
    /**
     * Update a thread
     */
    public function update(Request $request, Thread $thread)
    {
        $this->authorize('update', $thread->conversation);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:open,closed',
        ]);
        
        $thread->update($validated);
        
        // If status changed, add a notification message
        if (isset($validated['status']) && $validated['status'] != $thread->getOriginal('status')) {
            $user = Auth::user();
            $thread->messages()->create([
                'conversation_id' => $thread->conversation_id,
                'sender_id' => $user->id,
                'sender_type' => get_class($user),
                'content' => 'Thread status changed to: ' . $validated['status'],
                'is_notification' => true,
            ]);
        }
        
        return new ThreadResource($thread);
    }
    
    /**
     * Delete a thread
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread->conversation);
        
        // Don't allow deleting the default thread
        if ($thread->conversation->threads()->count() === 1) {
            return response()->json(['message' => 'Cannot delete the default thread'], 422);
        }
        
        // Delete messages
        $thread->messages()->delete();
        
        // Delete the thread
        $thread->delete();
        
        return response()->json(['message' => 'Thread deleted successfully']);
    }
    
    /**
     * Mark all messages in the thread as read
     */
    protected function markThreadAsRead(Thread $thread)
    {
        $user = Auth::user();
        
        // Get the type of the current user
        $userClass = get_class($user);
        $userType = 'App\\Models\\' . class_basename($userClass);
        
        // Only mark messages from other types of users as read
        $thread->messages()
            ->where('is_read', false)
            ->where('sender_type', '!=', $userType)
            ->update(['is_read' => true, 'opened_at' => now()]);
            
        return response()->json(['message' => 'Messages marked as read']);
    }
    
    /**
     * Mark all messages in the thread as read (standalone endpoint)
     */
    public function markAsRead(Thread $thread)
    {
        $this->authorize('view', $thread->conversation);
        
        return $this->markThreadAsRead($thread);
    }
}
