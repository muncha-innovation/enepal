<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessNotification;
use App\Models\User;
use App\Services\UserSegmentationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Jobs\ProcessNotificationBatch;

class BusinessNotificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Business $business
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\UserSegmentationService $segmentationService
     * @return \Illuminate\Http\Response
     */
    public function store(Business $business, Request $request, UserSegmentationService $segmentationService = null)
    {
        try {
            if ($segmentationService === null) {
                $segmentationService = new UserSegmentationService();
            }
            
            $validator = Validator::make($request->all(), [
                'recipient_type' => 'nullable|in:users,segment', 
                'segment_id' => 'required_without:users',
                'users' => 'required_without:segment_id|array',
                'users.*' => ['required', function ($attribute, $value, $fail) {
                    // Allow both 'all_users' and 'all' for backward compatibility
                    if (!in_array($value, ['all_users', 'all']) && !User::where('id', $value)->exists()) {
                        $fail("The selected user ({$value}) is invalid.");
                    }
                }],
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'image' => 'nullable|image|max:2048', 
            ], [
                'segment_id.required_without' => 'Please select a segment or users to send notification to',
                'users.required_without' => 'Please select users or a segment to send notification to',
                'title.required' => 'The notification title is required',
                'message.required' => 'The notification message is required',
                'image.image' => 'The uploaded file must be an image (jpg, jpeg, png, bmp, gif, svg, or webp)',
                'image.max' => 'The image size must not exceed 2MB'
            ]);

            if ($validator->fails()) {
                $request->flash();
                
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('notification_modal_open', true);
            }

            $userIds = [];
            $recipientType = $request->input('recipient_type', 'segment'); // Default or determine from active tab if needed

            // Determine active recipient type based on submitted data
            if ($request->filled('segment_id')) {
                $recipientType = 'segment';
            } elseif ($request->filled('users')) {
                $recipientType = 'users';
            }

            if ($recipientType === 'segment' && $request->filled('segment_id')) {
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
            } elseif ($recipientType === 'users' && $request->filled('users')) {
                if (in_array('all_users', $request->users) || in_array('all', $request->users)) {
                    // Get all active users directly as User IDs to avoid passing 'all' to the database
                    $userIds = User::where('is_active', true)->pluck('id')->toArray();
                } else {
                    $userIds = $request->users;
                }
            }

            if (empty($userIds)) {
                 return redirect()->back()
                    ->withErrors(['error' => 'No recipients found for the selected criteria.'])
                    ->withInput()
                    ->with('notification_modal_open', true);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('notification-images', 'public');
            }

            $notification = new BusinessNotification([
                'title' => $request->title,
                'content' => $request->message,
                'image' => $imagePath,
                'business_id' => $business->id,
                'is_active' => true, 
                'is_private' => false, // Assuming notifications sent this way are not private by default
                'is_verified' => false, // Needs verification step?
                'verified_by' => null,
                'is_sent' => true,
                'sent_at' => now()
            ]);
            
            $notification->save();
            
            $userData = [];
            $now = now();
            foreach ($userIds as $userId) {
                $userData[$userId] = [
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            $notification->users()->syncWithoutDetaching($userData);

            return redirect()
                ->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
                ->with('success', 'Notification sent successfully to ' . count($userIds) . ' user(s)');
                
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage(), [
                'exception' => $e,
                'business_id' => $business->id,
                'request_data' => $request->except(['image'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while sending the notification. Please check logs or contact support.'])
                ->with('notification_modal_open', true);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business $business
     * @param  \App\Models\BusinessNotification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business, BusinessNotification $notification)
    {
        if ($notification->business_id !== $business->id) {
            return redirect()->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
                ->with('error', 'This notification does not belong to this business');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notification-images', 'public');
            $notification->image = $imagePath;
        } elseif ($request->has('remove_image')) { // Add logic to remove image if needed
        }
        
        $notification->title = $request->title;
        $notification->content = $request->message;
        $notification->save();
        
        return redirect()->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
            ->with('success', 'Notification updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business $business
     * @param  \App\Models\BusinessNotification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business, BusinessNotification $notification)
    {
        // Ensure the notification belongs to this business
        if ($notification->business_id !== $business->id) {
            return redirect()->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
                ->with('error', 'This notification does not belong to this business');
        }
        
        // Delete the notification
        $notification->delete();
        
        return redirect()->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
            ->with('success', 'Notification deleted successfully');
    }

    /**
     * Mark a notification as read for the current user
     *
     * @param  \App\Models\Business $business
     * @param  int  $notification  The notification ID
     * @return \Illuminate\Http\Response
     */
    public function markNotificationAsRead(Business $business, $notificationId) 
    {
        $notification = $business->notifications()->findOrFail($notificationId);
        
        if (auth()->check()) {
            $notification->markAsReadBy(auth()->user());
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the current user
     *
     * @param  \App\Models\Business $business
     * @return \Illuminate\Http\Response
     */
    public function markAllNotificationsAsRead(Business $business)
    {
        if (auth()->check()) {
            $business->notifications()
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id())
                        ->whereNull('read_at');
                })
                ->get()
                ->each(function($notification) {
                    $notification->markAsReadBy(auth()->user());
                });
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Send notification to users.
     * This method is used by CommunicationsController to maintain interface compatibility
     *
     * @param  \App\Models\Business $business
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\UserSegmentationService $segmentationService
     * @return \Illuminate\Http\Response
     */
    public function sendNotification(Business $business, Request $request, UserSegmentationService $segmentationService)
    {
        $request->validate([
            'recipient_type' => 'required|in:users,segment',
            'users.*' => ['required_if:recipient_type,users', function ($attribute, $value, $fail) {
                // Allow both 'all_users' and 'all' for backward compatibility
                if (!in_array($value, ['all_users', 'all']) && !User::where('id', $value)->exists()) {
                    $fail("The selected user ({$value}) is invalid.");
                }
            }],
            'segment_id' => 'required_if:recipient_type,segment',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        try {
            // Handle image upload if present
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('notification-images', 'public');
            }

            // Get target user IDs based on recipient type
            $userIds = [];
            if ($request->recipient_type === 'users') {
                $users = $request->input('users', []);
                if (in_array('all', (array)$request->input('users'))) {
                    $userIds = User::pluck('id')->toArray();
              
                } else {
                    $userIds = $request->input('users');
                }
            } else {
                $segmentId = $request->segment_id;
                if (str_starts_with($segmentId, 'custom_')) {
                    // Custom segment
                    $segment = $business->userSegments()->findOrFail(substr($segmentId, 7));
                    $userIds = $segmentationService->getUsersForSegment($segment)->pluck('id')->toArray();
                } else {
                    // Predefined segment
                    $userIds = $segmentationService->getUsersForPredefinedSegment($segmentId)->pluck('id')->toArray();
                }
            }

            // First, create and store the notification in the database
            $notification = new BusinessNotification([
                'title' => $request->title,
                'content' => $request->message,
                'image' => $imagePath,
                'business_id' => $business->id,
                'is_active' => true,
                'is_private' => false,
                'is_verified' => true,
                'verified_by' => auth()->id(),
                'is_sent' => true,
                'sent_at' => now()
            ]);
            
            $notification->save();
            
            // Prepare user data for sync
            $userData = [];
            $now = now();
            foreach ($userIds as $userId) {
                $userData[$userId] = [
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            
            // Associate users with the notification
            $notification->users()->syncWithoutDetaching($userData);

            // Prepare notification data for push notifications
            $notificationData = [
                'title' => $request->title,
                'message' => $request->message,
                'image' => $imagePath,
                'type' => 'business',
                'notification_id' => $notification->id
            ];

            return redirect()
                ->route('business.communications.index', ['business' => $business, 'type' => 'notification'])
                ->with('success', 'Notification sent successfully to ' . count($userIds) . ' recipient(s) and queued for push delivery');

        } catch (\Exception $e) {
            \Log::error('Error sending notification: ' . $e->getMessage(), [
                'exception' => $e,
                'business_id' => $business->id
            ]);

            return back()->with('error', 'Error sending notification: ' . $e->getMessage())
                ->withInput();
        }
    }
}
