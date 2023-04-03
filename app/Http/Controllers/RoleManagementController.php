<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Role;
use App\Http\Models\Module;
use Session;
use App\Helpers\Helper;

class RoleManagementController extends Controller
{

	public function __construct(){
		$this->Role = new Role;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if(in_array('role_management_read', Helper::module_permission(Session::get('user')['role_id']))){
			$roleList = $this->Role->getRoles();
			return view('RoleManagement.role_list',compact('roleList'));
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
		if(in_array('role_management_create', Helper::module_permission(Session::get('user')['role_id']))){
			$modules = Module::where('status','1')->get();
			return view('RoleManagement.role_add',compact('modules'));
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
		$role_add = new Role;
		$finalDatapermission = json_encode($request->permissions);
		$role_add->role_name = $request->rolename;
		$role_add->description = $request->desc;
		$role_add->permission = $finalDatapermission;
		$role_add->status = isset($request->status) ? $request->status : "1";
		$result = $role_add->save();  // save data
		if($result){
			return redirect('roleManagement')->with('success_msg', 'Role added successfully.');
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
		if(in_array('role_management_write', Helper::module_permission(Session::get('user')['role_id']))){
			$role = Role::find($id);
			$modules = Module::where('status','1')->get();
			return view('RoleManagement.role_edit')->with(array('role'=>$role,'modules'=>$modules));
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
		$role_add = Role::find($id);
		$finalDatapermission = json_encode($request->permissions);
		$role_add->role_name = $request->rolename;
		$role_add->description = $request->desc;
		$role_add->permission = $finalDatapermission;
		$role_add->status = isset($request->status) ? $request->status : "1";
		$result = $role_add->save();  // save data
		if($result){
			return redirect('roleManagement')->with('success_msg', 'Role updated successfully.');
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
		$Role = Role::find($id)->delete();
		if($Role){
			Session::flash('success_msg', 'Role delete successfully!');
			$status = true;
		}else{
			Session::flash('error_msg', 'Problem was occured.Please try again.....');
			$status = false;
		}
		return response()->json(['status' => $status]);
	}
}
