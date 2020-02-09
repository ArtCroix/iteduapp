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

    Route::any('/getdt', 'FCMController@getAllSubscriptions')->middleware(CheckRoute::class . ":post");
    Route::any('/test/{group_id?}', 'GroupController@test')->middleware(CheckRoute::class . ":post");

    //////////////////////////User Auth Routes//////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::any('/authenticateuser', 'AuthenticationController@authenticateUser')->middleware(CheckRoute::class . ":post");

    Route::any('/unauthenticateuser/{user_id}', 'AuthenticationController@unAuthenticateUser')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["user_access"]);

    //////////////////////////User Data Routes//////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::any('/allusers', 'UserDataController@getAllUsers')->middleware([CheckRoute::class . ":post,get"]);
    // ->middleware(["jwt.verify"]);

    Route::any('/userallapplicationinfo/{user_id?}', 'UserDataController@getUserAllApplication')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["user_access"]);

    Route::any('/userbaseinfo/{user_id?}', 'UserDataController@getUserBaseInfo')->middleware([CheckRoute::class . ":post"])->middleware(["jwt.verify"]);

    Route::any('/user/{user_id?}', 'UserDataController@getUser')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["user_access"]);

    Route::any('/userappinfo/{user_id?}/{apply_id?}', 'UserDataController@getUserSingleApplication')->middleware([CheckRoute::class . ":post"]);

    Route::any('/usergroups/{user_id}', 'UserDataController@getUserGroups')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/updateuser/{user_id}', 'UserDataController@updateUser')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["user_access"]);

    Route::any('/gu/{user_id}', 'UserDataController@gu')->middleware([CheckRoute::class . ":post"]);

    ////////////////////////////Email Routes//////////////////////////////////////////////////////////////////

    Route::any('/email/os', 'EmailController@osSendEmail')->middleware(CheckRoute::class . ":post,get");

    ////////////////////////Events Routes/////////////////////////////////////////////////////////////////////////////////

    Route::any('/addevent', 'EventController@addEvent')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["event_access"]);

    Route::any('/allevents', 'EventController@getAllEvents')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/events/{event_id}', 'EventController@getEvent')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/updateevent/{event_id}', 'EventController@updateEvent')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["event_access"]);

    Route::any('/deleteevent/{event_id}', 'EventController@deleteEvent')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["event_access"]);

    ///////////////////////Directions Routes/////////////////////////////////////////////////////////////////////////////

    Route::any('/alldirections', 'DirectionController@getAllDirections')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/directions/{direction_id?}', 'DirectionController@getDirection')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/adddirection', 'DirectionController@addDirection')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["event_access"]);

    Route::any('/updatedirection/{direction_id}', 'DirectionController@updateDirection')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["direction_access"]);

    Route::any('/deletedirection/{direction_id}', 'DirectionController@deleteDirection')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["direction_access"]);

    /////////////////Groups Routes//////////////////////////////////////////////////////////////////

    Route::any('/allgroups', 'GroupController@getAllGroups')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/groups/{group_id?}', 'GroupController@getGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/addgroup', 'GroupController@addGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["direction_access"]);

    Route::any('/groupinfo/{group_id?}', 'GroupController@getGroupInfo')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"]);

    Route::any('/adduserintogroup/{group_id}', 'GroupController@addUserInGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["group_access"]);

    Route::any('/deleteuserfromgroup/{group_id}', 'GroupController@deleteUserFromGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["group_access"]);

    Route::any('/changegroupdirection/{group_id}', 'GroupController@changeGroupDirection')->middleware([CheckRoute::class . ":post,get"])
        ->middleware(["jwt.verify"])
        ->middleware(["group_access"]);

    Route::any('/updategroup/{group_id}', 'GroupController@updateGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["group_access"]);

    Route::any('/deletegroup/{group_id}', 'GroupController@deleteGroup')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["group_access"]);

    /////////////////////////////////////////////Notification routes////////////////////////////

    Route::any('/sendnotification', 'FCMController@sendNotificationToSpecificTarget')->middleware([CheckRoute::class . ":post,get"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);
    Route::any('/sendnotificationtodevices', 'FCMController@sendNotificationToSpecificDevices')->middleware([CheckRoute::class . ":post,get"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    /////////////////////////////////////////////Schedule routes////////////////////////////

    Route::any('/addschedule/{group_id}', 'ScheduleController@addSchedule')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    Route::any('/updateschedule/{schedule_id}', 'ScheduleController@updateSchedule')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    Route::any('/deleteschedule/{schedule_id}', 'ScheduleController@deleteSchedule')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    Route::any('/allschedules', 'ScheduleController@getAllSchedules')->middleware([CheckRoute::class . ":post"]);

    Route::any('/addroominschedule/{schedule_id}', 'ScheduleController@addRoomInSchedule')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    /////////////////////////////////////////////Room routes////////////////////////////

    Route::any('/addroom', 'RoomController@addRoom')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    Route::any('/allrooms', 'RoomController@getAllRooms')->middleware([CheckRoute::class . ":post"]);

    Route::any('/updateroom/{room_id}', 'RoomController@updateRoom')->middleware([CheckRoute::class . ":post"])
        ->middleware(["jwt.verify"])
        ->middleware(["admin_access"]);

    //////////////////////////////////////Quiz Routes///////////////////////////////////////////////

    Route::any('/allcourses', 'CourseController@allCourses')->middleware([CheckRoute::class . ":post"]);

    Route::any('/courses/{course_id}', 'CourseController@getCourse')->middleware([CheckRoute::class . ":post"]);

    //////////////////////////////////////QuizAttempt Routes///////////////////////////////////////////////

    Route::any('/allattempts', 'QuizAttemptController@allAttempts')->middleware([CheckRoute::class . ":post"]);

    //////////////////////////////////////Course Routes///////////////////////////////////////////////

    Route::any('/courses/{course_id}', 'CourseController@getCourse')->middleware([CheckRoute::class . ":post"]);
});

Route::group(['namespace' => 'Moodle'], function () {

    ////////////////////////////////Application routes///////////////////////////////////////////////////////////////
    Route::any('/allapps', 'ApplicationController@allApps')->middleware([CheckRoute::class . ":post"]);

    Route::any('/apps/{application_id?}', 'ApplicationController@getApplication')->middleware([CheckRoute::class . ":post,get"]);
});
