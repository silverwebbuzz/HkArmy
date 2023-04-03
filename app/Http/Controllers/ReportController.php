<?php

namespace App\Http\Controllers;

use App\Http\Models\EventAssignModel;
use App\Http\Models\EventPosttypeModel;
use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\User;
use DB;
use Illuminate\Http\Request;
use Session;

class ReportController extends Controller {

/**
 ** USE : ASSIGN EVENT USER REPORT
 **/
	public function remarksUpdate(Request $request) {

		if (isset($request->type) && $request->type == "all") {
			if ($request->id) {
				$update = EventAssignModel::whereIn('id', $request->id)->update(['remark' => $request->remark]);
			}
		} else {
			if ($request->id) {
				$update = EventAssignModel::where('id', $request->id)->update(['remark' => $request->remark]);
			}
		}

		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {

			//$user = explode(",", $event['event_assign_user']);
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();

			$html = '';

			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= "<option data-id='' value='0'>Not Confirm</option>";

			$html .= "<option data-id='' value='1'>Confirm</option>";

			$html .= "</select>";
			$html .= "</div>";

			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.event.Cost_Method_Name') . '</th>
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

					$html .= '<td>';
					$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					if(isset($costType) && !empty($costType)){
						foreach ($costType as $key => $costtype) {
							$costValue = EventPosttypeModel::where('id', $costtype['cost_type_id'])->first();
							if(isset($costValue) && !empty($costValue)){
								$costValue = $costValue->toArray();
								if ($costValue['post_type'] == 1) {
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 2) {
									$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 3) {
									$costValue = explode("+", $costValue['post_value']);
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
								}
							}
						}
					}

					$html .= '</td>';

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
					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Not_Confirm') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Confirm') . '</option>';

					$html .= '</select></td>';
					$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';
		}

		return response()->json(['status' => 'true', 'html' => $html, 'message' => 'Remark Added Successfully']);

	}

	/**
	 * USE : List page for event enrollmment order
	 */
	public function assignUserReport(Request $request) {
		$eventTypes = new EventType;
		if (empty($request->filter_event_type)) {
			$get_event_type_list = $eventTypes->get_event_type_select_list();
		}
		$query = Events::query()->with('eventCostType');
		if ($request->filter_event_id) {
			$query->where('id', $request->filter_event_id);
		}
		if ($request->filter_event_type) {
			$get_event_type_list = $eventTypes->get_event_type_select_list($request->filter_event_type);
			$query->where('event_type', $request->filter_event_type);
		}
		if (!empty($request->filter_date_attendance)) {
			//$search_result = EventSchedule::where('date', \Helper::dateFormatMDY('/','-',$request->filter_date_attendance))->where('status', '1')->groupBy('date')->get();
			$search_result = EventSchedule::where('date', \Helper::dateFormatMDY('/','-',$request->filter_date_attendance))->get();
			$ids = [];
			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'event_id');
				$query->whereIn('id', $ids);
			} else {
				$query->whereIn('id', $ids);
			}
		}
		$EventList = $query->where('status', '1')->orderBy('id','desc')->get()->toArray();
		if (!empty($EventList)) {
			$eventUser = [];
			foreach ($EventList as $key => $value) {
				$eventUser[$key]['event_name'] = $value['event_name'];
				$eventUser[$key]['user'] = explode(",", $value['event_assign_user']);
				$eventUserArray = explode(",", $value['event_assign_user']);
				// $scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
				$scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->get()->toArray();
				$dates = [];
				foreach ($scheduleData as $val) {
					$dates[] = $val['date'];
				}
				$EventList[$key]['event_start_date'] = date('d/m/Y', strtotime($dates[0]));
			}
		}
		/*End*/
		$query1 = Events::query();
		if ($request->filter_event_type) {
			$query1->where('event_type', $request->filter_event_type);
		}
		if ($request->filter_date_attendance) {
			//$search_result = EventSchedule::where('date', \Helper::dateFormatMDY('/','-',$request->filter_date_attendance))->where('status', '1')->get();
			$search_result = EventSchedule::where('date', \Helper::dateFormatMDY('/','-',$request->filter_date_attendance))->get();
			$ids = [];
			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'event_id');
				$query1->whereIn('id', $ids);
			} else {
				$query1->whereIn('id', $ids);
			};
		}
		$EventList1 = $query1->where('status', '1')->orderBy('id','desc')->get()->toArray();
		return view('ReportManagement.assign_event_user_report', compact('EventList1', 'EventList', 'get_event_type_list'));
	}

	public function assignUserReportOLD(Request $request) {
		$eventTypes = new EventType;
		if (empty($request->filter_event_type)) {
			$get_event_type_list = $eventTypes->get_event_type_select_list();
		}

		$query = Events::query();

		if ($request->filter_event_id) {
			$query->where('id', $request->filter_event_id);
		}

		if ($request->filter_event_type) {
			$get_event_type_list = $eventTypes->get_event_type_select_list($request->filter_event_type);
			$query->where('event_type', $request->filter_event_type);
		}

		$EventList = $query->where('status', '1')->get()->toArray();

		if (!empty($EventList)) {
			$eventUser = [];
			foreach ($EventList as $key => $value) {
				$eventUser[$key]['event_name'] = $value['event_name'];
				$eventUser[$key]['user'] = explode(",", $value['event_assign_user']);
				$eventUserArray = explode(",", $value['event_assign_user']);
				$scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
				$dates = [];
				foreach ($scheduleData as $val) {
					$dates[] = $val['date'];
				}
				$EventList[$key]['event_start_date'] = date('d/m/Y', strtotime($dates[0]));
			}
		}

		$query1 = Events::query();
		if ($request->filter_event_type) {
			$query1->where('event_type', $request->filter_event_type);
		}
		$EventList1 = $query1->where('status', '1')->get()->toArray();
		return view('ReportManagement.assign_event_user_report', compact('EventList1', 'EventList', 'get_event_type_list'));
	}
