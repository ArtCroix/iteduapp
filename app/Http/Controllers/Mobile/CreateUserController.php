<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\FCMController;
use App\Http\Resources\Mobile\User as UserResource;
use App\Mobile\Token;
use App\Moodle\MdlUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CreateUserController extends Controller
{
    public function __construct()
    {

    }

}
