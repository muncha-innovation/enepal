<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Business;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function userNotifications(Request $request) {

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $notifications = $request->user()->notifications()->latest()->offset($offset)->limit($limit)->get();
        return NotificationResource::collection($notifications);
        
    }

    public function businessNotifications(Request $request) {
            
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;
            $businessId = $request->get('businessId');
            $business = Business::find($businessId);
            $notifications = $$business->notifications()->latest()->offset($offset)->limit($limit)->get();
            return NotificationResource::collection($notifications);
    }
}
