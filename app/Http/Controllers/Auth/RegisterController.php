<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\Helper;
use App\Http\Models\User;
use DateTime;
use Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use Mail;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = RouteServiceProvider::HOME;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	public function index(){
		$get_last_unique_number = Helper::getLastRefrenceNumber();
        $unique_id = (1 + $get_last_unique_number);
		return view('auth.register',compact('unique_id'));
	}

	/**
	 * USE : Register user
	 */
	public function register(Request $request){
		if ($request->isMethod('get')) {
			return redirect('login');
		}
		if ($request->isMethod('post')) {
			$existingEmail = false;
			$existingEmail = User::where('Email',$request->email)->count();
			// if($existingEmail){
			// 	return response()->json(array('status' => 0,'message'=>'Email already exists..Try to other email..'));
			// }else{
				$random = Str::random(8);
				$hashpassword = Hash::make($random);
				$User = new User;
				$User->Role_ID = "2";
				$User->UserName = trim($request->username);
				$User->Email = trim($request->email);
				$User->Password = $hashpassword;
				$newjoindate = date("Y-m-d",strtotime($request->joindate));
				$User->JoinDate = $newjoindate;
				$newdob =  date("Y-m-d",strtotime($request->dob));
				$User->DOB = $newdob;
				$User->MemberCode = $request->membercode;
				$User->HkidNumber = $request->hkidnumber;
				$User->EmergencyContact = $request->emergencycontact;
				$User->Address = $request->address;
				$result = $User->save();  // save data
				$lastinsertid = $User->ID;
				if($result){
					$ID = $lastinsertid;
					$Email = $request->email;
					$UserName = $request->username;
					$Password = $hashpassword;
					$user_id = base64_encode($ID);
					$email_add = base64_encode($Email);
					$userdata = $user_id ."/". $email_add."/".trim($UserName);
					$public_path = public_path().'/image';
					$qrCode = new QrCode($userdata);
					$qrCode->setSize(200);
					$qrimag = trim($request->username)."-".time().".png";
					$qrcodeimag = $qrCode->writeFile($public_path.'/'.$qrimag);
					$dataUri = $qrCode->writeDataUri();
					
					$dataarr = array(
						'name' => $request->username,
						'email' => $request->email,
						'subject' => "Qrcode",
						'qrcode' => $dataUri,
					);
					Mail::send('email.sendCredential',$dataarr, function ($message) use ($dataarr,$qrimag,$public_path,$Email) {
						$attchmentImage = $public_path.'/'.$qrimag;
						$message->to($Email)
						->subject('Qr Code')
						->attach($attchmentImage, [
							'as' => 'qrcode.png',
							'mime' => 'image/png'
						]);
					});
					$qrImage = User::find($lastinsertid);
					$qrImage->QrCode = $qrimag;
					$qrImage->save();
					//return redirect('/register')->with('success_msg', 'Your account register successfully.');
					$redirect = '/login';
					return response()->json(array('status' => 1,'message'=>'Your registration has been successfully completed.','redirecturl' => $redirect));
				}else{
					return response()->json(array('status' => 0,'message'=>'Please try again..'));
				}
			//}
		}
	}

	public function EmailCheckRegister(Request $r){
		$email = !empty($r->email) ? $r->email : '';
		$user = User::where('Email',$email)->count();
		if($user){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name' => ['required', 'string'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string'],
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data)
	{

	}

	/**
	 * Account verified
	 */
	// public function verifyAccount($token){
	// 	if($token){
	// 		$findUser = User::where('verified_token',$token)->first();
	// 		$datetime1 = strtotime($findUser['updated_at']);
	// 		$datetime2 = strtotime(date('Y-m-d H:i:s'));
	// 		$interval  = abs($datetime2 - $datetime1);
	// 		$minutes   = round($interval / 60);
	// 		if($minutes <= 10){
	// 			if($findUser){
	// 				$findUser->remember_token = null;
	// 				$findUser->email_verified = 1;
	// 				$save = $findUser->save();
	// 				if($save){
	// 					return redirect('/login')->with('success_msg', 'Your account verified successfully.');
	// 				}else{
	// 					return redirect('/login')->with('error_msg', 'Please try again...');
	// 				}
	// 			}else{
	// 				return redirect('/login')->with('error_msg', 'Verification link has been expired....');
	// 			}
	// 		}else{
	// 			$findUser->remember_token = null;
	// 			$save = $findUser->save();
	// 			return redirect('/login')->with('error_msg', 'Verification link has been expired....');;
	// 		}
	// 	}
	// }
}
