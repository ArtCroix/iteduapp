<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;

use App\Moodle\MdlUser;
use App\Moodle\MdlUserLocal;

use App\Http\Resources\MdlUser as MdlUserResource;
use App\Http\Resources\MdlUserLocal as MdlUserLocalResource;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Http\Resources\MdlUserCollection;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use JWTAuth; 

use Tymon\JWTAuth\Token;

use Tymon\JWTAuth\Manager;

use Tymon\JWTAuth\Exceptions\JWTException;

class MdlUserDataController extends Controller
{
	
	public function __construct ()
	{
		//$this->middleware('jwt.verify');
	}
		
	public function getUserApplication(Request $request, $id=null, $apply_id=0)
	{	

		$user = MdlUser::with(['apps' => function ($query) use ($apply_id) {
			$query->where('mdl3_apply.id', $apply_id);
		}])->where('id', $id)->first();
		
		if($user){
			return new MdlUserResource($user);
		}
		else{
			$error = ["error"=>"Пользователь не найден"];
			return json_encode($error);			
		}
	}	
	
	
	public function getUserAllApplication(Request $request, $id=null)
	{		
		$user = MdlUser::with('apps')->where('id', $id)->first();
		
		if($user){
			return new MdlUserResource($user);

		}
		else{
			$error = ["error"=>"Пользователь не найден"];
			return json_encode($error);			
		}
	}

	public function getUserGroups(Request $request, $id=null)
	{		
		//$user = MdlUserLocal::with('groups')->find($id);
		$user = MdlUserLocal::with('groups')->find($id);
		
		if($user){
			return new MdlUserLocalResource($user);
		}
		else{
			$error = ["error"=>"Пользователь не найден"];
			return json_encode($error);			
		}
	}
	
	public function getAllUsers(Request $request, $id=null)
	{		
		return MdlUserResource::collection(MdlUser::all()->where('id',"<>",1)->where('deleted',0));
	}
	
	public function getCurrentUser(Request $request)
	{	
		$token = JWTAuth::parseToken();

        return $token->getPayload()->get('user_data')->username; 
	}	
}

