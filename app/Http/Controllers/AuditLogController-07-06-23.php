<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\User;
use App\Http\Models\AuditLog;
use DB;
use Session;
use App\Helpers\Helper;

class AuditLogController extends Controller
{
	
	public function index(Request $request){
		if(in_array('settings_read', Helper::module_permission(Session::get('user')['role_id']))){
			$auditlog = AuditLog::with('users')->orderBy('id','desc')->get()->toArray();
			foreach ($auditlog as $key => $value) {
				$id = $value['Log_id'];
				$tablename  = $value['table_name'];
				if($tablename == 'users'){
					$ID = 'ID';
				}else{
					$ID = 'id';
				}
				$data = DB::table($tablename)->where($ID,$id)->first();
			}
			return view('auditlog.auditlog_list',compact('auditlog'));
		}else{
			return redirect('/');
		}
	}

	public function show($id){
		if(in_array('settings_write', Helper::module_permission(Session::get('user')['role_id']))){
			$auditlog = AuditLog::where('id',$id)->first();
			return view('auditlog.auditlog_view',compact('auditlog'));
		}else{
			return redirect('/');
		}
	}

	public function audtilogDelete($id)
	{
		$AuditLog = AuditLog::where('id',$id)->delete();
		if($AuditLog){
			$message = 'Audit Log deleted successfully..';
			$status = true;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message]);
	}

	
}