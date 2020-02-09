<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\User as UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use JWTAuth;

class UserDataController extends Controller
{

    public function gu($user_id)
    {
        Redis::set("test", 1);
        $un = Redis::get("user:{$user_id}:jwt_token");
        echo $un;
    }

    public function getUserSingleApplication($user_id = null, $apply_id = 0)
    {
        $user = User::with(['apps' => function ($query) use ($apply_id) {
            $query->where('mdl3_apply.id', $apply_id);
        }])->where('id', $user_id)->first();

        if ($user) {
            return new UserResource($user);
        } else {

            return response()->json(['error' => 'user wasn\'t found'], 404);
        }
    }

    public function getUserAllApplication($user_id = null)
    {
        $user = User::with('apps')->where('id', $user_id)->first();

        if ($user) {
            return new UserResource($user);
        } else {
            return response()->json(['error' => 'user wasn\'t found'], 404);
        }
    }

    public function getUserBaseInfo($user_id = null)
    {
        $user = User::where('id', $user_id)->first();

        if ($user) {
            return new UserResource($user);
        } else {
            return response()->json(['error' => 'user wasn\'t found'], 404);
        }
    }

    public function getUser($user_id = null)
    {
        $user = User::find($user_id)->load("groups");

        if ($user) {
            return new UserResource($user);
        } else {
            return response()->json(['error' => 'user wasn\'t found'], 404);
        }
    }

    public function getAllUsers()
    {
        return UserResource::collection(User::all()->where('id', "<>", 1));
    }

    public function updateUser($user_id)
    {

        $user = User::find($user_id);

        if ($user) {
            $updated = request()->except(['jwt_token', 'username', 'firstname', 'lastname', 'thirdname', 'mdlrole', 'approle', 'email', 'remember_token']);
            try {
                $user->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 404);
            }
            return response()->json(['succes' => 'user was updated'], 200);
        }

        return response()->json(['error' => 'user wasn\'t found'], 404);
    }
}
