<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\UserSegmentationService;
use Exception;
use Illuminate\Http\Request;

class CommunicationsController extends Controller
{
    public function getConversations(Business $business, Request $request)
    {
        $type = $request->get('type', 'chat');
        
        // Get user segments and predefined segments for both chat and notifications tabs
        $segments = $business->userSegments()->where('is_active', true)->get();
        
        // Get predefined segments
        $predefinedSegments = [
            [
                'id' => 'recently_active',
                'name' => 'Recently Active Users',
                'conditions' => [['type' => 'last_active', 'operator' => 'less_than', 'value' => 7]]
            ],
            [
                'id' => 'inactive',
                'name' => 'Inactive Users (30+ days)',
                'conditions' => [['type' => 'last_active', 'operator' => 'more_than', 'value' => 30]]
            ],
            [
                'id' => 'engaged',
                'name' => 'Engaged Users',
                'conditions' => [['type' => 'notification_opened', 'value' => 7]]
            ],
            [
                'id' => 'students',
                'name' => 'Students',
                'conditions' => [['type' => 'user_type', 'value' => 'student']]
            ],
            [
                'id' => 'job_seekers',
                'name' => 'Job Seekers',
                'conditions' => [['type' => 'user_type', 'value' => 'job_seeker']]
            ]
        ];
        
        // Get users for both tabs
        $users = User::all(['id', 'first_name', 'email']);
        
        if ($type === 'chat') {
            $conversations = $business->conversations()
                ->with(['messages' => function($query) {
                    $query->latest()->first();
                }, 'user'])
                ->latest()
                ->get();

            $unreadChats = Message::whereHas('conversation', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })->where('is_read', false)
              ->where('is_notification', false)
              ->count();

            return view('modules.business.communications.index', compact('business', 'conversations', 'unreadChats', 'segments', 'predefinedSegments', 'users'));
        }

        // For notifications view
        $notifications = Message::whereHas('conversation', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->where('is_notification', true)
          ->latest()
          ->paginate(15);

        $unreadNotifications = Message::whereHas('conversation', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->where('is_notification', true)
          ->where('is_read', false)
          ->count();

        // Get user segments and all users for sending new notifications
        $users = User::all(['id', 'first_name', 'email']);
        $segments = $business->userSegments()->where('is_active', true)->get();
        
        // Get predefined segments
        $predefinedSegments = [
            [
                'id' => 'recently_active',
                'name' => 'Recently Active Users',
                'conditions' => [['type' => 'last_active', 'operator' => 'less_than', 'value' => 7]]
            ],
            [
                'id' => 'inactive',
                'name' => 'Inactive Users (30+ days)',
                'conditions' => [['type' => 'last_active', 'operator' => 'more_than', 'value' => 30]]
            ],
            [
                'id' => 'engaged',
                'name' => 'Engaged Users',
                'conditions' => [['type' => 'notification_opened', 'value' => 7]]
            ],
            [
                'id' => 'students',
                'name' => 'Students',
                'conditions' => [['type' => 'user_type', 'value' => 'student']]
            ],
            [
                'id' => 'job_seekers',
                'name' => 'Job Seekers',
                'conditions' => [['type' => 'user_type', 'value' => 'job_seeker']]
            ]
        ];

        return view('modules.business.communications.index', 
            compact('business', 'notifications', 'unreadNotifications', 'users', 'segments', 'predefinedSegments'));
    }

    public function createChat(Business $business, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'thread_title' => 'nullable|string|max:255'
        ]);

        $conversation = Conversation::firstOrCreate([
            'business_id' => $business->id,
            'user_id' => $request->user_id
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
            'is_notification' => false
        ]);

        $thread->messages()->save($message);
        
