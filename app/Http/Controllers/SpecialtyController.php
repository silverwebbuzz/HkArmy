<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Specialty;
use Session;
use App\Helpers\Helper;

class SpecialtyController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$Specialtys = Specialty::orderBy('id','desc')->get()->toArray();
			return view('Specialty.specialty_list',compact('Specialtys'));
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
			return view('Specialty.specialty_add');
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
		$Specialty = new Specialty;
		$Specialty->specialty_ch = !empty($request->chinesespecialty) ? $request->chinesespecialty : NULL;
		$Specialty->specialty_en = !empty($request->englishspecialty) ? $request->englishspecialty : NULL;
		$Specialty->status = isset($request->status) ? $request->status : "2";
		$result = $Specialty->save();  // save data
		if($result){
			return redirect('specialty')->with('success_msg', 'Specialty added successfully.');
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
			$Specialty = Specialty::find($id)->toArray();
			return view('Specialty.specialty_edit',compact('Specialty'));
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
		$Specialty = Specialty::find($id);
		$Specialty->specialty_ch = !empty($request->chinesespecialty) ? $request->chinesespecialty : NULL;
		$Specialty->specialty_en = !empty($request->englishspecialty) ? $request->englishspecialty : NULL;
		$Specialty->status = isset($request->status) ? $request->status : "2";
		$result = $Specialty->save();  // save data
		if($result){
			return redirect('specialty')->with('success_msg', 'Specialty Updated successfully.');
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
		$Specialty = Specialty::where('id',$id)->delete();
		if($Specialty){
			$message = 'Specialty deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}
}
