<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Redis;

use Closure;
use JWTAuth;
use Exception;
use App\User;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		try {
			$user = JWTAuth::parseToken()->authenticate();

			$current_jwt_token = JWTAuth::getToken()->get();

			$jwt_token = Redis::get("user:{$user->id}:jwt_token");

			/* 	if ($current_jwt_token != $user->jwt_token) { //Проверка, соответствует ли токен в приложении токену в БД
				return response()->json(["error" => "token match error"]);
			} */

			if ($current_jwt_token != $jwt_token) { //Проверка, соответствует ли токен в приложении токену в Redis
				return response()->json(["error" => "token match error"]);
			}
		} catch (Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
				$newToken = JWTAuth::refresh(JWTAuth::getToken());
				$request->headers->set('Authorization', 'Bearer ' . $newToken);
				return response()->json(['status' => 'Token is Invalid']);
			} else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

				$payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());

				$user = User::find($payload["user_data"]->id);

				$token = JWTAuth::customClaims(['user_data' => new \App\Http\Resources\Mobile\User($user)])->fromUser($user);

				$user->update(['jwt_token' => $token]);

				Redis::set("user:{$user->id}:jwt_token", $user->jwt_token);

				return response()->json(['new_token' => $token]);

				//$refreshed = JWTAuth::refresh(JWTAuth::getToken());

			} else {
				return response()->json(['status' => 'Authorization Token not found']);
			}
		}
		return $next($request);
	}
}
