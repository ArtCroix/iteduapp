<?php

namespace App\Http\Controllers\Mobile;

use App\Events\UserAuthenticated;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\CreateUserController;
use App\Http\Resources\Mobile\User as UserResource;
use App\Mobile\Token;
use App\Moodle\MdlUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Контроллер аутентификации пользователей через приложение.
 * Для аутентификации необходимо иметь аккаунт созданный в CMS Moodle по адресу: https: //it-edu.com/el
 */
class AuthenticationController extends Controller
{

    /**
     * Метод аутентификации пользователя. В качестве данных входа слжуат логин и пароль в Moodle.
     */
    public function authenticateUser(Request $request)
    {
        $credentials = request(['username', 'password']);

        if (!request()->device_token) {
            return response()->json(["error" => "Device token is null"], 500);
        }

        if (!auth('mdl_remote_user_api')->attempt($credentials)) {

            return response()->json([
                "error" => "The username or password is incorrect",
            ], 401);
        }

        /**
         * Получить объект пользователя из БД Мудл.
         */
        $mdl_user = MdlUser::where('username', $request->username)->first();

        /**
         * В случае успешной аутентификации получить объект пользователя из базы Moodle
         * При первой попытке успешного входа записать пользоватлея в БД приложения
         */
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

        /**
         * Сохранить user_id и токен устройства в таблице tokens
         */
        $this->setDeviceToken($user, $request->device_token);

        /**
         * Вызов события при успешной аутентификации пользователя
         */
        event(new UserAuthenticated());

        try {
            return response()->json([
                'token' => $this->setJWTToken($user),
            ], 200);

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }

    }

    /**
     * Метод разлогина пользователя
     */
    public function unAuthenticateUser($user_id)
    {
        $user = User::find($user_id);

        if ($user) {

            if (!request()->device_token) {
                return response()->json(["error" => "Device token is null"], 500);
            }

            $this->unsetDeviceToken();

            $this->unsetJWTToken($user);

            return response()->json(["success" => "The user was unauthenticated"], 200);
        } else {
            return response()->json(["error" => "The user wasn't unauthenticated"], 401);
        }
    }

    /**
     * Метод создания jwt-токена.
     * jwt-токен должен отправляться в заголовке с ключом --header Authorization: Bearer при каждом запросе
     */
    public function setJWTToken($user)
    {
        $token = JWTAuth::customClaims(['user_data' => new UserResource($user->load('groups'))])->fromUser($user);

        /**
         * Запись jwt-токена в таблицу. Необходимо для того, чтобы пользователь не мог использовать чужой токент со своим
         * аккаунтом
         */
        $user->update(['jwt_token' => $token]);

        /**
         * Запись jwt-токена в БД Redis. Для ускорения работы токен из заголовка будет сверяться с токеном из Redis
         */
        Redis::set("user:{$user->id}:jwt_token", $user->jwt_token);

        return $token;
    }

    public function unsetJWTToken($user)
    {
        /**
         * Удалить jwt_токен у пользоватлея и из Redis.
         */
        $user->update(["jwt_token" => null]);

        Redis::del("user:{$user->id}:jwt_token", $user->jwt_token);
    }

    /**
     * Метод записи токена устройства в БД
     */
    public function setDeviceToken(User $user, string $device_token)
    {
        /**
         * Получить объект токена устройства (+ создать, если нет)
         */
        $notification_token = Token::firstOrCreate(["device_token" => $device_token], ["device_token" => $device_token]);

        /**
         * Присвоить токену устрйоства соответствующий user_id
         */
        $notification_token->update(["user_id" => $user->id]);
    }

    /**
     * Метод удаления токена устройства из БД
     */
    public function unsetDeviceToken()
    {
        /**
         * Присвоить null полю user_id у соответствующего токена устройства
         */
        Token::where("device_token", request()->device_token)->update(["user_id" => null]);
    }
}
