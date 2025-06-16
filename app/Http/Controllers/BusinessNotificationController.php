<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessNotification;
use App\Models\User;
use App\Models\UserSegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BusinessNotificationController extends Controller
{
    public function sendNotification(Business $business, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:users,segment',
            'segment_id' => 'required_if:recipient_type,segment',
            'users' => 'required_if:recipient_type,users|array',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Upload image if present
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('notifications', 'public');
        }

        // Create notification
        $notification = BusinessNotification::create([
            'business_id' => $business->id,
            'title' => $request->title,
            'content' => $request->message,
            'image' => $imagePath,
            'is_active' => true,
            'is_sent' => true,
            'sent_at' => now()
        ]);

        // Get users based on selection
        $userIds = [];
        
        if ($request->recipient_type === 'segment') {
            // Process segment_id which could be 'custom_X' or a predefined segment name
            $segmentId = $request->segment_id;
            
            // Handle custom segments (custom_X format)
            if (Str::startsWith($segmentId, 'custom_')) {
                $actualSegmentId = (int)Str::after($segmentId, 'custom_');
                
                $segment = UserSegment::where('id', $actualSegmentId)
                    ->where('business_id', $business->id)
                    ->first();
                    
                if ($segment) {
                    $userIds = $segment->users->pluck('id')->toArray();
                }
            }
            // Add handling for other predefined segment types if needed
            // else if ($segmentId === 'other_predefined_type') { ... }
        } 
        else { // recipient_type === 'users'
            if (in_array('all', $request->users)) {
                // "All users" option selected
                $userIds = $business->users()->pluck('users.id')->toArray();
            } else {
                $userIds = $request->users;
            }
        }

        // Attach users to notification
        if (!empty($userIds)) {
            $notification->users()->attach($userIds);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Notification sent successfully');
    }

    public function markNotificationAsRead(Business $business, $notificationId)
    {
        
        $notification = $business->notifications()->findOrFail($notificationId);
        
        $notification->users()->updateExistingPivot(auth()->id(), ['read_at' => now()]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllNotificationsAsRead(Business $business)
    {
        $notifications = $business->notifications()
            ->whereHas('users', function($query) {
                $query->where('user_id', auth()->id())
                    ->whereNull('read_at');
            })
            ->get();

        foreach ($notifications as $notification) {
            $notification->users()->updateExistingPivot(auth()->id(), ['read_at' => now()]);
        }

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

}
