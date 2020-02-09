<?php

namespace App\Listeners;

use App\Events\UserAuthenticated;
use App\Http\Controllers\Mobile\FCMController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveFromAllTopicInFirebase
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
     * @param  UserAuthenticated  $event
     * @return void
     */
    public function handle(UserAuthenticated $event)
    {
        (new FCMController)->unsubscribeFromTopic("all", [request()->device_token]);
    }
}
