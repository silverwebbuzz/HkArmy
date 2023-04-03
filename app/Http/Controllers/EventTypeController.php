<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\EventType;
use Session;
use App\Helpers\Helper;


class EventTypeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$eventTypes = EventType::orderBy('id','desc')->get()->toArray();
			return view('EventType.event_type_list',compact('eventTypes'));
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
			return view('EventType.event_type_add');
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
		$EventType = new EventType;
		$EventType->event_type_name_ch = !empty($request->chineseeventtype) ? $request->chineseeventtype : NULL;
		$EventType->event_type_name_en = !empty($request->englisheventtpye) ? $request->englisheventtpye : NULL;
		$EventType->status = isset($request->status) ? $request->status : "2";
		$EventType->type_id = isset($request->type_id) ? $request->type_id : "0";
		$result = $EventType->save();  // save data
		if($result){
			return redirect('event-type')->with('success_msg', 'Event Type added successfully.');
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
			$eventTypes = EventType::find($id)->toArray();
			return view('EventType.event_type_edit',compact('eventTypes'));
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
		$EventType = EventType::find($id);
		$EventType->event_type_name_ch = !empty($request->chineseeventtype) ? $request->chineseeventtype : NULL;
		$EventType->event_type_name_en = !empty($request->englisheventtpye) ? $request->englisheventtpye : NULL;
		$EventType->status = isset($request->status) ? $request->status : "2";
		$EventType->type_id = isset($request->type_id) ? $request->type_id : "0";
		$result = $EventType->save();  // save data
		if($result){
			return redirect('event-type')->with('success_msg', 'Event Type updated successfully.');
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
		$EventType = EventType::where('id',$id)->delete();
		if($EventType){
			$message = 'Event Type deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}
}
