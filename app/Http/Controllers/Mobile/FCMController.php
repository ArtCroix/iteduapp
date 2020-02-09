<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Kreait\Firebase\Messaging\CloudMessage;

class FCMController extends Controller
{
    protected $fb_service;

    public function __construct()
    {
        // $this->middleware('jwt.verify');

        $this->fb_service = new FirebaseService;
    }

    public function getAllSubscriptions()
    {
        $this->fb_service->getAllSubscriptions(request()->device_token);
    }

    public function sendNotificationToSpecificTarget()
    {
        $messaging = $this->fb_service->firebase->getMessaging();

        try {
            $message = CloudMessage::withTarget(request()->target, request()->target_name)
                ->withNotification(['title' => request()->title, 'body' => request()->body]);
            $messaging->send($message);
            return response()->json(['success' => 'message was delivered'], 200);

        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function sendNotificationToSpecificDevices()
    {
        $messaging = $this->fb_service->firebase->getMessaging();
        $device_tokens = explode(",", request()->device_tokens);
        try {
            $message = CloudMessage::new ()
                ->withNotification(['title' => request()->title, 'body' => request()->body]);
            $messaging->sendMulticast($message, $device_tokens);
            return response()->json(['success' => 'message was delivered'], 200);

        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function subscribeToTopic(string $topic, array $device_tokens)
    {
        try {
            $this->fb_service->subscribeToTopic($topic, $device_tokens);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return $this;

    }

    public function unsubscribeFromTopic(string $topic, array $device_tokens)
    {
        try {
            $this->fb_service->unsubscribeFromTopic($topic, $device_tokens);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return $this;
    }
}
