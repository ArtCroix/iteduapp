<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;


class GoogleAuthController extends Controller
{
    const CREDENTIALS_JSON = "/var/www/iteduapp/config/google-services.json";

    const MOBILE_PROJETC_ID = "project-855943836626";

    public static $client;


    public function __construct()
    {
        self::$client = new \Google_Client();

        self::$client->setAuthConfig(self::CREDENTIALS_JSON);
    }

    public function addFCMScope()
    {
        self::$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public static function getAuthClient()
    {
        self::$client = new \Google_Client();

        self::$client->setAuthConfig(self::CREDENTIALS_JSON);
        self::$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        return self::$client->authorize();
    }
}
