<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Business;
use App\Models\BusinessNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function userNotifications(Request $request) {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        
        $user = $request->user();
        $notifications = $user->businessNotifications()
            ->with(['business','business.type'])
            ->orderBy('business_notifications_users.created_at', 'desc')
            ->offset($offset)
            ->limit($limit
            )
            ->get();
            
        return NotificationResource::collection($notifications);
    }

    public function businessNotifications(Request $request) {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $businessId = $request->get('businessId');
        
        $business = Business::find($businessId);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }
        
        $notifications = $business->notifications()
            ->where('is_active', true)
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
            
        return NotificationResource::collection($notifications);
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $notificationId) {
        $user = $request->user();
        $notification = BusinessNotification::findOrFail($notificationId);
        
        $notification->markAsReadBy($user);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read for the current user
     */
    public function markAllAsRead(Request $request) {
        $user = $request->user();
        
        $user->businessNotifications()
            ->wherePivotNull('read_at')
            ->get()
            ->each(function($notification) use ($user) {
                $notification->markAsReadBy($user);
            });
            
        return response()->json(['success' => true]);
    }
}
