<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;

use App\User;

use App\Http\Resources\Mobile\User as UserResource;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use JWTAuth;

class UserDataController extends Controller
{

	public function __construct()
	{
		$this->middleware('jwt.verify');
	}

	public static function isAllowed(User $user)
	{
		$payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());


		if ($payload['user_data']->approle == 'admin' || $payload['user_data']->id == $user->id) {

			return true;
		}

		exit(json_encode(["error" => "not allowed"]));
	}

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

			return ["error" => "user wasn\'t found"];
		}
	}

	public function getUserAllApplication($user_id = null)
	{
		$user = User::with('apps')->where('id', $user_id)->first();

		if ($user) {
			return new UserResource($user);
		} else {
			return ["error" => "user wasn\'t found"];
		}
	}

	public function getUser($user_id = null)
	{
		$user = User::find($user_id)->load("groups");

		if ($user) {
			return new UserResource($user);
		} else {
			return ["error" => "user wasn\'t found"];
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
			self::isAllowed($user);
			$updated = request()->except(['jwt_token', 'username', 'firstname', 'lastname', 'thirdname', 'mdlrole', 'approle', 'email', 'remember_token']);
			try {
				$user->update($updated);
			} catch (QueryException $ex) {
				return ['error' => $ex->getMessage()];
			}
			return  ['succes' => 'user was updated'];
		}

		return ['error' => 'user wasn\'t found'];
	}
}
