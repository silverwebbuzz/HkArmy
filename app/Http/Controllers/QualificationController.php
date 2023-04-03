<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\QualificationModel;
use Session;
use App\Helpers\Helper;

class QualificationController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$Qualifications = QualificationModel::orderBy('id','desc')->get()->toArray();
			return view('Qualification.qualification_list',compact('Qualifications'));
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
			return view('Qualification.qualification_create');
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
		$qualification = new QualificationModel;
		$qualification->qualification_ch = !empty($request->chinesequalification) ? $request->chinesequalification : NULL;
		$qualification->qualification_en = !empty($request->englishqualification) ? $request->englishqualification : NULL;
		$qualification->status = isset($request->status) ? $request->status : "2";
		$result = $qualification->save();  // save data
		if($result){
			return redirect('qualification')->with('success_msg', 'Qualification added successfully.');
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
			$Qualification = QualificationModel::find($id)->toArray();
			return view('Qualification.qualification_edit',compact('Qualification'));
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
		$qualification = QualificationModel::find($id);
		$qualification->qualification_ch = !empty($request->chinesequalification) ? $request->chinesequalification : NULL;
		$qualification->qualification_en = !empty($request->englishqualification) ? $request->englishqualification : NULL;
		$qualification->status = isset($request->status) ? $request->status : "2";
		$result = $qualification->save();  // save data
		if($result){
			return redirect('qualification')->with('success_msg', 'Qualification Updated successfully.');
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
		$qualification = QualificationModel::where('id',$id)->delete();
		if($qualification){
			$message = 'Qualification deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}
}
