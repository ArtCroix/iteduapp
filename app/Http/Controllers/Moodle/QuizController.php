<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Moodle\Quiz;
use App\Moodle\Course;
use App\Http\Resources\Quiz as QuizResource;
use App\Http\Resources\QuizCollection;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\QuizAttempt as QuizAttemptResource;
use App\Http\Resources\CourseCollection;
use App\Moodle\QuizAttempt;
use JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException;

class QuizController extends Controller
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

    public function getQuizesByCourse($course_id)
    {
      $attempts=QuizAttempt::all();
      

/*       $courses = Course::with('quizes')->find($course_id);

      return  new CourseResource($courses); */
    //  return  new QuizAttemptResource($attempts); 
    }
    


}