/**
 ** USE : GET USER LIST FOR EVENT ASSIGN IN REPORT
 **/
	public function ajaxGetUserList(Request $request) {
		if ($request->event_id) {
			/*$EventList = Events::where('id',$request->event_id)->whereNotNull('event_assign_user')->first();*/
			$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
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

		$eventAssign = EventAssignModel::where('user_id', $request->memberID)->where('event_id', $request->eventID)->first();
		if (empty($eventAssign)) {
			$assign = new EventAssignModel;
			$assign->event_id = !empty($request->eventID) ? $request->eventID : NULL;
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
		$eventAssign = EventAssignModel::where('user_id', $request->memberID)->where('event_id', $request->eventID)->first();
		if (empty($eventAssign)) {
			$assign = new EventAssignModel;
			$assign->event_id = !empty($request->eventID) ? $request->eventID : NULL;
			$assign->user_id = !empty($request->memberID) ? $request->memberID : NULL;
			$assign->save();
		}

		$EventList = EventAssignModel::where('event_id', $request->eventID)->get()->toArray();
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = EventAssignModel::with('users')->where('event_id', $request->eventID)->get()->toArray();

			$html = '';

			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= "<option data-id='' value='0'>Not Confirm</option>";

			$html .= "<option data-id='' value='1'>Confirm</option>";

			$html .= "</select>";
			$html .= "</div>";

			$html .= '<div class="remarkdiv"><input class="form-control" type="text" name="selectRemark" id="selectRemark" placeholder=' . __('languages.member.Add Notes') . '>';
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.event.Cost_Method_Name') . '</th>
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

					$html .= '<td>';
					$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					if(isset($costType) && !empty($costType)){
						foreach ($costType as $key => $costtype) {
							$costValue = EventPosttypeModel::where('id', $costtype['cost_type_id'])->first();
							if(!empty($costValue)){
								$costValue = $costValue->toArray();
								if ($costValue['post_type'] == 1) {
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 2) {
									$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 3) {
									$costValue = explode("+", $costValue['post_value']);
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
								}
							}
						}
					}

					$html .= '</td>';

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

					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

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

					$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->eventID . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';

			$EventList = EventAssignModel::where('event_id', $request->eventID)->get()->toArray();
			$member = [];
			if (!empty($EventList)) {
				$user = array_column($EventList, "user_id");
				//$user = explode(",", $event['event_assign_user']);
				$member = User::where('Role_ID', '2')->where('Status', '1')->whereNotIn('ID', $user)->get()->toArray();
			} else {
				$member = User::where('Role_ID', '2')->where('Status', '1')->get()->toArray();
			}

			return response()->json(['status' => true, 'html' => $html, 'member' => $member, 'message' => 'Assign event to user successfully']);

		} else {
			return response()->json(['status' => false, 'message' => 'Please try again']);
		}

	}

/** USE : GET MEMBER LIST OF EVENT**/
	public function getEventMember(Request $request) {
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {

			//$user = explode(",", $event['event_assign_user']);
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();

			$html = '';
			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= "<option data-id='' value='0'>Not Confirm</option>";

			$html .= "<option data-id='' value='1'>Confirm</option>";

			$html .= "</select>";
			$html .= "</div>";

			$html .= '<div class="remarkdiv"><input class="form-control" type="text" name="selectRemark" id="selectRemark" placeholder=' . __('languages.member.Add Notes') . '>';
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";
			$html .= '<div class="ml-1 event-enrollment-section">
							<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-assign-member-report mb-0">'.__('languages.export_assign_user_report').'</a>
							<button type="button" class="btn btn-primary ml-1 remove-assigned-event-members">
								<i class="bx bx-check d-block d-sm-none"></i>
								<span class="d-none d-sm-block">'.__("languages.delete_member").'</span>
							</button>
						</div>';

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" class="select-all-view-member-chkbox" id="ckbCheckAll" value="all" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>';
		// <th>' . __('languages.member.Member_Name') . '</th>
		$html .= '<th>' . __('languages.member.English_name') . '</th>
		<th>' . __('languages.member.Chinese_name') . '</th>
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
					$html .= '<td><input type="checkbox" name="selectMember" value="' . $val['id'] . '" class="checkBoxClass select-view-member-chkbox" id="Checkbox' . $val['id'] . '" /></td>';
					$html .= '<td>'.($key + 1).'</td>';
					$html .= '<td>C' . $val['users']['MemberCode'] . '</td>';
					$html .= '<td>' . $val['users']['English_name'] . '</td>';
					$html .= '<td>' . $val['users']['Chinese_name'] . '</td>';
					// $html .= '<td>';
					// 	if(isset($val['users']['Chinese_name'])){
					// 		$html .= $val['users']['Chinese_name']." & ";
					// 	}
					// 	if(isset($val['users']['English_name'])){
					// 		$html .= $val['users']['English_name'];
					// 	}
					// $html .= '</td>';

					$html .= '<td>';
					$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					if(isset($costType) && !empty($costType)){
						foreach ($costType as $key => $costtype) {
							$costValue = EventPosttypeModel::where('id', $costtype['cost_type_id'])->first();
							if(isset($costValue) && !empty($costValue)){
								$costValue = $costValue->toArray();
								if ($costValue['post_type'] == 1) {
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 2) {
									$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue['post_value'];
								} else if ($costValue['post_type'] == 3) {
									$costValue = explode("+", $costValue['post_value']);
									$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
								}
							}
						}
					}
					$html .= '</td>';

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

					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Not_Confirm') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Confirm') . '</option>';

					$html .= '</select></td>';
					$html .= '<td>
				<a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';

		}
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$member = [];
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			//$user = explode(",", $event['event_assign_user']);
			$member = User::where('Role_ID', '2')->where('Status', '1')->whereNotIn('ID', $user)->get()->toArray();
		} else {
			$member = User::where('Role_ID', '2')->where('Status', '1')->get()->toArray();
		}

		return response()->json(['status' => true, 'html' => $html, 'member' => $member]);
	}

	public function deleteAssignMember(Request $request) {
		$deleteEventMember = EventAssignModel::where('event_id', $request->event_id)->where('user_id', $request->user_id)->delete();
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();
			$html = '';

			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= '<option data-id="" value="0">' . __('languages.Not_Confirm') . '</option>';

			$html .= '<option data-id="" value="1">' . __('languages.Confirm') . '</option>';

			$html .= "</select>";
			$html .= "</div>";

			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
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
					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Not_Confirm') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Confirm') . '</option>';

					$html .= '</select></td>';
					$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';

			$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
			$member = [];
			if (!empty($EventList)) {
				$user = array_column($EventList, "user_id");
				//$user = explode(",", $event['event_assign_user']);
				$member = User::where('Role_ID', '2')->where('Status', '1')->whereNotIn('ID', $user)->get()->toArray();
			} else {
				$member = User::where('Role_ID', '2')->where('Status', '1')->get()->toArray();
			}

			return response()->json(['status' => true, 'html' => $html, 'member' => $member, 'message' => 'User delete successfully']);
		}

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

	public function eventAssignStatusUpdate($id, Request $request) {

		$eventStatus = EventAssignModel::find($id);
		$eventStatus->status = $request->status;
		$result = $eventStatus->save();
		if ($result) {
			$message = 'Status Updated successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {

			//$user = explode(",", $event['event_assign_user']);
			$user = array_column($EventList, "user_id");
			/*$member = User::where('Role_ID','2')->where('Status','1')->whereIn('ID',$user)->get()->toArray();*/
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();

			$html = '';

			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";

			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

			$html .= '<option data-id="" value="0">' . __('languages.Not_Confirm') . '</option>';

			$html .= '<option data-id="" value="1">' . __('languages.Confirm') . '</option>';

			$html .= "</select>";
			$html .= "</div>";

			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.event.Cost_Method_Name') . '</th>
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

					$html .= '<td>';
					// $costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					// foreach ($costType as $key => $costtype) {
					// 	$costValue = EventPosttypeModel::where('event_id', $costtype['event_id'])->where('post_type', $costtype['cost_type'])->get()->first();
					// 	if ($costtype['cost_type'] == 1) {
					// 		$html .= '' . __('languages.event.Money') . ' - ' . $costValue->post_value;
					// 	} else if ($costtype['cost_type'] == 2) {
					// 		$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue->post_value;
					// 	} else if ($costtype['cost_type'] == 3) {
					// 		$costValue = explode("+", $costValue->post_value);
					// 		$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
					// 	}
					// }

					$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					if(isset($costType) && !empty($costType)){
						foreach ($costType as $key => $costtype) {
							$costValue = EventPosttypeModel::where('id', $costtype['cost_type_id'])->first()->toArray();
							if ($costValue['post_type'] == 1) {
								$html .= '' . __('languages.event.Money') . ' - ' . $costValue['post_value'];
							} else if ($costValue['post_type'] == 2) {
								$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue['post_value'];
							} else if ($costValue['post_type'] == 3) {
								$costValue = explode("+", $costValue['post_value']);
								$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
							}
						}
					}


					$html .= '</td>';

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
					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Not_Confirm') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Confirm') . '</option>';

					$html .= '</select></td>';
					$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);

	}

	/**
	 * USE : Update event enrollment oder members status
	 */
	public function selecteventAssignStatusUpdate(Request $request) {
		$result = EventAssignModel::whereIn('id', $request->id)->update(['status' => $request->status]);
		if ($result) {
			$message = __('languages.status_update_successfully');
			$status = true;
		} else {
			$message = __('languages.problem_was_error_accured');
			$status = false;
		}
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();
			$html = '';
			$html .= "<div class='statusremarkdiv'>";
			$html .= "<div class='Mstatusdiv'>";
			$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";
			$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';
			$html .= "<option data-id='' value='0'>Not Confirm</option>";
			$html .= "<option data-id='' value='1'>Confirm</option>";
			$html .= "</select>";
			$html .= "</div>";
			$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
			$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
			$html .= "</div>";
			$html .= '<table id="assignMemberList" class="table">
		<thead>
		<tr>
		<th><input type="checkbox" id="ckbCheckAll" /></th>
		<th>' . __('languages.RoleManagement.Sr_No') . '</th>
		<th>' . __('languages.member.Member_Number') . '</th>
		<th>' . __('languages.member.Member_Name') . '</th>
		<th>' . __('languages.event.Cost_Method_Name') . '</th>
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

					$html .= '<td>';
					$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
					foreach ($costType as $key => $costtype) {
						$costValue = EventPosttypeModel::where('event_id', $costtype['event_id'])->where('post_type', $costtype['cost_type'])->get()->first();
						if ($costtype['cost_type'] == 1) {
							$html .= '' . __('languages.event.Money') . ' - ' . $costValue->post_value;
						} else if ($costtype['cost_type'] == 2) {
							$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue->post_value;
						} else if ($costtype['cost_type'] == 3) {
							$costValue = explode("+", $costValue->post_value);
							$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
						}

					}

					$html .= '</td>';

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
					$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';

					$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="0"';
					if ($val['status'] == '0') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Not_Confirm') . '</option>';

					$html .= '<option data-id="' . $val['id'] . '" value="1"';
					if ($val['status'] == '1') {
						$html .= ' selected ';
					}
					$html .= '>' . __('languages.Confirm') . '</option>';

					$html .= '</select></td>';
					$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
					$html .= '</tr>';
				}
			}
			$html .= '</tbody></table>';
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);
	}

	/**
	 * USE : Update event enrollment oder members status
	 */
	public function deleteEnrollmentEventMember(Request $request) {
		$result = EventAssignModel::whereIn('id', $request->recordIds)->where('event_id',$request->event_id)->delete();
		if ($result) {
			$message = __('languages.members_deleted_successfully');
			$status = true;
		} else {
			$message = __('languages.problem_was_error_accured');
			$status = false;
		}
		$EventList = EventAssignModel::where('event_id', $request->event_id)->get()->toArray();
		$html = '';
		if (!empty($EventList)) {
			$user = array_column($EventList, "user_id");
			$member = EventAssignModel::with('users')->where('event_id', $request->event_id)->get()->toArray();
			$html = '';
			$html .= "<div class='statusremarkdiv'>";
				$html .= "<div class='Mstatusdiv'>";
					$html .= "<select class='form-control select_event_assign_status' data-event-id=''>";
						$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';
						$html .= "<option data-id='' value='0'>Not Confirm</option>";
						$html .= "<option data-id='' value='1'>Confirm</option>";
					$html .= "</select>";
				$html .= "</div>";

				$html .= "<div class='remarkdiv'><input class='form-control' type='text' name='selectRemark' id='selectRemark' placeholder='Add Remark'>";
					$html .= '<button type="button" class="btn btn-primary ml-1 assign_add_remarks"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">' . __('languages.Save') . '</span></button></div>';
				$html .= "</div>";

			$html .= '<table id="assignMemberList" class="table">
						<thead>
							<tr>
								<th><input type="checkbox" id="ckbCheckAll" /></th>
								<th>' . __('languages.RoleManagement.Sr_No') . '</th>
								<th>' . __('languages.member.Member_Number') . '</th>
								<th>' . __('languages.member.Member_Name') . '</th>
								<th>' . __('languages.event.Cost_Method_Name') . '</th>
								<th>' . __('languages.Remarks.Remarks') . '</th>
								<th>' . __('languages.Status') . '</th>
								<th>' . __('languages.Action') . '</th>
							</tr>
						</thead>
						<tbody>';
						if(!empty($member)){
							foreach ($member as $key => $val) {
								$html .= '<tr>';
									$html .= '<td><input type="checkbox" name="selectMember" value="' . $val['id'] . '" class="checkBoxClass" id="Checkbox' . $val['id'] . '" /></td>';
									$html .= '<td>' . ($key + 1) . '</td> <td>C' . $val['users']['MemberCode'] . '</td> <td>' . $val['users']['English_name'] . '</td>';
									$html .= '<td>';
									$costType = EventAssignModel::where('event_id', $val['event_id'])->where('user_id', $val['user_id'])->get()->toArray();
									foreach ($costType as $key => $costtype) {
										$costValue = EventPosttypeModel::where('event_id', $costtype['event_id'])->where('post_type', $costtype['cost_type'])->get()->first();
										if ($costtype['cost_type'] == 1) {
											$html .= '' . __('languages.event.Money') . ' - ' . $costValue->post_value;
										} else if ($costtype['cost_type'] == 2) {
											$html .= '' . __('languages.event.Tokens') . ' - ' . $costValue->post_value;
										} else if ($costtype['cost_type'] == 3) {
											$costValue = explode("+", $costValue->post_value);
											$html .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
										}
									}
									$html .= '</td>';
									$html .= '<td>
												<span  id="remarkEdit' . $val['id'] . '" style="display:none;"><input type="text" class="remarkinput" data-id="' . $val['id'] . '" value="' . $val['remark'] . '" /></span>
												<span  class="remarkClick" data-id="' . $val['id'] . '" id="remarkShow' . $val['id'] . '">';
												if (isset($val['remark'])) {
													$html .= $val['remark'];
												} else {
													$html .= '-';
												}
												$html .= '</span></td>';
									$html .= '<td><select class="form-control event_assign_status" data-event-id=' . $val['event_id'] . '>';
										$html .= '<option value="">' . __('languages.event.Select_status') . '</option>';
										$html .= '<option data-id="' . $val['id'] . '" value="0"';
										if ($val['status'] == '0') {
											$html .= ' selected ';
										}
										$html .= '>' . __('languages.Not_Confirm') . '</option>';
										$html .= '<option data-id="' . $val['id'] . '" value="1"';
										if ($val['status'] == '1') {
											$html .= ' selected ';
										}
										$html .= '>' . __('languages.Confirm') . '</option>';
										$html .= '</select></td>';
									$html .= '<td><a href="javascript:void(0);" data-event-id=' . $request->event_id . ' data-id="' . $val['users']['ID'] . '" class="deleteAssignMember"><i class="bx bx-trash-alt"></i></a></td>';
								$html .= '</tr>';
							}
						}
						$html .= '</tbody></table>';
		}
		return response()->json(['status' => $status, 'html' => $html, 'message' => $message]);
	}

}