<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EilteModel;
use App\Http\Models\Subteam;
use Session;
use App\Helpers\Helper;

class SubTeamController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$Subteam = Subteam::with('elite')->orderBy('id','desc')->get()->toArray();
			return view('Subteam.list_subteam',compact('Subteam'));
		}else{
			return redirect('/');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		if(in_array('settings_create', Helper::module_permission(Session::get('user')['role_id']))){
			$Eiltes = EilteModel::where('status','1')->get()->toArray();
			return view('Subteam.add_subteam',compact('Eiltes'));
		}else{
			return redirect('/');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$Subteam = new Subteam;
		$Subteam->elite_id = !empty($request->elite) ? $request->elite : NULL;
		$Subteam->subteam_ch = !empty($request->chinesesubteam) ? $request->chinesesubteam : NULL;
		$Subteam->subteam_en = !empty($request->englishsubteam) ? $request->englishsubteam : NULL;
		$Subteam->status = isset($request->status) ? $request->status : "2";
		$result = $Subteam->save();  // save data
		if($result){
			return redirect('subteam')->with('success_msg', 'Subteam added successfully.');
		}else{
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(in_array('settings_write', Helper::module_permission(Session::get('user')['role_id']))){
			$Eiltes = EilteModel::where('status','1')->get()->toArray();
			$Subteam = Subteam::find($id)->toArray();
			return view('Subteam.subteam_edit',compact('Eiltes','Subteam'));
		}else{
			return redirect('/');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$Subteam = Subteam::find($id);
		$Subteam->elite_id = !empty($request->elite) ? $request->elite : NULL;
		$Subteam->subteam_ch = !empty($request->chinesesubteam) ? $request->chinesesubteam : NULL;
		$Subteam->subteam_en = !empty($request->englishsubteam) ? $request->englishsubteam : NULL;
		$Subteam->status = isset($request->status) ? $request->status : "2";
		$result = $Subteam->save();  // save data
		if($result){
			return redirect('subteam')->with('success_msg', 'Subteam Updated successfully.');
		}else{
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$Subteam = Subteam::where('id',$id)->delete();
		if($Subteam){
			$message = 'Subteam deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}
}