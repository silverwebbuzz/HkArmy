<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\User;
use App\Http\Models\Attendance;
use App\Http\Models\Events;
use App\Http\Models\OrderItems;
use Illuminate\Support\Facades\App;
use Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;
use App\Http\Models\MemberTokenStatus;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('login');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{
		$total_users = User::count();

		$total_mentor_users = User::where('team',2)->count();
		$total_elite_users = User::where('team',3)->count();
		$total_district_users = User::where('team',4)->count();

		$Attendance = Attendance::count();
		$Events = Events::count();
		$months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
		foreach ($months as $key => $value) {
			$allyearcountusers[] = User::whereMonth('created_at', date($key))->whereYear('created_at', date('Y'))->count();
		}
		$product_amount = OrderItems::sum('product_amount');
		return view('home',compact('total_users','total_mentor_users','total_elite_users','total_district_users','Attendance','Events','allyearcountusers','product_amount'));
	}

	/**
	** USE : Filter Graph data by 'Daily, Monthly, Weekly'
	**/
	public function filterGraph(Request $request)
	{
		if (App::isLocale('en')) {
			if($request->GraphType == 'daily'){
                $usrestext = 'Users';
            }else if($request->GraphType == 'weekly'){                
                $usrestext = 'Users';
            }else{                
                $usrestext = 'Users';
            }
		}else{
			if($request->GraphType == 'daily'){                
                $usrestext = 'Users';
            }else if($request->GraphType == 'weekly'){
                $usrestext = 'Users';
            }else{                
                $usrestext = 'Users';
            }
		}

		if($request->GraphType == 'daily'){

			for($i = 1; $i <=  date('t'); $i++){
			   // add the date to the dates array
			   $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
			}
			$months = [];
			foreach ($dates as $key => $value) {
				$allyearcountusers[] = User::whereDate('created_at', date($value))->count();
				$months[] = $value;
			}
		}else if($request->GraphType == 'weekly'){
			$monthsname = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			$months = [];
			foreach ($monthsname as $key => $value) {
				$allyearcountusers[] = User::whereMonth('created_at', date($key))->whereYear('created_at', date('Y'))->count();
				$months[] = $value;
			}
		}else{
			$monthsname = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			$months = [];
			foreach ($monthsname as $key => $value) {
				$allyearcountusers[] = User::whereMonth('created_at', date($key))->whereYear('created_at', date('Y'))->count();
				$months[] = $value;
			}
		}

		$userarr = [];
		foreach ($allyearcountusers as $key => $value1) {
			$userarr[] = $value1;
		}


		return response()->json(
			array(
				'months' => $months,
				'usrestext' => $usrestext,
				'userarr' => $userarr
			)
		);

		// foreach ($months as $key => $value) {
		// 	$allyearcountusers[] = User::whereMonth('created_at', date($key))->whereYear('created_at', date('Y'))->count();
		// }
		// $product_amount = OrderItems::sum('product_amount');
		// return view('home',compact('users','Attendance','Events','allyearcountusers','product_amount'));
	}

	public function changepassword(Request $request){
		if(Session::get('user')['user_id'] != ''){
			$method = $request->method();
			if($request->isMethod('get')) {
				return view('changePassword');
			}
			if($request->isMethod('post')) {
				$userData = User::where('ID',Session::get('user')['user_id'])->first();
				if(Hash::check($request->old_password,$userData->password)){
					if($request->new_password == $request->confirm_password){
						$user = User::find(Session::get('user')['user_id']);
						$user->password  =  Hash::make($request->new_password);
						$user->save();
						if($user){
							return response()->json(array('status' => 1,'message'=>'Password has been changed successfully...'));
						}else{
							return response()->json(array('status' => 0,'message'=>'Something went wrong.','redirecturl' => ''));
						}
					}else{
						return response()->json(array('status' => 0,'message'=>'New password & Confirm Password does not match','redirecturl' => ''));
					}
				}else{
					return response()->json(array('status' => 0,'message'=>'Old password incorrect!','redirecturl' => ''));
				}
			}
		}else{
			return redirect('login');
		}
	}

	public function userProfile(Request $request){
		
		$method = $request->method();
		if($request->isMethod('get')) {
			$userData = User::where('ID',Session::get('user')['user_id'])->first();
			$tokenDetail = MemberTokenStatus::where('user_id',Session::get('user')['user_id'])->first();
			return view('profile',compact('userData','tokenDetail'));
		}
		if($request->isMethod('post')) {
			$userData = User::find(Session::get('user')['user_id']);
			$public_path = 'assets/image';
			$fullImagePath = null;
			if($request->hasfile('image')){
				$image = $request->file('image');
				$name =  time().$image->getClientOriginalName();
				$image->move(public_path($public_path),$name);
				$fullImagePath = $public_path.'/'.$name;
			}
			
			$userData->Chinese_name = $request->Chinese_name;
			$userData->English_name = $request->English_name;
			$userData->Chinese_address = $request->Chinese_address;
			$userData->English_address = $request->English_address;
			if(isset($request->image) && !empty($fullImagePath)){
				$userData->image        = $fullImagePath;
			}
			$result = $userData->save();
			if($result){
				return response()->json(array('status' => 1,'message'=>'Profile Updated successfully.'));
			}else{
				return response()->json(array('status' => 0,'message'=>'Something went wrong.'));
			}
		}
	}
}