        // Update last_message_at timestamp
        $thread->update(['last_message_at' => now()]);
        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('business.communications.messages', [
            'business' => $business,
            'conversation' => $conversation,
            'thread_id' => $thread->id
        ])->with('success', 'Message sent successfully');
    }

    public function sendNotification(Business $business, Request $request, UserSegmentationService $segmentationService)
    {
        $request->validate([
            'segment_id' => 'required_without:users',
            'users' => 'required_without:segment_id|array',
            'users.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'thread_title' => 'nullable|string|max:255',
            'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Get users based on segment or direct selection
        if ($request->segment_id) {
            if (str_starts_with($request->segment_id, 'custom_')) {
                $segmentId = str_replace('custom_', '', $request->segment_id);
                $segment = $business->userSegments()->findOrFail($segmentId);
                $userQuery = $segmentationService->getUsersBySegment($segment->conditions);
            } else {
                $predefinedSegments = [
                    'recently_active' => [['type' => 'last_active', 'operator' => 'less_than', 'value' => 7]],
                    'inactive' => [['type' => 'last_active', 'operator' => 'more_than', 'value' => 30]],
                    'engaged' => [['type' => 'notification_opened', 'value' => 7]],
                    'students' => [['type' => 'user_type', 'value' => 'student']],
                    'job_seekers' => [['type' => 'user_type', 'value' => 'job_seeker']]
                ];
                
                $conditions = $predefinedSegments[$request->segment_id] ?? [];
                $userQuery = $segmentationService->getUsersBySegment($conditions);
            }
            
            $userIds = $userQuery->pluck('id')->toArray();
        } else {
            // Check if "all" is selected
            if (in_array('all', $request->users)) {
                $userIds = User::pluck('id')->toArray();
            } else {
                $userIds = $request->users;
            }
        }

        // Process attachments if any
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('notification-attachments', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error uploading notification attachment: ' . $e->getMessage(), [
                        'exception' => $e,
                        'file' => $file->getClientOriginalName()
                    ]);
                }
            }
        }

        // Get thread title (default to "Notifications")
        $threadTitle = $request->thread_title ?: 'Notifications';
        $notificationsSent = 0;

        foreach ($userIds as $userId) {
            $conversation = Conversation::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $userId
            ]);

            // Get or create the thread for notifications
            $thread = $conversation->threads()->firstOrCreate(
                ['title' => $threadTitle],
                ['status' => 'open']
            );

            $message = new Message([
                'conversation_id' => $conversation->id,
                'thread_id' => $thread->id,
                'content' => json_encode([
                    'title' => $request->title,
                    'message' => $request->message
                ]),
                'sender_id' => $business->id,
                'sender_type' => Business::class,
                'attachments' => $attachments,
                'is_notification' => true,
                'is_read' => false
            ]);

            $thread->messages()->save($message);
            
            // Update last_message_at timestamps
            $thread->update(['last_message_at' => now()]);
            $conversation->update(['last_message_at' => now()]);
            
            $notificationsSent++;
        }

        return back()->with('success', 'Notification sent successfully to ' . $notificationsSent . ' user(s)');
    }

    public function getMessages(Business $business, Conversation $conversation, Request $request)
    {
        try {
            \Log::info('Loading conversation', [
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
            
            if ($threadId) {
                $thread = $conversation->threads()->find($threadId);
            }
            
            if (!$thread) {
                // Get or create default thread
                $thread = $conversation->defaultThread();
            }
            
            // Load messages for the specific thread
            $messages = $thread->messages()
                ->orderBy('created_at', 'asc')
                ->get();
                
            // Mark messages from other senders as read
            $messages->where('sender_type', '!=', Business::class)
                    ->where('is_read', false)
                    ->each(function($message) {
                        $message->update(['is_read' => true]);
                    });
                    
            if ($request->ajax() || $request->query('ajax') == '1') {
                \Log::info('Returning AJAX response for conversation', [
                    'conversation_id' => $conversation->id,
                    'thread_id' => $thread->id,
                    'message_count' => $messages->count()
                ]);
                
                return response()->view('modules.business.communications.messages-content', [
                    'business' => $business,
                    'conversation' => $conversation,
                    'messages' => $messages,
                    'thread' => $thread
                ])->header('X-AJAX-Response', 'true');
            }
            
            return view('modules.business.communications.messages', [
                'business' => $business,
                'conversation' => $conversation,
                'messages' => $messages,
                'thread' => $thread
            ]);
            
        } catch(Exception $e) {
            \Log::error('Error loading conversation', [
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
            'message' => 'required|string',
            'thread_id' => 'nullable|exists:threads,id',
            'attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Get or create thread
        $threadId = $request->input('thread_id');
        if ($threadId) {
            $thread = $conversation->threads()->findOrFail($threadId);
        } else {
            $thread = $conversation->defaultThread();
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
                    \Log::error('Error uploading file: ' . $e->getMessage(), [
                        'exception' => $e,
                        'file' => $file->getClientOriginalName()
                    ]);
                }
            }
        }

        $message = new Message([
            'conversation_id' => $conversation->id,
            'thread_id' => $thread->id,
            'content' => $request->message,
            'sender_id' => $business->id,
            'sender_type' => Business::class,
            'attachments' => $attachments,
            'is_notification' => false,
            'is_read' => true // Business messages are automatically read by the business
        ]);

        $thread->messages()->save($message);
        
        // Update last_message_at timestamp on both thread and conversation
        $thread->update(['last_message_at' => now()]);
        $conversation->update(['last_message_at' => now()]);

        // If this is an AJAX request, return the message view
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'message_id' => $message->id,
                'thread_id' => $thread->id
            ]);
        }

        return back()->with('success', 'Message sent successfully');
    }

    public function markNotificationAsRead(Business $business, Message $notification)
    {
        if ($notification->is_notification) {
            $notification->update([
                'is_read' => true,
                'opened_at' => now()
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead(Business $business)
    {
        Message::whereHas('conversation', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->where('is_notification', true)
          ->where('is_read', false)
          ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function manageSegments(Business $business)
    {
        $segments = $business->userSegments()->latest()->get();
        
        // Get some basic user stats for segment creation guidance
        $userStats = [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('last_active_at', '>', now()->subDays(7))->count(),
            'student_users' => \App\Models\User::whereHas('preference', function($q) {
                $q->where('user_type', 'student');
            })->count(),
            'job_seeker_users' => \App\Models\User::whereHas('preference', function($q) {
                $q->where('user_type', 'job_seeker');
            })->count()
        ];
        
        return view('modules.business.communications.segments.index', 
            compact('business', 'segments', 'userStats'));
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
            'is_notification' => false,
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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting thread: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error deleting thread: ' . $e->getMessage());
        }
    }

}
