<?php

namespace App\Listeners;

use App\Events\UserAuthenticated;
use App\Http\Controllers\Mobile\FCMController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddToAllTopicInFirebase
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
        (new FCMController)->subscribeToTopic("all", [request()->device_token]);
    }
}
