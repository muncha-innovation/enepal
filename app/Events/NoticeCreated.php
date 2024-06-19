<?php
namespace App\Events;

use App\Models\Business;
use App\Models\Notice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NoticeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Business $business;
    public Notice $notification;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($business, $notification)
    {
        Log::info('NotificationCreated event fired');
        $this->business = $business;
        $this->notification = $notification;
    }
}
