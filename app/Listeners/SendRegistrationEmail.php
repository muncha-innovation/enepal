<?php

namespace App\Listeners;

use App\Events\MemberAddedToBusiness;
use App\Notifications\MemberAddedToBusinessNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRegistrationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\MemberAddedToBusiness  $event
     * @return void
     */
    public function handle(MemberAddedToBusiness $event)
    {
        $event->user->notify(new MemberAddedToBusinessNotification($event->user, $event->business, $event->password, $event->role));
    }
}
