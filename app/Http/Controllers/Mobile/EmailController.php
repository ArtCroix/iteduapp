<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Support\Facades\Mail;
use App\Mail\Event as MailEvent;
use App\Mobile\Email;
use App\Mobile\Subject;
use JWTAuth;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{


	private $user_data;

	public function getUserFromJWT()
	{
		try {
			$payload = JWTAuth::manager()->getJWTProvider()->decode(JWTAuth::getToken()->get());
			$this->user_data = $payload['user_data'];
		} catch (\Throwable $ex) {
			return;
		}
	}


	public function __construct()
	{
		$this->middleware('jwt.verify');
		$this->getUserFromJWT();
	}

	public function osSendEmail(Request $request)
	{
		$subject = Subject::with(['emails' => function ($query) use ($request) {
			return $query->wherePivot('event_id', $request->event_id);
		}])->where('subject',  $request->subject)->first();

		$address_emails = [];

		foreach ($subject->emails as $email) {
			$address_emails[] = $email->email;
		}

		Mail::to($address_emails)->send(new MailEvent($request, $this->user_data));
		// return (new MailEvent($request, $this->user_data))->render();
	}
}
