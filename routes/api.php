<?php

use App\Http\Middleware\CheckRoute;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'email', 'name' => 'email', 'namespace' => 'Emails'], function () {
    Route::post('/event', "EventController@index");
    Route::get('/event', "EventController@wrong");
});

Route::group(['namespace' => 'Mobile'], function () {

    //////////////////////////User Auth Routes//////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::any('/authenticateuser', 'AuthenticationController@authenticateUser')->middleware(CheckRoute::class . ":POST");

    Route::any('/unauthenticateuser/{user_id}', 'AuthenticationController@unAuthenticateUser')->middleware([CheckRoute::class . ":POST"]);

    //////////////////////////User Data Routes//////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::any('/allusers', 'UserDataController@getAllUsers')->middleware([CheckRoute::class . ":POST,get"]);

    Route::any('/userallapplicationinfo/{user_id?}', 'UserDataController@getUserAllApplication')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/user/{user_id?}', 'UserDataController@getUser')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/userappinfo/{user_id?}/{apply_id?}', 'UserDataController@getUserSingleApplication')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/usergroups/{user_id}', 'UserDataController@getUserGroups')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updateuser/{user_id}', 'UserDataController@updateUser')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/gu/{user_id}', 'UserDataController@gu')->middleware([CheckRoute::class . ":POST"]);

    ////////////////////////////Email Routes//////////////////////////////////////////////////////////////////

    Route::any('/email/os', 'EmailController@osSendEmail')->middleware(CheckRoute::class . ":POST,get");

    ////////////////////////Events Routes/////////////////////////////////////////////////////////////////////////////////

    Route::any('/addevent', 'EventController@addEvent')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/allevents', 'EventController@getAllEvents')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/events/{event_id}', 'EventController@getEvent')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updateevent/{event_id}', 'EventController@updateEvent')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/deleteevent/{event_id}', 'EventController@deleteEvent')->middleware([CheckRoute::class . ":POST"]);

    ///////////////////////Directions Routes/////////////////////////////////////////////////////////////////////////////

    Route::any('/alldirections', 'DirectionController@getAllDirections')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/directions/{direction_id?}', 'DirectionController@getDirection')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/adddirection', 'DirectionController@addDirection')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updatedirection/{direction_id}', 'DirectionController@updateDirection')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/deletedirection/{direction_id}', 'DirectionController@deleteDirection')->middleware([CheckRoute::class . ":POST"]);

    /////////////////Groups Routes//////////////////////////////////////////////////////////////////

    Route::any('/allgroups', 'GroupController@getAllGroups')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/groups/{group_id?}', 'GroupController@getGroup')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/addgroup', 'GroupController@addGroup')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/groupinfo/{group_id?}', 'GroupController@getGroupInfo')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/adduserintogroup/{group_id}', 'GroupController@addUserInGroup')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/deleteuserfromgroup/{group_id}', 'GroupController@deleteUserFromGroup')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updategroup/{group_id}', 'GroupController@updateGroup')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/deletegroup/{group_id}', 'GroupController@deleteGroup')->middleware([CheckRoute::class . ":POST"]);

    /////////////////////////////////////////////Notification routes////////////////////////////

    Route::any('/sendnotification', 'FCMController@sendNotification')->middleware([CheckRoute::class . ":POST,get"]);

    /////////////////////////////////////////////Schedule routes////////////////////////////

    Route::any('/addschedule/{group_id}', 'ScheduleController@addSchedule')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updateschedule/{schedule_id}', 'ScheduleController@updateSchedule')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/deleteschedule/{schedule_id}', 'ScheduleController@deleteSchedule')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/allschedules', 'ScheduleController@getAllSchedules')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/addroominschedule/{schedule_id}', 'ScheduleController@addRoomInSchedule')->middleware([CheckRoute::class . ":POST"]);

    /////////////////////////////////////////////Room routes////////////////////////////

    Route::any('/addroom', 'RoomController@addRoom')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/allrooms', 'RoomController@getAllRooms')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/updateroom/{room_id}', 'RoomController@updateRoom')->middleware([CheckRoute::class . ":POST"]);

    //////////////////////////////////////Quiz Routes///////////////////////////////////////////////

    Route::any('/allcourses', 'CourseController@allCourses')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/courses/{course_id}', 'CourseController@getCourse')->middleware([CheckRoute::class . ":POST"]);

    //////////////////////////////////////QuizAttempt Routes///////////////////////////////////////////////

    Route::any('/allattempts', 'QuizAttemptController@allAttempts')->middleware([CheckRoute::class . ":POST"]);

    //////////////////////////////////////Course Routes///////////////////////////////////////////////

    Route::any('/courses/{course_id}', 'CourseController@getCourse')->middleware([CheckRoute::class . ":POST"]);
});

Route::group(['namespace' => 'Moodle'], function () {

    ////////////////////////////////Application routes///////////////////////////////////////////////////////////////
    Route::any('/allapps', 'ApplicationController@allApps')->middleware([CheckRoute::class . ":POST"]);

    Route::any('/apps/{application_id?}', 'ApplicationController@getApplication')->middleware([CheckRoute::class . ":POST,get"]);
});
//////////////////////////////////////////////////////////////////////////////////////
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
