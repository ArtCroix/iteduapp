<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Moodle\Quiz;
use App\Moodle\Course;
use App\Http\Resources\Quiz as QuizResource;
use App\Http\Resources\QuizCollection;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\CourseCollection;
use App\Moodle\QuizAttempt;
use JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException;

class CourseController extends Controller
{
    
    public function __construct ()
    {
    //  $this->middleware('jwt.verify');
    }

    public static function isAllowed($event=null)
    {
      $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());
              
      if($payload['user_data']->approle=='admin'){

         return true;
      }    

      exit(json_encode(["error"=>"not allowed"]));
    }


    public function allCourses()
    {
      $courses=Course::with('quizes')->get();

      return new CourseCollection($courses);
     // return new CourseResource($course);
    }

    public function getCourse($course_id)
    {
      $courses=Course::with('quizes')->find($course_id);

      return new CourseResource($courses);
     // return new CourseResource($course);
    }
    


}
