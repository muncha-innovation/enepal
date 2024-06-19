<?php

namespace App\Listeners;

use App\Events\NoticeCreated;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationToSubscribedUsers
{
    /**
     * Handle the event.
     *
     * @param  NoticeCreated  $event
     * @return void
     */
    public function handle(NoticeCreated $event)
    {
        Log::info('SendNotificationToSubscribedUsers listener fired');
        $business = $event->business;
        $notification = $event->notification;

        $subscribedUsers = User::all();
        foreach ($subscribedUsers as $user) {
            if($user->fcm_token && $user->fcm_token != '') {
                $user->notify(new UserNotification($notification));
            }
        }
    }
}