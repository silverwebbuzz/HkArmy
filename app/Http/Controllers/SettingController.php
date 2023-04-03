<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Settings;
use App\Helpers\Helper;

class SettingController extends Controller
{
    public function siteSetting(){
    	if(in_array('settings_write', Helper::module_permission(Session::get('user')['role_id']))){
			$Setting = Settings::first();
			return view('setting',compact('Setting'));
		}else{
			return redirect('/');
		}
	}

	public function update(Request $request){
		$public_path = 'assets/image';
		$fullImagePath = null;
		if($request->hasfile('image')){
			$image = $request->file('image');
			$name =  time().$image->getClientOriginalName();
			$image->move(public_path($public_path),$name);
			$fullImagePath = $public_path.'/'.$name;
		}
		$Setting = Settings::first();
		$Setting->min_hour = $request->min_hour;
		$Setting->HKD = $request->HKD;
		$Setting->SiteName = $request->SiteName;
		$Setting->token_expire_day = !empty($request->tokenExpireDay) ? $request->tokenExpireDay : NULL;
		if(isset($request->image) && !empty($fullImagePath)){
			$Setting->Logo        = $fullImagePath;
		}
		$result = $Setting->save();
		if($result){
			//return response()->json(array('status' => 1,'message'=>'Settings Updated successfully.'));
			return redirect('setting')->with('success_msg', 'Settings Updated successfully.');
		}else{
			//return response()->json(array('status' => 0,'message'=>'Something went wrong.'));
			return redirect('setting')->with('success_msg', 'Something went wrong.');
		}
	}
}
