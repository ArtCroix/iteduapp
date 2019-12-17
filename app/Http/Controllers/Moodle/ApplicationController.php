<?php

namespace App\Http\Controllers\Moodle;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Moodle\Application;
use App\Moodle\Value;
use App\Moodle\Item;
use Illuminate\Support\Facades\DB;
use App\Moodle\Submit;
use App\Http\Resources\Moodle\Application as ApplicationResource;
use App\Http\Resources\Moodle\Submit as SubmitResource;
use App\Http\Resources\Moodle\Item as ItemResource;
use App\Http\Resources\Moodle\Value as ValueResource;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApplicationController extends Controller
{
  protected $usersRaw;
  protected $submitsRaw;
  protected $usersCollection;
  protected $submitsCollection;
  protected $questions;
  protected $questionsCollection;
  protected $application_id;


  public function __construct(Request $request)
  {
    //  $this->middleware('jwt.verify');

    if (!$this->application_id = $request->application_id) {
      die();
    }

    $this->getRawSubmits();
    $this->getRawUsers();
    $this->getUsersCollection();
    $this->getQuestionsCollection();
  }


  const GET_USERS_QUERY = "SELECT
  mdl3_user.id AS user_id,
  mdl3_apply_submit.id AS submit_id,
  mdl3_user.lastname,
  mdl3_user.firstname,
  mdl3_user.firstname,
  mdl3_user_info_data.data as thirdname
  FROM
  mdl3_user INNER JOIN mdl3_user_info_data on mdl3_user.id=mdl3_user_info_data.userid
  INNER JOIN mdl3_apply_submit ON mdl3_user.id = mdl3_apply_submit.user_id
  WHERE
  mdl3_apply_submit.apply_id = ? and mdl3_user_info_data.fieldid=4";

  const INSERT_MISSING_ANSWERS_QUERY = "insert into mdl3_apply_value (submit_id, item_id, value, time_modified, version) 
  SELECT avi.submit_id, avi.item_id, '' as `value`,UNIX_TIMESTAMP() as time_modified, '1' version from(
  SELECT DISTINCT mdl3_apply_value.submit_id, mdl3_apply_item.id as item_id, mdl3_apply_item.name
        FROM mdl3_apply_value, mdl3_apply_item
  WHERE
    submit_id IN (
      SELECT
        id
      FROM
        mdl3_apply_submit
      WHERE
        apply_id =  ?
    )
  AND apply_id =  ?
  AND NAME <> 'label') avi 
  LEFT JOIN mdl3_apply_value a_v ON (avi.submit_id = a_v.submit_id AND avi.item_id = a_v.item_id)
  WHERE a_v.submit_id IS NULL;
  ";

  const GET_QUESTIONS_QUERY = "SELECT
  mdl3_apply_item.`id`,
  mdl3_apply_item.`name` as question,
  mdl3_apply_item.presentation
  FROM
  mdl3_apply_item
  WHERE
  mdl3_apply_item.apply_id = ?";

  const GET_SUBMITS_QUERY = "SELECT
  mdl3_user.id AS user_id,
  mdl3_apply_item.`typ`,
  mdl3_apply_value.`value` as answer,
  mdl3_apply_value.`id` as value_id,
  mdl3_apply_item.presentation
  FROM
  mdl3_apply_submit
  INNER JOIN mdl3_user ON mdl3_user.id = mdl3_apply_submit.user_id
  INNER JOIN mdl3_apply_value ON mdl3_apply_submit.id = mdl3_apply_value.submit_id
  INNER JOIN mdl3_apply_item ON mdl3_apply_value.item_id = mdl3_apply_item.id
  WHERE
  mdl3_apply_submit.apply_id = ? order by position";



  public static function isAllowed($app = null)
  {
    $payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());

    if ($payload['user_data']->approle == 'admin') {

      return true;
    }

    exit(json_encode(["error" => "not allowed"]));
  }

  public function allApps()
  {
    return ApplicationResource::collection(Application::all());
  }

  /*   public function getApplication($app_id)
  {
    $app = Application::with('submits')->find($app_id);

    if ($app) {

      return new ApplicationResource($app);
    } else {
      return ['error' => 'app wasn\'t found'];
    }
  } */

  public function addMissingAnswers()
  {
    DB::connection('itedu')->select(static::INSERT_MISSING_ANSWERS_QUERY, [$this->application_id, $this->application_id]);
  }

  public function getQuestions()
  {
    $this->questions = DB::connection('itedu')->select(static::GET_QUESTIONS_QUERY, [$this->application_id]);
  }

  public function getRawUsers()
  {
    $this->usersRaw = DB::connection('itedu')->select(static::GET_USERS_QUERY, [$this->application_id]);
  }

  public function getRawSubmits()
  {
    $this->submitsRaw = DB::connection('itedu')->select(static::GET_SUBMITS_QUERY, [$this->application_id]);
  }

  public function getColumnsFromUsers()
  {
    return  collect(
      ["users_columns" => ["user_id,", "submit_id", "lastname", "firstname", "thirdname"]]
    );

    // return ["user_id,", "submit_id", "lastname", "firstname", "thirdname"];
  }

  public function getQuestionsCollection()
  {
    // dd($this->getColumnsFromUsers());

    $this->getQuestions();

    $this->questionsCollection = collect(
      $this->questions
    );

    $this->questionsCollection = [$this->getColumnsFromUsers()->merge(["app_columns" => $this->questionsCollection])];

    // dd($this->questionsCollection);
    // $this->questionsCollection = [$this->getColumnsFromUsers()];
  }

  public function getUsersCollection()
  {
    $this->usersCollection = collect(
      $this->usersRaw
    );

    $this->getSubmitsCollection();

    $this->usersCollection = $this->usersCollection->keyBy('user_id');

    $this->usersCollection->map(function (&$user, $key) {
      $user->submit = $this->submitsCollection[$key];
    });
  }

  public function sanitizeSubmitsCollection(array $forSanitize = [])
  {
    $this->submitsCollection->map(function (&$submits) use ($forSanitize) {

      foreach ($submits as $column => $submit) {

        foreach ($forSanitize as $stdKey) {
          unset($submit->$stdKey);
        }
      }
    });

    // dd($this->submitsCollection);
  }


  public function getSubmitsCollection()
  {
    $this->submitsCollection = collect(
      $this->submitsRaw
    );
    $this->submitsCollection = $this->submitsCollection->groupBy('user_id');

    $this->submitsCollection->map(function (&$submits) {

      foreach ($submits as $submit) {

        if ($submit->typ == "multichoice") {

          $presentation_keys = explode("|", $submit->answer); // создать массив из строки отображений

          $submit->presentation = substr_replace($submit->presentation, null, 0, 6); //удалить из ответа подстроку вида с(r)>>>>>

          $multi_values = explode("\r|",  $submit->presentation); //создать массив из возможных значений

          array_unshift($multi_values, "N/A"); //добавить нулевой элемент в массив отображений

          $filtered_values = array_filter($multi_values, function ($key) use ($presentation_keys) {
            return in_array($key, $presentation_keys);
          }, ARRAY_FILTER_USE_KEY); //получиь массив нужных значений

          $submit->answer = implode("<br>", $filtered_values);
        }
      }
    });
  }


  public function createTableHead()
  {
    echo "<thead>";

    echo "<tr>";

    echo "</tr>";
    echo "</thead>";
  }

  public function createTable()
  {

    echo "<table>";
    echo "<thead>";
    echo "</thead>";

    echo "<tbody>";


    $this->usersCollection->map(function ($users, $key) {

      echo "<tr>";
      echo "<td>" . $users->user_id . "</td>";
      echo "<td>" . $users->submit_id . "</td>";
      echo "<td>" . $users->lastname . "</td>";
      echo "<td>" . $users->firstname . "</td>";

      foreach ($users->submit as $submit) {

        echo "<td>" . $submit->answer . "</td>";
      }

      echo "</tr>";
    });
    echo "</tbody>";
    echo "</table>";
  }

  public function applicationToJSON()
  {


    $this->sanitizeSubmitsCollection(['typ', 'presentation', 'user_id']);

    $str = '{"data":[{ "columns":[ ';

    foreach ($this->questionsCollection as $question) {

      $str .=  json_encode($question) . ",";
    }
    $str = rtrim($str, ',');
    $str .= "]},";
    $str .= '{"answers":[';
    foreach ($this->usersCollection as $user) {

      $str .=  json_encode($user) . ",";
    }
    $str = rtrim($str, ',');
    $str .= ']}]}';

    return $str;
  }

  public function applicationToSerialize()
  {
    $this->ser = igbinary_serialize($this->getUsersCollection());
  }

  public function getApplication()
  {
    echo $this->applicationToJSON();
  }
}
