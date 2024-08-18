<?php

namespace App\Listeners;

use App\Events\MemberAddedToBusiness;
use App\Notifications\MemberAddedToBusinessNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendRegistrationEmail implements ShouldQueue
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
        try{
        $event->user->notify(new MemberAddedToBusinessNotification($event->user, $event->business, $event->password, $event->role));
        }catch(Exception $e) {
            dd($event);
        }
    }
}
