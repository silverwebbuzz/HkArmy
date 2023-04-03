<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\RelatedActivityHistory;
use Session;
use App\Helpers\Helper;

class RelatedActivityController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$RelatedActivityHistory = RelatedActivityHistory::orderBy('id','desc')->get()->toArray();
			return view('RelatedActivityHistory.relatedactivity_list',compact('RelatedActivityHistory'));
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
			return view('RelatedActivityHistory.relatedactivity_create');
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
		$RelatedActivityHistory = new RelatedActivityHistory;
		$RelatedActivityHistory->ActivityHistory_ch = !empty($request->chineserelatedhistroy) ? $request->chineserelatedhistroy : NULL;
		$RelatedActivityHistory->ActivityHistory_en = !empty($request->englishrelatedhistroy) ? $request->englishrelatedhistroy : NULL;
		$RelatedActivityHistory->status = isset($request->status) ? $request->status : "2";
		$result = $RelatedActivityHistory->save();  // save data
		if($result){
			return redirect('related-activity-history')->with('success_msg', 'Related Activity History added successfully.');
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
			$RelatedActivityHistory = RelatedActivityHistory::find($id)->toArray();
			return view('RelatedActivityHistory.relatedactivity_edit',compact('RelatedActivityHistory'));
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
		$RelatedActivityHistory = RelatedActivityHistory::find($id);
		$RelatedActivityHistory->ActivityHistory_ch = !empty($request->chineserelatedhistroy) ? $request->chineserelatedhistroy : NULL;
		$RelatedActivityHistory->ActivityHistory_en = !empty($request->englishrelatedhistroy) ? $request->englishrelatedhistroy : NULL;
		$RelatedActivityHistory->status = isset($request->status) ? $request->status : "2";
		$result = $RelatedActivityHistory->save();  // save data
		if($result){
			return redirect('related-activity-history')->with('success_msg', 'Related Activity History updated successfully.');
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
		$RelatedActivityHistory = RelatedActivityHistory::where('id',$id)->delete();
		if($RelatedActivityHistory){
			$message = 'Related Activity History deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}
}
