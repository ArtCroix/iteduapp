<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Resources\Mobile\Group as GroupResource;

use App\Http\Controllers\Mobile\DirectionController;

use App\Mobile\Group;

use Kreait\Firebase\Messaging\CloudMessage;

use App\Mobile\Direction;

use App\Http\Controllers\Mobile\FCMController;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use JWTAuth;

use Illuminate\Database\QueryException;
use App\User;

class GroupController extends Controller
{

  public function __construct()
  {
    $this->middleware('jwt.verify');
  }


  public static function isAllowed(Group $group)
  {
    $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());

    $current_event_admins = $group->direction->event->event_admins ? array_map('intval', explode(',', $group->direction->event->event_admins)) : [];

    $current_direction_admins = $group->direction->direction_admins ? array_map('intval', explode(',', $group->direction->direction_admins)) : [];

    $current_group_admins = $group->group_admins ? array_map('intval', explode(',', $group->group_admins)) : [];

    if (
      $payload['user_data']->approle == 'admin' || in_array($payload['user_data']->id, $current_event_admins)  || in_array($payload['user_data']->id, $current_direction_admins)
      || in_array($payload['user_data']->id, $current_group_admins)
    ) {

      return true;
    }

    exit(json_encode(["error" => "not allowed"]));
  }

  public function getGroup($group_id = 0)
  {
    $group = Group::with(['users', 'schedules'])->where('id', $group_id)->first();

    if ($group) {
      return new GroupResource($group);
    } else {
      return ['error' => 'group wasn\'t found'];
    }
  }

  public function getAllGroups()
  {
    $groups = Group::all();

    return GroupResource::collection($groups);
  }


  public function addGroup()
  {

    DirectionController::isAllowed(Direction::find(request()->direction_id));

    try {

      $group = Group::create([
        'direction_id' => request()->direction_id,
        'group_name' => request()->group_name,
      ]);
    } catch (QueryException $ex) {
      return ['error' => $ex->getMessage()];
    }
    return ['success' => $group->id];
  }

  public function updateGroup($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);

      $updated = request()->all();
      try {

        $group->update($updated);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
      return  ['succes' => 'group was updated'];
    }
    return ['error' => 'group wasn\'t found'];
  }

  public function deleteGroup($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);
      try {
        Group::destroy($group_id);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
      return ['success' => 'group was deleted'];
    }
    return ['error' => 'group wasn\'t found'];
  }

  public function addUserInGroup($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);

      $users_id = array_map('intval', explode(',', request()->users));
      try {

        $group->users()->syncWithoutDetaching($users_id);

        (new FCMController)->subscribeToTopic("group_" . $group_id, $this->getDeviceTokens($users_id));
        (new FCMController)->subscribeToTopic("event_" . $group->direction->event->id, $this->getDeviceTokens($users_id));
      } catch (QueryException $ex) {

        return ['error' => $ex->getMessage()];
      }
    } else {
      return ["error" => "Group wasn\'t found"];
    }
    return ["success" => "User was added"];
  }

  public function getDeviceTokens(array $users_id)
  {
    $device_tokens =  [];

    $users = User::whereHas('tokens', function ($query) use ($users_id) {
      $query->whereIn('user_id', $users_id);
    })->get();

    foreach ($users as $user) {
      array_push($device_tokens, $user->tokens()->get()->pluck("device_token")->all());
    }

    $device_tokens = call_user_func_array('array_merge', $device_tokens);

    return $device_tokens;
  }

  public function deleteUserFromGroup($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);
      $users_id = array_map('intval', explode(',', request()->users));
      (new FCMController)->unsubscribeFromTopic("group_" . $group_id, $this->getDeviceTokens($users_id));
      (new FCMController)->unsubscribeFromTopic("event_" . $group->direction->event->id, $this->getDeviceTokens($users_id));
      try {
        $group->users()->detach($users_id);
      } catch (QueryException $ex) {
        return ['error' => $ex->getMessage()];
      }
    } else {
      return ["error" => "Group wasn\'t found"];
    }
    return ["success" => "Users was deleted"];
  }


  public function addGroupAdmins($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);
      $group_admins = array_map('intval', explode(',', request()->group_admins));

      $current_group_admins = $group->group_admins ? array_map('intval', explode(',', $group->group_admins)) : [];

      $result_array = array_unique(array_merge($group_admins, $current_group_admins), SORT_REGULAR);

      $group->group_admins = implode(",", $result_array);

      $group->save();
      return ['success' => 'admins were added'];
    }
    return ['error' => 'group wasn\'t found'];
  }


  public function deleteGroupAdmins($group_id)
  {
    $group = Group::find($group_id);

    if ($group) {
      self::isAllowed($group);
      $group_admins = array_map('intval', explode(',', request()->group_admins));

      $current_group_admins = $group->group_admins ? array_map('intval', explode(',', $group->group_admins)) : [];

      $result_array = array_diff($current_group_admins, $group_admins);

      $group->group_admins = implode(",", $result_array);

      $group->save();
      return ['success' => 'admins were deleted'];
    }
    return ['error' => 'group wasn\'t found'];
  }
}
