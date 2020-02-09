<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\FCMController;
use App\Http\Resources\Mobile\Group as GroupResource;
use App\Mobile\Direction;
use App\Mobile\Group;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер управления сущностью "Group"
 * Group служит для объединения пользователей
 */
class GroupController extends Controller
{

    public function getGroup($group_id = 0)
    {
        $group = Group::with(['users', 'schedules'])->where('id', $group_id)->first();

        if ($group) {
            return new GroupResource($group);
        } else {
            return response()->json(['error' => 'group wasn\'t found'], 404);
        }
    }

    public function getAllGroups()
    {
        $groups = Group::all();

        return GroupResource::collection($groups);
    }

    public function addGroup()
    {
        try {

            $group = Group::create([
                'direction_id' => request()->direction_id,
                'group_name' => request()->group_name,
            ]);
        } catch (QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return response()->json(['success' => $group->id], 200);
    }

    public function updateGroup($group_id)
    {
        $group = Group::find($group_id);

        if ($group) {
            $updated = request()->all();
            try {

                $group->update($updated);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['succes' => 'group was updated'], 200);
        }
        return response()->json(['error' => 'group wasn\'t found'], 404);
    }

    public function deleteGroup($group_id)
    {
        $group = Group::find($group_id);

        if ($group) {
            try {
                Group::destroy($group_id);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
            return response()->json(['success' => 'group was deleted'], 200);
        }
        return response()->json(['error' => 'group wasn\'t found'], 404);
    }

    /**
     * Метод добавления пользователя в группу и подписки на топики
     */
    public function addUserInGroup($group_id)
    {
        /**
         * Получить объект группы
         */
        $group = Group::with('direction.event')->where('id', $group_id)->first() ?? new Group;

        if ($group) {
            /**
             * Создать массив users_id пользователей из строки POST-запроса
             */
            $users_id = array_map('intval', explode(',', request()->users));
            try {
                $group->users()->syncWithoutDetaching($users_id);

                /**
                 * Получить все токены устройств всех пользователей по user_id
                 */
                $tokens = $this->getDeviceTokens($users_id);

                /**
                 * Подписать полученнеы токены к топикам. Всего существуют три группы топиков: группы, направления и события.
                 * Токены нужно подписать к каждой из этих групп.
                 */
                (new FCMController)
                    ->subscribeToTopic("group_" . $group_id, $tokens)
                    ->subscribeToTopic("direction_" . $group->direction->id, $tokens)
                    ->subscribeToTopic("event_" . $group->direction->event->id, $tokens);
            } catch (QueryException $ex) {

                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(["error" => "Group wasn\'t found"], 404);
        }
        return response()->json(["success" => "User was added"], 200);
    }

    /**
     * Метод удаления пользователя из группы.
     * После удаления поисходит отписка от топиков групп, направлений и событий.
     * Но перед отпиской от топиков событий и направлений, необходимо отобрать только те токены,
     * которые принадлежат ОДНОМУ направлению и ОДНОМУ событию.
     * Такая проверка необходима в том случае, если пользователь принадлежит более одного раза одному направлению или событию через группу.
     */
    public function deleteUserFromGroup($group_id)
    {
        $users_id = array_map('intval', explode(',', request()->users));
        $users_directions_tokens = [];
        $users_events_tokens = [];
        $group = Group::with('direction.event')->where('id', $group_id)->first() ?? new Group;

        $tokens = $this->getDeviceTokens($users_id);

        /**
         * Найти пользователей, которые участвуют только в одном направлении у указанной группы
         */

        $users_directions = DB::select('
        SELECT distinct device_token from(
            SELECT
            device_token,
            direction_id,
            Count(groups.direction_id) AS count_dir
            FROM
            tokens
            INNER JOIN users_groups ON tokens.user_id = users_groups.user_id
            INNER JOIN groups ON users_groups.group_id = groups.id
            INNER JOIN directions ON groups.direction_id = directions.id
            WHERE
            tokens.user_id in (?)
            GROUP BY 1,2
            having count_dir=1
            ORDER BY 1,3,2) as A', [...$users_id, $group->direction->id]);

        $users_directions = collect($users_directions)->map(function ($x) {
            return (array) $x;
        })->toArray();

        foreach ($users_directions as $key => $users_direction) {
            $users_directions_tokens[] = $users_direction['device_token'];
        }

        /**
         * Найти пользователей, которые участвуют только в одном событии у указанной группы
         */
        $users_events = DB::select('
        SELECT distinct device_token from(
        SELECT
            device_token,
            event_id,
            Count(event_id) AS count_event
            FROM
            tokens
            INNER JOIN users_groups ON tokens.user_id = users_groups.user_id
            INNER JOIN groups ON users_groups.group_id = groups.id
            INNER JOIN directions ON groups.direction_id = directions.id
            WHERE
            tokens.user_id in (?) and event_id = ?
            GROUP BY 1,2
            having count_event=1
            ORDER BY 1,3,2) as A', [...$users_id, $group->direction->event->id]);

        $users_events = collect($users_events)->map(function ($x) {
            return (array) $x;
        })->toArray();

        foreach ($users_events as $key => $users_event) {
            $users_events_tokens[] = $users_event['device_token'];
        }

        if ($group) {
            $users_id = array_map('intval', explode(',', request()->users));
            (new FCMController)
                ->unsubscribeFromTopic("group_" . $group_id, $tokens)
                ->unsubscribeFromTopic("direction_" . $group->direction->id, $users_directions_tokens)
                ->unsubscribeFromTopic("event_" . $group->direction->event->id, $users_events_tokens);
            try {
                $group->users()->detach($users_id);
            } catch (QueryException $ex) {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'group wasn\'t found'], 404);
        }
        return response()->json(["success" => "Users was deleted"], 200);
    }

    /**
     * Получить все токены устройств у пользователей по их user_id
     */
    public function getDeviceTokens(array $users_id)
    {
        $device_tokens = [];

        $users = User::whereHas('tokens', function ($query) use ($users_id) {
            $query->whereIn('user_id', $users_id);
        })->get();

        foreach ($users as $user) {
            array_push($device_tokens, $user->tokens()->get()->pluck("device_token")->all());
        }

        $device_tokens = call_user_func_array('array_merge', $device_tokens);

        return $device_tokens;
    }

    public function changeGroupDirection($group_id)
    {
        try {

            $group = Group::with(['users.tokens'])->where('id', $group_id)->first();

            $device_tokens = [];

            foreach ($group->users as $key => $user) {

                foreach ($user->tokens as $token) {
                    $device_tokens[] = $token->device_token;
                }
            }

            (new FCMController)
                ->subscribeToTopic("direction_" . request()->direction_id, $device_tokens)
                ->unsubscribeFromTopic("direction_" . $group->direction->id, $device_tokens);

            $group->update([
                "direction_id" => request()->direction_id,
            ]);
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }
        return response()->json(['success' => 'direction was changed'], 200);
    }
}
