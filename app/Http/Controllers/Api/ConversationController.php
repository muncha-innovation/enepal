<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Get all conversations for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Conversation::query();
        
        $query->where('user_id', $user->id);
        
        
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }
        
        // Apply status filter if provided
        if ($request->has('status')) {
            $status = $request->input('status');
            $query->whereHas('threads', function($q) use ($status) {
                $q->where('status', $status);
            });
        }
        
        // Load necessary relationships
        $query->with(['defaultThread.latestMessage','business', 'user', 'vendor']);
        
        // Order by latest message
        $query->orderBy('updated_at', 'desc');
        
        // Paginate the results
        $conversations = $query->paginate($request->input('per_page', 15));
        
        return ConversationResource::collection($conversations);
    }
    
    /**
     * Get a specific conversation with threads
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        
        $conversation->load(['threads', 'defaultThread']);
        
        return new ConversationResource($conversation);
    }
    
    /**
     * Create a new conversation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'business_id' => 'required_without:user_id|exists:businesses,id',
            'user_id' => 'required_without:business_id|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);
        
        $user = Auth::user();
        
        // Check if the user can create this conversation
        if (isset($validated['business_id'])) {
            $this->authorize('create-business-conversation', [$user, $validated['business_id']]);
        }
        
        // Create the conversation
        $conversation = Conversation::create($validated);
        
        // Create the default thread
        $defaultThread = $conversation->threads()->create([
            'title' => 'General',
            'status' => 'open',
        ]);
        
        // Add initial message
        $defaultThread->messages()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'content' => 'Conversation started',
        ]);
        
        $conversation->load(['threads', 'defaultThread']);
        
        return new ConversationResource($conversation);
    }
    
    /**
     * Update a conversation
     */
    public function update(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
        ]);
        
        $conversation->update($validated);
        
        return new ConversationResource($conversation);
    }
    
    /**
     * Delete a conversation
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);
        
        // Delete all threads and messages
        foreach ($conversation->threads as $thread) {
            $thread->messages()->delete();
        }
        $conversation->threads()->delete();
        
        // Finally delete the conversation
        $conversation->delete();
        
        return response()->json(['message' => 'Conversation deleted successfully']);
    }
}
