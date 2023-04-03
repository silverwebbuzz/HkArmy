<?php

namespace App\Http\Controllers;

use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\ProductAssignModel;
use App\Http\Models\ProductModel;
use App\Http\Models\User;
use App\Http\Models\AssignProductOrder;
use DB;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\View;

class ProductReportController extends Controller {

/**
 ** USE : ASSIGN EVENT USER REPORT
 **/
	public function remarksUpdate(Request $request) {

		if (isset($request->type) && $request->type == "all") {
			if ($request->id) {
				$update = ProductAssignModel::whereIn('id', $request->id)->update(['remark' => $request->remark]);
			}
		} else {
			if ($request->id) {
				$update = ProductAssignModel::where('id', $request->id)->update(['remark' => $request->remark]);
			}
		}

		$EventList = ProductAssignModel::where('product_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {

			//$user = explode(",", $event['event_assign_user']);
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = ProductAssignModel::with('users')->where('product_id', $request->event_id)->get()->toArray();

			$html = '';

			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_product_assign_status' data-product-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= "<option data-id='' value='0'>Not Confirm</option>";

			$html .= "<option data-id='' value='1'>Confirm</option>";

			$html .= "</select>";
			$html .= "</div>";

			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 product_assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.Remarks.Remarks') . '</th>
		<th>' . __('languages.Status') . '</th>
		<th>' . __('languages.Action') . '</th>
		</tr>
		</thead>
		<tbody>';
			if (!empty($member)) {
				foreach ($member as $key => $val) {
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="selectMember" value="' . $val['id'] . '" class="checkBoxClass" id="Checkbox' . $val['id'] . '" /></td>';
					$html .= '<td>' . ($key + 1) . '</td> <td>C' . $val['users']['MemberCode'] . '</td> <td>' . $val['users']['English_name'] . '</td>';
					$html .= '<td>
					<span  id="remarkEdit' . $val['id'] . '" style="display:none;"><input type="text" class="remarkinput" data-id="' . $val['id'] . '" value="' . $val['remark'] . '" />
					</span>
					<span  class="remarkClick" data-id="' . $val['id'] . '" id="remarkShow' . $val['id'] . '">';
					if (isset($val['remark'])) {
						$html .= $val['remark'];
					} else {
						$html .= '-';
					}
					$html .= '</span></td>';
					$html .= '<td><select class="form-control product_assign_status_change" data-product-id=' . $val['product_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>Not Confirm</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>Confirm</option>';

					$html .= '</select></td>';
					$html .= '<td><a href="javascript:void(0);" data-product-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteproductAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';
		}
		return response()->json(['status' => 'true', 'html' => $html, 'message' => 'Remark Added Successfully']);
	}

	public function assignUserReport(Request $request) {
		$enrollmentOrderList = AssignProductOrder::with('ProductCostType')->with('product')->with('childProducts')->orderBy('id','desc')->get();
		return view('ProductReportManagement.assign_product_user_report', compact('enrollmentOrderList'));
	}

	/**
	 ** USE : GET USER LIST FOR EVENT ASSIGN IN REPORT
	**/
	public function ajaxGetUserList(Request $request) {
		if ($request->event_id) {
			/*$EventList = Events::where('id',$request->event_id)->whereNotNull('event_assign_user')->first();*/
			$EventList = ProductAssignModel::where('product_id', $request->event_id)->get()->toArray();
			$member = [];
			if (!empty($EventList)) {
				$user = array_column($EventList, "user_id");
				//$user = explode(",", $event['event_assign_user']);
				$member = User::where('Role_ID', '2')->where('Status', '1')->whereNotIn('ID', $user)->get()->toArray();
			} else {
				$member = User::where('Role_ID', '2')->where('Status', '1')->get()->toArray();
			}
			return response()->json(['status' => true, 'member' => $member]);

		}
	}

/**
 ** USE : MEMBER ASSIGN
 **/
	public function assignMember(Request $request) {

		$eventAssign = ProductAssignModel::where('user_id', $request->memberID)->where('product_id', $request->eventID)->first();
		if (empty($eventAssign)) {
			$assign = new ProductAssignModel;
			$assign->product_id = !empty($request->eventID) ? $request->eventID : NULL;
			$assign->user_id = !empty($request->memberID) ? $request->memberID : NULL;
			$assign->save();
		}

		Session::flash('success_msg', 'Assign User successfully.');
		$status = true;

		return response()->json(['status' => $status]);

	}

/**
 ** USE : MEMBER ASSIGN FROM VIEW
 **/
	public function assignMemberFromView(Request $request) {
		$eventAssign = ProductAssignModel::where('user_id', $request->memberID)->where('product_id', $request->eventID)->first();
		if (empty($eventAssign)) {
			$assign = new ProductAssignModel;
			$assign->product_id = !empty($request->eventID) ? $request->eventID : NULL;
			$assign->user_id = !empty($request->memberID) ? $request->memberID : NULL;
			$assign->save();
		}

		$EventList = ProductAssignModel::where('product_id', $request->eventID)->get()->toArray();
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			$member = ProductAssignModel::with('users')->with('ProductCosttypeModel')->where('product_id', $request->eventID)->get()->toArray();
			$html = '';
			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_product_assign_status' data-product-id=''>";
			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';
			$html .= "<option data-id='' value='0'>Not Confirm</option>";
			$html .= "<option data-id='' value='1'>Confirm</option>";
			$html .= "</select>";
			$html .= "</div>";

			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 product_assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.cost_method') . '</th>
		<th>' . __('languages.Remarks.Remarks') . '</th>
		<th>' . __('languages.Status') . '</th>
		<th>' . __('languages.Action') . '</th>
		</tr>
		</thead>
		<tbody>';
			if (!empty($member)) {
				foreach ($member as $key => $val) {
					$html .= '<tr>';
						$html .= '<td>';
							$html .= '<input type="checkbox" name="selectMember" value="' . $val['id'] . '" class="checkBoxClass" id="Checkbox' . $val['id'] . '" />';
						$html .= '</td>';
						$html .= '<td>' . ($key + 1) . '</td>';
						$html .= '<td>C' . $val['users']['MemberCode'] . '</td>';
						$html .= '<td>' . $val['users']['English_name'] . '</td>';
						$costMehod = '---';
						if(isset($val['product_costtype_model']) && !empty($val['product_costtype_model'])){
							if($val['product_costtype_model']['cost_type'] == 1){  // If Cost Type is "Money"
								$costMehod = 'Money : '.$val['product_costtype_model']['cost_value'];
							}else if($val['product_costtype_model']['cost_type'] == 2){ // If Cost Type is "Token"
								$costMehod = 'Token : '.$val['product_costtype_model']['cost_value'];
							}else if($val['product_costtype_model']['cost_type'] == 3){  // If Cost Type is "Money + Token"
								$costMehod = 'Money + Token : '.$val['product_costtype_model']['cost_value'];
							}
						}
						$html .= '<td>' . $costMehod . '</td>';
						$html .= '<td><span  id="remarkEdit' . $val['id'] . '" style="display:none;"><input type="text" class="remarkinput" data-id="' . $val['id'] . '" value="' . $val['remark'] . '" /></span><span  class="remarkClick" data-id="' . $val['id'] . '" id="remarkShow' . $val['id'] . '">';
							if (isset($val['remark'])) {
								$html .= $val['remark'];
							} else {
								$html .= '-';
							}
						$html .= '</span></td>';

						$html .= '<td><select class="form-control product_assign_status_change" data-product-id=' . $val['product_id'] . '>';
							$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';
							$html .= '<option data-id="' . $val['id'] . '" value="0"';
							if ($val['status'] == '0') {
								$html .= ' selected ';
							}
							$html .= '>Not Confirm</option>';
							$html .= '<option data-id="' . $val['id'] . '" value="1"';
							if ($val['status'] == '1') {
								$html .= ' selected ';
							}
							$html .= '>Confirm</option>';
						$html .= '</select></td>';
						$html .= '<td><a href="javascript:void(0);" data-product-id=' . $request->eventID . ' data-id="' . $val['users']['ID'] . '" class="deleteproductAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';

			$EventList = ProductAssignModel::where('product_id', $request->eventID)->get()->toArray();
			$member = [];
			if (!empty($EventList)) {
				$user = array_column($EventList, "user_id");
				//$user = explode(",", $event['event_assign_user']);
				$member = User::where('Role_ID', '2')->where('Status', '1')->whereNotIn('ID', $user)->get()->toArray();
			} else {
				$member = User::where('Role_ID', '2')->where('Status', '1')->get()->toArray();
			}

			return response()->json(['status' => true, 'html' => $html, 'member' => $member, 'message' => 'Assign product to user successfully']);

		} else {
			return response()->json(['status' => false, 'message' => 'Please try again']);
		}

	}

	/** USE : GET MEMBER LIST OF EVENT**/
	public function getEnrollmenetProductAssignedMembers(Request $request){
		$html = '';
		if(isset($request->enrollmentid) && !empty($request->enrollmentid)){
			$EnrollmentOrder = AssignProductOrder::with('ProductCostType')
								->with('childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users')
								->where('id',$request->enrollmentid)
								->first();
			$html = (string)View::make('ProductReportManagement.enrollment_order_member_list',compact('EnrollmentOrder'));
			return response()->json(['status' => true, 'html' => $html]);
		}
	}

	public function deleteAssignMember(Request $request) {
		$deleteEventMember = ProductAssignModel::where('id', $request->assignProductId)->delete();
		$html = '';
		if(isset($request->enrolmentOrderId) && !empty($request->enrolmentOrderId)){
			$EnrollmentOrder = AssignProductOrder::with('ProductCostType')
								->with('product','product.childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users')
								->where('id',$request->enrolmentOrderId)
								->first();
			$html = (string)View::make('ProductReportManagement.enrollment_order_member_list',compact('EnrollmentOrder'));
		}
		return response()->json(['status' => true, 'html' => $html, 'message' => 'User delete successfully']);
	}

	public function getEventFromType(Request $request) {
		/*	$EventList1 = Events::where('event_type',$request->event_type_val)->where('status','1')->get();*/
		$Select_db = DB::table('event_schedule')->select('event_schedule.id as scheduleID', 'event_schedule.event_id', 'event_schedule.start_time as scheduleStartTime', 'event_schedule.end_time as scheduleEndTime', 'event_schedule.event_hours as scheduleEventHours', 'events.*', 'event_type.*')
			->join('events', 'events.id', 'event_schedule.event_id')
			->join('event_type', 'event_type.id', 'events.event_type')
			->where('event_schedule.status', 1);
		if (!empty($request->filter_date_attendance_event)) {
			$search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE `date` = '" . $request->filter_date_attendance_event . "' AND status = 1 GROUP BY event_code"));
			$ids = [];
			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'id');
				$Select_db->whereIn('event_schedule.id', $ids);
			} else {
				$Select_db->whereIn('event_schedule.id', $ids);
			}
		}
		if (!empty($request->event_type_val)) {
			$Select_db->where('events.event_type', $request->event_type_val);
		}
		$Select_db->where('event_schedule.status', 1);
		$EventList1 = $Select_db->groupBy('event_schedule.event_code')
			->get();

		return response()->json($EventList1);
	}

	public function productAssignChangeStatusUpdate($id, Request $request) {
		$eventStatus = ProductAssignModel::find($id);
		$eventStatus->status = $request->status;
		$result = $eventStatus->save();
		if ($result) {
			$message = 'Status Updated successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		$html = '';
		if(isset($request->enrolmentOrderId) && !empty($request->enrolmentOrderId)){
			$EnrollmentOrder = AssignProductOrder::with('ProductCostType')
								->with('product','product.childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users')->where('id',$request->enrolmentOrderId)->first();
								//echo '<pre>';print_r($EnrollmentOrder->toArray());die;
			$html = (string)View::make('ProductReportManagement.enrollment_order_member_list',compact('EnrollmentOrder'));
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);
	}

	public function selectproductAssignStatusUpdate(Request $request) {
		$result = ProductAssignModel::where('assign_product_order_id', $request->enrollmentorderid)
					->whereIn('user_id',$request->member_ids)
					->update(['status' => $request->status]);
		if ($result) {
			$message = 'Status Updated successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		$html = '';
		if(isset($request->enrollmentorderid) && !empty($request->enrollmentorderid)){
			$EnrollmentOrder = AssignProductOrder::with('ProductCostType')
								->with('product','product.childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users')->where('id',$request->enrollmentorderid)->first();
			$html = (string)View::make('ProductReportManagement.enrollment_order_member_list',compact('EnrollmentOrder'));
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);
	}

	public function deleteAllMembersProductAssigned(Request $request){
		$result = ProductAssignModel::where('assign_product_order_id', $request->enrollmentorderid)->whereIn('user_id',$request->member_ids)->delete();
		if ($result) {
			$message = 'Members deleted successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		$html = '';
		if(isset($request->enrollmentorderid) && !empty($request->enrollmentorderid)){
			$EnrollmentOrder = AssignProductOrder::with('ProductCostType')
								->with('product','product.childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users')->where('id',$request->enrollmentorderid)->first();
			$html = (string)View::make('ProductReportManagement.enrollment_order_member_list',compact('EnrollmentOrder'));
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);
	}

	public function deleteEnrollmentProduct(Request $request){
		$recordDelete = AssignProductOrder::where('id',$request->id)->delete();
		if($recordDelete){
			return response()->json(['status' => true,  'message' => 'Enroll Product delete successfully']);
		}else{
			return response()->json(['status' => false, 'message' => 'Please try again']);
		}
	}
}