<?php

namespace App\Http\Controllers\Mobile;

use Kreait\Firebase\Messaging\CloudMessage;

use App\Services\FirebaseService;

use App\Http\Controllers\Controller;

class FCMController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function sendNotification()
    {
        $messaging = (new FirebaseService)->firebase->getMessaging();

        try {
            $message = CloudMessage::withTarget(request()->target, request()->target_name)
                ->withNotification(['title' => request()->title, 'body' => request()->body]);

            $messaging->send($message);
        } catch (\Throwable $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function subscribeToTopic($topic, array $device_tokens)
    {
        (new FirebaseService)
            ->firebase
            ->getMessaging()
            ->subscribeToTopic($topic, $device_tokens);
    }

    public function unsubscribeFromTopic($topic, array $device_tokens)
    {
        (new FirebaseService)
            ->firebase
            ->getMessaging()
            ->unsubscribeFromTopic($topic, $device_tokens);
    }
}
