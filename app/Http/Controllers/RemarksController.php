<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Remarks;
use Session;
use App\Helpers\Helper;

class RemarksController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$Remarks = Remarks::orderBy('id','desc')->get()->toArray();
			return view('Remarks.remarks_list',compact('Remarks'));
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
			return view('Remarks.remarks_add');
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
		$Remarks = new Remarks;
		$Remarks->remarks_ch = !empty($request->chineseremarks) ? $request->chineseremarks : NULL;
		$Remarks->remarks_en = !empty($request->englishremarks) ? $request->englishremarks : NULL;
		$Remarks->status = isset($request->status) ? $request->status : "2";
		$result = $Remarks->save();  // save data
		if($result){
			return redirect('remarks')->with('success_msg', 'Remarks added successfully.');
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
			$Remarks = Remarks::find($id)->toArray();
			return view('Remarks.remarks_edit',compact('Remarks'));
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
		$Remarks = Remarks::find($id);
		$Remarks->remarks_ch = !empty($request->chineseremarks) ? $request->chineseremarks : NULL;
		$Remarks->remarks_en = !empty($request->englishremarks) ? $request->englishremarks : NULL;
		$Remarks->status = isset($request->status) ? $request->status : "2";
		$result = $Remarks->save();  // save data
		if($result){
			return redirect('remarks')->with('success_msg', 'Remarks updated successfully.');
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
		$Remarks = Remarks::where('id',$id)->delete();
		if($Remarks){
			$message = 'Remarks deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}	
}
