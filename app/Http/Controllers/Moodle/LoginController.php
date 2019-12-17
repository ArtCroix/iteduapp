<?php

namespace App\Http\Controllers\Mdl;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
	
	public function showLoginForm()
    {
        return view('mdlauth.login');
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'mdl/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
/* 	protected function guard()
    {
        return Auth::guard('mdl_user');
    } */
	
	public function login(Request $request)
	{
		$this->validate($request, [
			'email'=>'required|email',
			'password'=>'required|min:1'
		]);
		
		if(Auth::guard('mdl_user')->attempt(['email'=>$request->email,'password'=>$request->password], $request->remember)){
			return redirect()->intended(route('mdlhome'));
		}
		
		return redirect()->back()->withInput($request->only('email', 'remember'));
	}
	
	public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : 'mdl/home';
    }
	
	public function logout()
	{
		Auth::guard('mdl_user')->logout();

		return redirect('/');
	}
}
