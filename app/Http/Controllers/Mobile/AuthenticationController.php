<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;

use App\Moodle\MdlUser;

use App\User;

use App\Http\Controllers\Mobile\FCMController;

use App\Mobile\Token;

use Illuminate\Support\Facades\Hash;

use App\Http\Resources\Mobile\User as UserResource;

use App\Http\Controllers\Controller;

use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;


class AuthenticationController extends Controller
{

	public static function isAllowed(User $user)
	{
		$payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());


		if ($payload['user_data']->approle == 'admin' || $payload['user_data']->mdlrole == 'admin' || $payload['user_data']->id == $user->id) {

			return true;
		}

		exit(json_encode(["error" => "not allowed"]));
	}

	public function authenticateUser(Request $request)
	{
		$credentials = request(['username', 'password']);

		if (!auth('mdl_remote_user_api')->attempt($credentials)) {
			return ["error" => "The username or password is incorrect"];
		}

		$mdl_user = MdlUser::all()->where('username', $request->username)->first();

		$user = User::firstOrCreate(
			['id' => $mdl_user->id],
			[
				'id' => $mdl_user->id,
				'firstname' => $mdl_user->firstname,
				'lastname' => $mdl_user->lastname,
				'thirdname' => $mdl_user->thirdname,
				'username' => $request->username,
				'mdlrole' => $mdl_user->role,
				'approle' => $mdl_user->role,
				'password' => Hash::make($request->password),
				'email' => $mdl_user->email,
			]
		);

		$notification_token = Token::firstOrCreate(["device_token" => request()->device_token], ["device_token" => request()->device_token]);

		$notification_token->update(["user_id" => $user->id]);

		(new FCMController)->subscribeToTopic("all", [request()->device_token]);

		return $this->set_token($user);
	}

	public function unAuthenticateUser($user_id)
	{

		$user = User::find($user_id);

		if ($user) {

			self::isAllowed($user);

			if (!request()->device_token) {
				return ["error" => "Device token is null"];
			}

			Token::where("device_token", request()->device_token)->update(["user_id" => null]);

			$user->update(["jwt_token" => ""]);

			return ["success" => "The user was unauthenticated"];
		} else {
			return ["error" => "The user wasn't unauthenticated"];
		}
	}

	public function set_token($user)
	{
		request()->route()->setParameter('user_id', $user->id);

		// Время жизни токена устанавливается в Service Provider

		$token = JWTAuth::customClaims(['user_data' => new UserResource($user->load('groups'))])->fromUser($user);

		$user->update(['jwt_token' => $token]);

		Redis::set("user:{$user->id}:jwt_token", $user->jwt_token);

		return response()->json([
			'token' => $token,
		]);
	}
}
