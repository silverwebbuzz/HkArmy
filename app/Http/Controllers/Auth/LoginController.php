<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Http\Models\User;
use App\Role;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Session;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use Mail;

/*use Illuminate\Foundation\Auth\AuthenticatesUsers;*/

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

	//use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	//protected $redirectTo = RouteServiceProvider::HOME;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
		$user = new User;
		$this->Role = new Role;
	}

   
	public function index(Request $request){
		 return view('auth.login');
	}

	public function logincheck(Request $request){
		$credential = $request->only('email','password');
		$findUser = User::where('email',$credential['email'])->first();
		if($findUser){
			// $checkVerified = User::where('email',$credential['email'])->where('email_verified',1)->count();
			// if($checkVerified){
			if($findUser->Status == 1){
				if (Hash::check($credential['password'], $findUser->password)) {
					Session::put('user', ['role_id' => $findUser->Role_ID, 'user_id' => $findUser->ID, 'email' => $findUser->email,'username' => $findUser->UserName,'Chinese_name' => $findUser->Chinese_name,'English_name' => $findUser->English_name,'image' => $findUser->image]);
					$redirect = '/';
					return response()->json(array('status' => 1,'message'=>'Login Successfully','redirecturl' => $redirect));
				}else{
					return response()->json(array('status' => 0,'message'=>'Invalid login credential...'));
				}
			}else{
				return response()->json(array('status' => 0,'message'=>'User Inactive.'));
			}
			// }else{
			//     // $verifyToken = Str::random(120);
			//     // $userData = User::find($findUser[0]->id);
			//     // $userData->remember_token = $verifyToken;
			//     // $save = $userData->save();
			//     // $data = array(
			//     //  'name' => ucfirst(trim($findUser[0]->name)).' '.ucfirst(trim($findUser[0]->surname)),
			//     //  'email' => $findUser[0]->email,
			//     //  'subject' => "WellFit360 Email Verify",
			//     //  'verifyUrl' => env('APP_URL').'/verifyAccount/',
			//     //  'verifyToken' => $verifyToken,
			//     // );

			//     // // Send email
			//     // Helper::sendMail($data);
			//     return response()->json(array('status' => 0,'message'=>'Your account is not verified. Please verify your account'));
			// }
		}else{
			return response()->json(array('status' => 0,'message'=>'Please enter registered email'));
		}
	}

	public function checkEmailRegister(Request $r){
		$email = $r->email;
		$user = User::where('email',$email)->count();
		if($user){
			echo 'true';    
		}else{
			echo 'false';
		}
	}

	public function CheckQrLogin(Request $request){
		$userid = base64_decode($request->user_id);
		$email = base64_decode($request->email);
		$user = User::where('ID',$userid)->where("Email",$email)->get()->toArray();
		if($user){
			Session::put('user', ['role_id' => $user[0]['Role_ID'], 'user_id' => $user[0]['ID'], 'email' => $user[0]['email'],'username' => $user[0]['UserName'], 'Chinese_name' => $user[0]['Chinese_name'], 'English_name' => $user[0]['English_name']]);
				$redirect = '/';
				return response()->json(array('status' => 1,'message'=>'Login Successfully','redirecturl' => $redirect));
		}else{
			$redirect = '/login';
			return response()->json(array('status' => 0,'message'=>'Invalid login credential...','redirecturl' => $redirect));
		}
	}

	public function logout() {
		Session::flush();
		return Redirect('login');
	}
}
