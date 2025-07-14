<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Business;
use App\Models\BusinessNotification;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\UserSegment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CommunicationsController extends Controller
{
    protected $businessNotificationController;

    public function __construct(BusinessNotificationController $businessNotificationController)
    {
        $this->businessNotificationController = $businessNotificationController;
    }

    public function getConversations(Business $business, Request $request)
    {
        $type = $request->get('type', 'chat');

        $segments = $business->segments()
            ->with('users')
            ->active()
            ->get();
        if ($type === 'chat') {
            $conversations = $business->conversations()
                ->with(['user'])
                ->withCount('messages')
                ->latest()
                ->get();

            // Load the latest message for each conversation separately
            $conversations->each(function ($conversation) {
                $conversation->latest_message = $conversation->messages()
                    ->latest()
                    ->first();
            });

            $unreadChats = Message::whereHas('conversation', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })->where('is_read', false)
                ->count();
            return view('modules.business.communications.index', compact(
                'business',
                'conversations',
                'unreadChats',
                'segments'
            ));
        } else {
            // Get paginated notifications
            $notifications = $business->notifications()
                ->where('is_active', true)
                ->latest()
                ->paginate(10); // Adjust per page as needed

            // Count unread notifications
            $unreadNotifications = 0;
            if (auth()->check()) {
                $user = auth()->user();
                $unreadNotificationsQuery = $business->notifications()
                    ->whereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->whereNull('read_at');
                    });
                $unreadNotifications = $unreadNotificationsQuery->count();
            }

            return view(
                'modules.business.communications.index',
                compact('business', 'notifications', 'unreadNotifications', 'segments')
            );
        }
    }



    /**
     * API endpoint to search users
     */
    public function searchUsers(Request $request)
    {
        $query = User::query()->select(['id', 'first_name', 'email']);

        if ($request->has('q')) {
            $searchTerm = $request->input('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->limit(10)->get();

        return response()->json([
            'results' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->first_name . ' (' . $user->email . ')'
                ];
            })
        ]);
    }

    public function createChat(Business $business, Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:user,segment',
            'user_id' => 'required_if:recipient_type,user|nullable|exists:users,id',
            'segment_id' => 'required_if:recipient_type,segment|nullable',
            'message' => 'required|string',
            'thread_title' => 'nullable|string|max:255'
        ]);

        $userIds = [];
        
        if ($request->recipient_type === 'user') {
            $userIds = [$request->user_id];
        } else {
            // Handle segment selection
            $segmentId = $request->segment_id;
            
            if (str_starts_with($segmentId, 'custom_')) {
                $actualSegmentId = (int) str_replace('custom_', '', $segmentId);
                
                $segment = UserSegment::where('id', $actualSegmentId)
                    ->where('business_id', $business->id)
                    ->first();
                    
                if ($segment) {
                    $userIds = $segment->users()->pluck('users.id')->toArray();
                }
            }
        }

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'No users found for the selected recipient.');
        }

        $createdConversations = [];
        
        foreach ($userIds as $userId) {
            $conversation = Conversation::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $userId
            ]);

            // Create or use the thread
            $threadTitle = $request->thread_title ?: 'General';
            $thread = $conversation->threads()->firstOrCreate(
                ['title' => $threadTitle],
                ['status' => 'open']
            );

            $message = new Message([
                'conversation_id' => $conversation->id,
                'thread_id' => $thread->id,
                'content' => $request->message,
                'sender_id' => $business->id,
                'sender_type' => Business::class,
            ]);

            $thread->messages()->save($message);

            // Update last_message_at timestamp
            $thread->update(['last_message_at' => now()]);
            $conversation->update(['last_message_at' => now()]);
            
            $createdConversations[] = $conversation;
        }

        $count = count($createdConversations);
        $successMessage = $count === 1 ? 'Chat started successfully' : "Chats started with {$count} users successfully";
        
        return redirect()->back()->with('success', $successMessage);
    }

    public function sendNotification(Business $business, Request $request)
    {
        // Delegate to BusinessNotificationController
        return $this->businessNotificationController->sendNotification($business, $request);
    }

    public function getMessages(Business $business, Conversation $conversation, Request $request)
    {
        try {
            Log::info('Loading conversation', [
                'business_id' => $business->id,
                'conversation_id' => $conversation->id,
                'is_ajax' => $request->ajax() || $request->query('ajax') == '1',
                'user_id' => $conversation->user_id ?? 'null'
            ]);

            // Check if the conversation belongs to this business
            if ($conversation->business_id != $business->id) {
                if ($request->ajax() || $request->query('ajax') == '1') {
                    return response()->view('modules.business.communications.error', [
                        'message' => 'This conversation does not belong to this business.'
                    ], 403);
                }

                return redirect()->route('business.communications.index', $business)
                    ->with('error', 'This conversation does not belong to this business.');
            }

            // Eagerly load the user relationship on the conversation
            $conversation->load(['user', 'threads']);

            // Get requested thread or default to the main thread
            $threadId = $request->input('thread_id');
            $thread = null;
            $isAllThread = $threadId === 'all';
            $messages = collect(); // Initialize messages collection

            if ($isAllThread) {
                // Create a virtual "All" thread
                $thread = (object) [
                    'id' => 'all',
                    'title' => 'All Messages',
                    'conversation_id' => $conversation->id,
                    'status' => 'open'
                ];

                // Get all messages from all threads in chronological order
                $messages = $conversation->messages()
                    ->with(['thread'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                if ($threadId) {
                    $thread = $conversation->threads()->find($threadId);
                }

                if (!$thread) {
                    // Get "General" thread first, then fall back to oldest thread
                    $thread = $conversation->threads()->where('title', 'General')->first();
                    
                    if (!$thread) {
                        $thread = $conversation->threads()->oldest()->first();
                    }

                    // If still no thread, create a "General" one
                    if (!$thread) {
                        $thread = $conversation->threads()->create([
                            'title' => 'General',
                            'status' => 'open',
                            'last_message_at' => now()
                        ]);
                    }
                }

                // Load messages for the specific thread
                $messages = $thread->messages()
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            // Mark messages from other senders as read (only for specific threads, not "All")
            if (!$isAllThread) {
                $messages->where('sender_type', '!=', Business::class)
                    ->where('is_read', false)
                    ->each(function ($message) {
                        $message->update(['is_read' => true]);
                    });
            }

            if ($request->ajax() || $request->query('ajax') == '1') {
                Log::info('Returning AJAX response for conversation', [
                    'conversation_id' => $conversation->id,
                    'thread_id' => $isAllThread ? 'all' : $thread->id,
                    'message_count' => $messages->count(),
                    'is_all_thread' => $isAllThread
                ]);

                return response()->view('modules.business.communications.messages-content', [
                    'business' => $business,
                    'conversation' => $conversation,
                    'messages' => $messages,
                    'thread' => $thread,
                    'isAllThread' => $isAllThread
                ])->header('X-AJAX-Response', 'true');
            }

            return view('modules.business.communications.messages', [
                'business' => $business,
                'conversation' => $conversation,
                'messages' => $messages,
                'thread' => $thread,
                'isAllThread' => $isAllThread
            ]);
        } catch (Exception $e) {
            Log::error('Error loading conversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'conversation_id' => $conversation->id ?? 'null',
                'business_id' => $business->id
            ]);

            if ($request->ajax() || $request->query('ajax') == '1') {
                return response()->view('modules.business.communications.error', [
                    'message' => 'Error loading conversation: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('business.communications.index', $business)
                ->with('error', 'Error loading conversation: ' . $e->getMessage());
        }
    }

    public function sendMessage(Business $business, Conversation $conversation, Request $request)
    {
        $request->validate([
            'message' => 'nullable|string', // Changed from required to nullable
            'thread_id' => 'nullable|exists:threads,id',
            'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Validate that at least message or attachment is present
        if (empty($request->message) && !$request->hasFile('attachments')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a message or attachment'
            ], 422);
        }

        // Get or create thread - always default to "General" thread
        $threadId = $request->input('thread_id');
        if ($threadId) {
            $thread = $conversation->threads()->findOrFail($threadId);
        } else {
            // Look for the "General" thread first
            $thread = $conversation->threads()->where('title', 'General')->first();
            
            // If no "General" thread exists, create it
            if (!$thread) {
                $thread = $conversation->threads()->create([
                    'title' => 'General',
                    'status' => 'open',
                    'last_message_at' => now()
                ]);
            }
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('chat-attachments', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ];
                } catch (\Exception $e) {
                    Log::error('Error uploading file: ' . $e->getMessage(), [
                        'exception' => $e,
                        'file' => $file->getClientOriginalName()
                    ]);
                }
            }
        }

        $message = new Message([
            'conversation_id' => $conversation->id,
            'thread_id' => $thread->id,
            'content' => $request->message ?? '', // Use empty string if message is null
            'sender_id' => $business->id,
            'sender_type' => Business::class,
            'attachments' => $attachments,
            'is_read' => true // Business messages are automatically read by the business
        ]);

        $thread->messages()->save($message);

        // Update last_message_at timestamp on both thread and conversation
        $thread->update(['last_message_at' => now()]);
        $conversation->update(['last_message_at' => now()]);

        // Load relationships needed for the broadcast
        $message->load(['sender', 'conversation', 'thread']);

        // Broadcast the message to the thread's channel
        broadcast(new MessageSent($message))->toOthers();

        // If this is an AJAX request, return the message data
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->toArray(), // Return the actual message data
                'message_id' => $message->id,
                'thread_id' => $thread->id
            ]);
        }

        return back()->with('success', 'Message sent successfully');
    }

    /**
     * Create a new thread in a conversation
     */
    public function createThread(Business $business, Conversation $conversation, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'message' => 'required|string'
        ]);

        // Create the new thread
        $thread = $conversation->threads()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'open',
            'last_message_at' => now()
        ]);

        // Add the first message to the thread
        $message = new Message([
            'conversation_id' => $conversation->id,
            'thread_id' => $thread->id,
            'content' => $request->message,
            'sender_id' => $business->id,
            'sender_type' => Business::class,
            'is_read' => true
        ]);

        $thread->messages()->save($message);

        // Update conversation timestamp
        $conversation->update(['last_message_at' => now()]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thread created successfully',
                'thread_id' => $thread->id
            ]);
        }

        return redirect()->route('business.communications.messages', [
            'business' => $business,
            'conversation' => $conversation,
            'thread_id' => $thread->id
        ])->with('success', 'Thread created successfully');
    }

    /**
     * Delete a thread in a conversation
     */
    public function deleteThread(Business $business, Conversation $conversation, $thread, Request $request)
    {
        try {
            // Get the thread
            $threadModel = $conversation->threads()->findOrFail($thread);
            
            // Prevent deletion of the "General" thread
            if (strtolower($threadModel->title) === 'general') {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The General thread cannot be deleted.'
                    ], 422);
                }
                
                return back()->with('error', 'The General thread cannot be deleted.');
            }

            // Check if this is the only thread in the conversation
            $isOnlyThread = $conversation->threads()->count() === 1;

            // Get default thread or another thread to switch to
            $defaultThread = null;
            if (!$isOnlyThread) {
                $defaultThread = $conversation->threads()
                    ->where('id', '!=', $threadModel->id)
                    ->orderBy('last_message_at', 'desc')
                    ->first();
            }

            // Delete thread messages
            $threadModel->messages()->delete();

            // Delete thread
            $threadModel->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thread deleted successfully',
                    'default_thread_id' => $defaultThread ? $defaultThread->id : null,
                    'is_only_thread' => $isOnlyThread
                ]);
            }

            if ($isOnlyThread) {
                return redirect()->route('business.communications.index', [
                    'business' => $business
                ])->with('success', 'Thread deleted successfully');
            }

            return redirect()->route('business.communications.messages', [
                'business' => $business,
                'conversation' => $conversation,
                'thread_id' => $defaultThread->id
            ])->with('success', 'Thread deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting thread', [
                'error' => $e->getMessage(),
                'thread_id' => $thread,
                'business_id' => $business->id,
                'conversation_id' => $conversation->id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting thread: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting thread: ' . $e->getMessage());
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Business $business, $notification)
    {
        return $this->businessNotificationController->markNotificationAsRead($business, $notification);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Business $business)
    {
        return $this->businessNotificationController->markAllNotificationsAsRead($business);
    }


    public function stats(Business $business, BusinessNotification $notification)
    {
        $totalUsers = $notification->users()->count();
        $opened = $notification->users()->whereNotNull('read_at')->count();
        $unopened = $totalUsers - $opened;

        // Paginated users
        $users = $notification->users()->paginate(20);

        // Daily stats (in %)
        $dailyStats = DB::table('business_notifications_users')
            ->selectRaw("DATE(read_at) as date, COUNT(*) as count")
            ->where('notification_id', $notification->id)
            ->whereNotNull('read_at')
            ->groupByRaw("DATE(read_at)")
            ->orderBy('date')
            ->get()
            ->map(function ($item) use ($totalUsers) {
                $item->total = $totalUsers > 0 ? round(($item->count / $totalUsers) * 100, 2) : 0;
                return $item;
            });

        // Weekly stats (in %)
        $weeklyStats = DB::table('business_notifications_users')
            ->selectRaw("YEAR(read_at) as year, WEEK(read_at, 1) as week, COUNT(*) as count")
            ->where('notification_id', $notification->id)
            ->whereNotNull('read_at')
            ->groupByRaw("YEAR(read_at), WEEK(read_at, 1)")
            ->orderByRaw("YEAR(read_at), WEEK(read_at, 1)")
            ->get()
            ->map(function ($item) use ($totalUsers) {
                $start = \Carbon\Carbon::now()->setISODate($item->year, $item->week)->startOfWeek();
                $item->label = $start->format('Y-m-d');
                $item->total = $totalUsers > 0 ? round(($item->count / $totalUsers) * 100, 2) : 0;
                return $item;
            });

        return view('modules.business.communications.notification_stats', compact(
            'notification',
            'totalUsers',
            'opened',
            'unopened',
            'business',
            'users',
            'dailyStats',
            'weeklyStats'
        ));
    }
}
