<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Models\Attendance;
use App\Http\Models\EilteModel;
use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\HistoryLog;
use App\Http\Models\MemberTokenStatus;
use App\Http\Models\QualificationModel;
use App\Http\Models\RelatedActivityHistory;
use App\Http\Models\Remarks;
use App\Http\Models\ServiceHourPackage;
use App\Http\Models\Specialty;
use App\Http\Models\SubElite;
use App\Http\Models\Subteam;
use App\Http\Models\User;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductCosttypeModel;
use App\Http\Models\EventPosttypeModel;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;
use App\Http\Models\ChildProduct;
use App\Http\Models\EventTokenManage;

class UserController extends Controller {

	public function __construct() {
		date_default_timezone_set(Config::get('constants.timeZone'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		if(!session()->has('user')){
			return redirect('/login');
		}
		if (in_array('members_read', Helper::module_permission(Session::get('user')['role_id']))) {
			$users = User::where('Role_ID', '2')
						->where('Status', '1')
						//->with('MemberTokenStatus')
						//->with('elite')
						//->with('rank')
						//->with('subteam')
						//->with('Qualification')
						//->with('Remarks')
						->orderBy('id','desc')
						->limit(10)
						->get()
						->toArray();
			$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
			$Specialty = Specialty::where('status', '1')->get()->toArray();
			$Qualification = QualificationModel::where('status', '1')->get()->toArray();
			$Ranks = SubElite::where('status', '1')->groupBy('subelite_' . app()->getLocale())->get()->toArray();
			$Teams = EilteModel::where('status', '1')->get()->toArray();
			$subteams = Subteam::where('status', '1')->get()->toArray();
			return view('MemberManagement.member_list', compact('users', 'RelatedActivityHistory', 'Specialty', 'Qualification', 'Ranks', 'Teams', 'subteams'));
		}
	}

	/**
	 * USE : Check User member type
	 */
	public function checkUserIsMentorTeam(Request $request){
		if(isset($request->user_id)){
			$data = User::where('ID',$request->user_id)->where('team',2)->first();
			if(isset($data) && !empty($data)){
				return true;
			}else{
				return false;
			}

		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		if (in_array('members_create', Helper::module_permission(Session::get('user')['role_id']))) {
			$get_last_unique_number = Helper::getLastRefrenceNumber('User');
			$unique_id = (++$get_last_unique_number);
			$hourpackages = ServiceHourPackage::where('status', '1')->get()->toArray();
			$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
			$Specialty = Specialty::where('status', '1')->get()->toArray();
			$Remarks = Remarks::where('status', '1')->get()->toArray();
			$Qualification = QualificationModel::where('status', '1')->get()->toArray();
			$EilteModel = EilteModel::where('status', '1')->get()->toArray();
			return view('MemberManagement.add_member', compact("unique_id", 'hourpackages', 'RelatedActivityHistory', 'Specialty', 'Remarks', 'Qualification', 'EilteModel'));
		} else {
			return redirect('/');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$rules = array(
			'team' => 'required',
			'team_effiective_date' => 'required',
			'rank_effiective_date' => 'required',
			'Reference_number' => 'required',
			'Gender' => 'required',
			'email' => 'required|email',
			'Contact_number' => 'required',
			'JoinDate' => 'required'
		);
		if(empty($request->Chinese_name) && empty($request->English_name)){
			$rules += array(
				'Chinese_name' => 'required',
				'English_name' => 'required'
			);	
		}
		
		$messages = array(
			'team.required' => 'Please select team.',
			'team_effiective_date.required' => 'Please select effective date.',
			'rank_effiective_date.required' => 'Please select effective date.',
			'Reference_number.required' => 'Please enter reference number.',
			// 'Chinese_name.required' => 'Please enter chinese name.',
			// 'English_name.required' => 'Please enter english name.',
			'Gender.required' => 'Please select gender.',
			'email.required' => 'Please enter email address.',
			'email.email' => "Please enter valid email address.",
			'Contact_number.required' => 'Please enter contact number.',
			'JoinDate.required' => 'Please select join date.',
			//'Remarks.required' => 'Please select remark.',
			// 'hour_point.required' => 'Please enter hour point.',
		);
		if(empty($request->chinese_name) && empty($request->english_name)){
			$messages += array(
				'Chinese_name.required' => 'Please enter chinese name.',
				'English_name.required' => 'Please enter english name.'
			);
		}
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}
		$public_path = 'assets/image';
		$fullImagePath = array();
		if ($request->hasfile('Attachment')) {
			$images = $request->file('Attachment');
			foreach ($images as $val) {
				//$name =  time().$val->getClientOriginalName();
				$name = time() . rand(10, 90) . $val->getClientOriginalName();
				$val->move(public_path($public_path), $name);
				$fullImagePath[] = $public_path . '/' . $name;
			}
		}

		$user = new User;
		$aval = array();
		if (!empty($request->relatedactivity)) {
			foreach ($request->relatedactivity as $key => $value) {
				foreach ($request->data as $key1 => $row) {
					if ($key1 == $value) {
						$aval[$key1] = $row[0];
					}
				}
			}
			$val = serialize($aval);
		}
		$specialty = array();
		if (!empty($request->specialty)) {
			foreach ($request->specialty as $key => $value) {
				foreach ($request->data as $key1 => $row) {
					if ($key1 == $value) {
						$specialty[$key1] = $row[0];
					}
				}
			}
			$specialtys = serialize($specialty);
		}
		$explode_member_code = explode("C", $request->MemberCode);
		$user->MemberCode = !empty($request->MemberCode) ? $explode_member_code[1] : NULL;
		$user->team_effiective_date = !empty($request->team_effiective_date) ? $request->team_effiective_date : NULL;
		$user->team = !empty($request->team) ? $request->team : NULL;
		$user->elite_team = !empty($request->elite_team) ? $request->elite_team : NULL;
		$user->Specialty_Instructor = !empty($request->Specialty_Instructor) ? $request->Specialty_Instructor : NULL;
		$user->Specialty_Instructor_text = !empty($request->Specialty_Instructor_text) ? $request->Specialty_Instructor_text : NULL;
		$user->rank_effiective_date = !empty($request->rank_effiective_date) ? $request->rank_effiective_date : NULL;
		$user->Reference_number = !empty($request->Reference_number) ? $request->Reference_number : NULL;
		$user->rank_team = !empty($request->rank_team) ? $request->rank_team : NULL;
		$user->Chinese_name = !empty($request->Chinese_name) ? $request->Chinese_name : NULL;
		$user->English_name = !empty($request->English_name) ? $request->English_name : NULL;
		$user->DOB = !empty($request->DOB) ? $request->DOB : NULL;
		$user->age = !empty($request->age) ? $request->age : 0;
		$user->Gender = !empty($request->Gender) ? $request->Gender : NULL;
		$user->email = !empty($request->email) ? $request->email : NULL;
		$user->password = !empty($request->email) ? Hash::make($request->email) : NULL;
		$user->Contact_number = !empty($request->Contact_number) ? $request->Contact_number : NULL;
		$user->Contact_number_1 = !empty($request->Contact_number_1) ? $request->Contact_number_1 : NULL;
		$user->Contact_number_2 = !empty($request->Contact_number_2) ? $request->Contact_number_2 : NULL;
		$user->Chinese_address = !empty($request->Chinese_address) ? $request->Chinese_address : NULL;
		$user->English_address = !empty($request->English_address) ? $request->English_address : NULL;
		$user->Nationality = !empty($request->Nationality) ? $request->Nationality : NULL;
		$user->Occupation = !empty($request->Occupation) ? $request->Occupation : NULL;
		$user->ID_Number = !empty($request->ID_Number) ? $request->ID_Number : NULL;
		$user->Qualification = !empty($request->Qualification) ? $request->Qualification : NULL;
		$user->note = !empty($request->note) ? $request->note : NULL;
		$user->School_Name = !empty($request->School_Name) ? $request->School_Name : NULL;
		$user->Subject = !empty($request->Subject) ? $request->Subject : NULL;
		$user->Related_Activity_History = !empty($request->relatedactivity) ? $val : NULL;
		// $user->is_other_experience = !empty($request->otherexperience) 
		// $user->Other_experience = !empty($request->Other_experience) ? $request->Other_experience : NULL;
		$user->Specialty = !empty($request->specialty) ? $specialtys : NULL;
		$user->Health_declaration = !empty($request->Health_declaration) ? $request->Health_declaration : NULL;
		$user->Health_declaration_text = !empty($request->Health_declaration_text) ? $request->Health_declaration_text : NULL;
		$user->Emergency_contact_name = !empty($request->Emergency_contact_name) ? $request->Emergency_contact_name : NULL;
		$user->EmergencyContact = !empty($request->EmergencyContact) ? $request->EmergencyContact : NULL;
		$user->Relationship = !empty($request->Relationship) ? $request->Relationship : NULL;
		$user->Relationship_text = !empty($request->Relationship_text) ? $request->Relationship_text : NULL;
		$user->JoinDate = !empty($request->JoinDate) ? $request->JoinDate : NULL;
		$user->Remarks = !empty($request->Remarks) ? $request->Remarks : NULL;
		$user->Remarks_desc = !empty($request->Remarks_desc) ? $request->Remarks_desc : NULL;
		$user->remark_date = !empty($request->remark_date) ? $request->remark_date : NULL;
		$user->hour_point = !empty($request->hour_point) ? $request->hour_point : NULL;
		$user->hour_point_rate = !empty($request->hour_point_rate) ? $request->hour_point_rate : NULL;
		$user->Role_ID = '2';
		$user->Attachment = !empty($fullImagePath) ? implode(',', $fullImagePath) : '';
		$user->Status = !empty($request->Status) ? $request->Status : '1';
		$user->member_token = !empty($request->member_token) ? $request->member_token : '0';
		//$user->total_money = !empty($request->total_money) ? $request->total_money : '0';
		$user->lastactivity = date('Y-m-d');
		if($request->otherexperience == 1){
			$user->is_other_experience = !empty($request->otherexperience); 
			$user->Other_experience = !empty($request->other_experience_text) ? $request->other_experience_text : NULL;
		}else{
			$user->is_other_experience = !empty($request->otherexperience); 
		}
		
		$result = $user->save();

		if (!empty($user->ID)) {
			$MemberTokenStatus = new MemberTokenStatus;
			$MemberTokenStatus->user_id = !empty($user->ID) ? $user->ID : NULL;
			$MemberTokenStatus->total_money = !empty($request->total_money) ? $request->total_money : NULL;
			$saveMemberToken = $MemberTokenStatus->save();
		}

		$history_log = new HistoryLog;
		$teamarr = array(
			'team_effiective_date' => !empty($request->team_effiective_date) ? $request->team_effiective_date : NULL,
			'team' => !empty($request->team) ? $request->team : NULL,
			'elite_team' => !empty($request->elite_team) ? $request->elite_team : NULL,
		);
		$history_log->teameilte_log = json_encode($teamarr);
		$history_log->team_status = '1';
		$history_log->user_id = $user['ID'];
		$history_log->page = 'edit_member';
		$rankarr = array(
			'rank_effiective_date' => !empty($request->rank_effiective_date) ? $request->rank_effiective_date : NULL,
			'Reference_number' => !empty($request->Reference_number) ? $request->Reference_number : NULL,
			'rank_team' => !empty($request->rank_team) ? $request->rank_team : NULL,
		);
		$history_log->rank_log = json_encode($rankarr);
		$history_log->rank_status = '1';
		$history_log->user_id = $user['ID'];
		$history_log->page = 'edit_member';
		$remarkarr = array(
			'remark' => !empty($request->Remarks) ? $request->Remarks : NULL,
			'Remarks_desc' => !empty($request->Remarks_desc) ? $request->Remarks_desc : NULL,
			'remark_date' => !empty($request->remark_date) ? $request->remark_date : NULL,
		);
		$history_log->remark_log = json_encode($remarkarr);
		$history_log->remark_status = '1';
		$history_log->user_id = $user['ID'];
		$history_log->page = 'edit_member';
		$history_log->save();

		if ($result) {
			return redirect('users')->with('success_msg', 'Member add successfully.');
		} else {
			return back()->with('error_msg', 'Something went wrong.');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
			$audit_log = HistoryLog::where('user_id', $id)->orderBy('id', 'desc')->get();
			$user_view = User::with('MemberTokenStatus')->where('ID', $id)->with('elite')->with('rank')->first()->toArray();
			$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
			$attendancecount = Attendance::where('user_id', $id)->get();
			$attendance = Attendance::where('user_id', $id)->with('users')->with('event')->offset(0)->limit(5)->get()->toArray();
			$Qualification = QualificationModel::where('status', '1')->get()->toArray();

			return view('MemberManagement.view_member', compact('user_view', 'RelatedActivityHistory', 'attendance', 'Qualification', 'attendancecount', 'audit_log'));
		} else {
			return redirect('/');
		}
	}

	public function event_type_serach(Request $request) {
		$eventtype = !empty($request->eventtype) ? $request->eventtype : '';
		$user_id = !empty($request->user_id) ? $request->user_id : '';
		$attendancedatacount = Attendance::where('user_id', $user_id)->where('event_type', $eventtype)->get();
		$attendance = Attendance::where('user_id', $user_id)->where('event_type', $eventtype)->with('users')->with('event')->offset(0)->limit(5)->get()->toArray();
		$html = '';
		$countdata = '';
		$color_array = array('timeline-icon-primary', 'timeline-icon-danger', 'timeline-icon-info', 'timeline-icon-warning');
		$size_of_array = sizeof($color_array);
		if (!empty($attendancedatacount)) {
			$attendancecount = count($attendancedatacount);
		} else {
			$attendancecount = '0';
		}
		$countdata .= '<div class="col-12 p-2"><h6 class="text-primary mb-0">' . __('languages.Attendance.Number_of_Attendance') . ': <span class="font-large-1 align-middle">' . $attendancecount . '</span></h6></div>';
		if (!empty($attendance)) {
			$html .= '<ul class="widget-timeline activity-cls">';
			foreach ($attendance as $key => $val) {
				$n = rand(0, $size_of_array - 1);
				$class = $color_array[$n % 3];
				$html .= '<li class="timeline-items ' . $class . ' active">
				<h6 class="timeline-title">' . $val['event']['event_name'] . '</h6>
				<div class="timeline-content">';
				if ($val['event_type'] == 'Training') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Training') . '</strong></p>';
				} elseif ($val['event_type'] == 'Activity') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Activity') . '</strong></p>';
				} elseif ($val['event_type'] == 'honour') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Guard_of_honour') . '</strong></p>';
				} elseif ($val['event_type'] == 'community') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.community') . '</strong></p>';
				} elseif ($val['event_type'] == 'Headquatters') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Headquatters') . '</strong></p>';
				} elseif ($val['event_type'] == 'administration') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Administration') . '</strong></p>';
				} elseif ($val['event_type'] == 'other') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Other') . '</strong></p>';
				} else {
					$html .= '<p class="timeline-text"></p>';
				}
				$html .= '<div class="col-md-2">
				<p class="timeline-text">' . __('languages.Attendance.In_Time') . ' : ' . $val['in_time'] . '</p>
				</div>
				<div class="col-md-2">
				<p class="timeline-text">' . __('languages.Attendance.Out_Time') . ' : ' . $val['out_time'] . '</p>
				</div>
				</div>
				<div class="timeline-content">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Total Hour') . ' : ' . $val['users']['hour_point'] . '</strong></p>
				<div class="col-md-2">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Used Hour') . ' : ' . $val['hours'] . '</strong></p>
				</div>
				<div class="col-md-3">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Remaining Hours') . ': ' . $val['remaining_hour'] . '</strong></p>
				</div>
				</div>
				</li>';
			}
			$html .= '</ul>';
		} else {
			$html .= '<ul class="widget-timeline"><li>' . __('languages.Attendance.No_activity_found') . '</li></ul>';
		}
		$last_id = !empty($val['id']) ? $val['id'] : 0;
		return response()->json(['html' => $html, 'countattendance' => $countdata, 'last_id' => $last_id]);
	}

	public function LoadMoreattendanceList(Request $request) {
		$attendanceId = !empty($request->attendanceId) ? $request->attendanceId : '';
		$user_id = !empty($request->user_id) ? $request->user_id : '';
		if (!empty($request->eventtype)) {
			$attendance = Attendance::where('id', '>', $attendanceId)->where('event_type', $request->eventtype)->where('user_id', $user_id)->with('users')->with('event')->offset(0)->limit(5)->get()->toArray();
		} else {
			$attendance = Attendance::where('id', '>', $attendanceId)->where('user_id', $user_id)->with('users')->with('event')->offset(0)->limit(5)->get()->toArray();
		}
		$html = '';
		$countdata = '';
		$color_array = array('timeline-icon-primary', 'timeline-icon-danger', 'timeline-icon-info', 'timeline-icon-warning');
		$size_of_array = sizeof($color_array);
		if (!empty($attendance)) {
			$attendancecount = count($attendance);
		} else {
			$attendancecount = '0';
		}
		$countdata .= '<div class="col-12 p-2"><h6 class="text-primary mb-0">' . __('languages.Attendance.Number_of_Attendance') . ': <span class="font-large-1 align-middle">' . $attendancecount . '</span></h6></div>';
		if (!empty($attendance)) {
			foreach ($attendance as $key => $val) {
				$n = rand(0, $size_of_array - 1);
				$class = $color_array[$n % 3];
				$html .= '<li class="timeline-items ' . $class . ' active">
				<h6 class="timeline-title">' . $val['event']['event_name'] . '</h6>
				<div class="timeline-content">';
				if ($val['event']['event_type'] == 'Training') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Training') . '</strong></p>';
				} elseif ($val['event_type'] == 'Activity') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Activity') . '</strong></p>';
				} elseif ($val['event_type'] == 'honour') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Guard_of_honour') . '</strong></p>';
				} elseif ($val['event_type'] == 'community') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.community') . '</strong></p>';
				} elseif ($val['event_type'] == 'Headquatters') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Headquatters') . '</strong></p>';
				} elseif ($val['event_type'] == 'administration') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Administration') . '</strong></p>';
				} elseif ($val['event_type'] == 'other') {
					$html .= '<p class="timeline-text"><strong>' . __('languages.event.Other') . '</strong></p>';
				} else {
					$html .= '<p class="timeline-text"></p>';
				}
				$html .= '<div class="col-md-2">
				<p class="timeline-text">' . __('languages.Attendance.In_Time') . ' : ' . $val['in_time'] . '</p>
				</div>
				<div class="col-md-2">
				<p class="timeline-text">' . __('languages.Attendance.Out_Time') . ' : ' . $val['out_time'] . '</p>
				</div>
				</div>
				<div class="timeline-content">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Total Hour') . ' : ' . $val['users']['hour_point'] . '</strong></p>
				<div class="col-md-2">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Used Hour') . ' : ' . $val['hours'] . '</strong></p>
				</div>
				<div class="col-md-3">
				<p class="timeline-text"><strong>' . __('languages.Attendance.Remaining Hours') . ': ' . $val['remaining_hour'] . '</strong></p>
				</div>
				</div>
				</li>';
			}
		}
		$last_id = !empty($val['id']) ? $val['id'] : 0;
		return response()->json(['html' => $html, 'countattendance' => $countdata, 'last_id' => $last_id]);

	}

	public function convertToStandardNumber($input) {

		$chinese_numsets = array("〇", "一", "二", "三", "四", "五", "六", "七", "八", "九");
		$standard_numsets = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

		return str_replace($standard_numsets, $chinese_numsets, $input);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
			$memberid = $id;
			$MemberToken = EventTokenManage::where('user_id',$memberid)->where('status','=','active')->sum('generate_token');
			if(empty($MemberToken)){
				$MemberToken = 0;
			}
			$activityHour = 0;
			$serviceHour = 0;
			$trainingHour = 0;
			$totalHour = 0;
			$edit_user = User::with('MemberTokenStatus')->where('ID', $id)->first()->toArray();
			$audit_log = HistoryLog::where('user_id', $id)->orderBy('id', 'desc')->get();
			$hourpackages = ServiceHourPackage::where('status', '1')->get()->toArray();
			$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
			$Specialty = Specialty::where('status', '1')->get()->toArray();
			$Remarks = Remarks::where('status', '1')->get()->toArray();
			$EilteModel = EilteModel::where('status', '1')->get()->toArray();
			$SubElite = SubElite::where('elite_id', $edit_user['team'])->get()->toArray();
			$Qualification = QualificationModel::where('status', '1')->get()->toArray();
			$attendanceData = Attendance::where('user_id',$id)->get();
			if(!empty($attendanceData)){
				foreach($attendanceData as $attendance){
					$activityValue = explode(':',$attendance->activity_hour);
					$serviceValue = explode(':',$attendance->service_hour);
					$trainingValue = explode(':',$attendance->training_hour);

					$activityHour = ($attendance->activity_hour != "00:00") ? ($activityHour + intval($activityValue[0])) : ($activityHour + 0);
					$serviceHour = ($attendance->serviceHour != "00:00") ?  ($serviceHour + intval($serviceValue[0])) : ($serviceHour + 0);
					// echo $serviceHour . " ".$serviceValue[0]."<br/>";
					$trainingHour = ($attendance->trainingHour != "00:00") ?  ($trainingHour + intval($trainingValue[0])) : ($trainingHour + 0);
				}
				$totalHour = $activityHour + $serviceHour + $trainingHour;
			}
			return view('MemberManagement.edit_member', compact('edit_user', 'audit_log', 'hourpackages', 'RelatedActivityHistory', 'Specialty', 'Remarks', 'EilteModel', 'SubElite', 'Qualification','MemberToken','activityHour','serviceHour','trainingHour','totalHour'));
		} else {
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
	public function update(Request $request, $id) {
		$user_update = User::find($id);
		if (!empty($user_update)) {
			$Attachment_images = explode(",", $user_update->Attachment);
		}
		$public_path = 'assets/image';
		if ($request->hasfile('Attachment')) {
			$images = $request->file('Attachment');
			foreach ($images as $val) {
				// $name =  time().$val->getClientOriginalName();
				$name = time() . rand(10, 90) . $val->getClientOriginalName();
				$val->move(public_path($public_path), $name);
				$fullImagePath[] = $public_path . '/' . $name;
			}
			if (!empty($Attachment_images)) {
				$fullImagePath = array_merge($fullImagePath, $Attachment_images);
			} else {
				$fullImagePath;
			}
		}
		$aval = array();
		if (!empty($request->relatedactivity)) {
			foreach ($request->relatedactivity as $key => $value) {
				foreach ($request->data as $key1 => $row) {
					if ($key1 == $value) {
						$aval[$key1] = $row[0];
					}
				}
			}
			$val = serialize($aval);
		}
		$specialty = array();
		if (!empty($request->specialty)) {
			foreach ($request->specialty as $key => $value) {
				foreach ($request->data as $key1 => $row) {
					if ($key1 == $value) {
						$specialty[$key1] = $row[0];
					}
				}
			}
			$specialtys = serialize($specialty);
		}

		/* Histroy Log START*/
		$history_log = new HistoryLog;
		$effirctivedate = !empty($user_update->team_effiective_date) ? $user_update->team_effiective_date : '';
		$team = !empty($user_update->team) ? $user_update->team : '';
		if (!empty($$request->team_effiective_date) || !empty($request->team)) {
			$teamarr = array(
				'team_effiective_date' => !empty($request->team_effiective_date) ? $request->team_effiective_date : NULL,
				'team' => !empty($request->team) ? $request->team : NULL,
				'elite_team' => !empty($request->elite_team) ? $request->elite_team : NULL,
			);
			$history_log->teameilte_log = json_encode($teamarr);
			$history_log->team_status = '1';
			$history_log->user_id = $id;
			$history_log->page = 'edit_member';
			$history_log->save();
		}
		$rank_effiective_date = !empty($user_update->rank_effiective_date) ? $user_update->rank_effiective_date : '';
		$Reference_number = !empty($user_update->Reference_number) ? $user_update->Reference_number : '';
		$rank_team = !empty($user_update->rank_team) ? $user_update->rank_team : '';
		if (!empty($request->rank_effiective_date) || !empty($request->Reference_number) || !empty($request->rank_team)) {
			$rankarr = array(
				'rank_effiective_date' => !empty($request->rank_effiective_date) ? $request->rank_effiective_date : NULL,
				'Reference_number' => !empty($request->Reference_number) ? $request->Reference_number : NULL,
				'rank_team' => !empty($request->rank_team) ? $request->rank_team : NULL,
			);
			$history_log->rank_log = json_encode($rankarr);
			$history_log->rank_status = '1';
			$history_log->user_id = $id;
			$history_log->page = 'edit_member';
			$history_log->save();
		}

		$remark = !empty($user_update->Remarks) ? $user_update->Remarks : NULL;
		$Remarks_desc = !empty($user_update->Remarks_desc) ? $user_update->Remarks_desc : NULL;
		$remark_date = !empty($user_update->remark_date) ? $user_update->remark_date : NULL;
		if (!empty($request->Remarks)) {
			$remarkarr = array(
				'remark' => !empty($request->Remarks) ? $request->Remarks : NULL,
				'Remarks_desc' => !empty($request->Remarks_desc) ? $request->Remarks_desc : NULL,
				'remark_date' => !empty($request->remark_date) ? $request->remark_date : NULL,
			);
			$history_log->remark_log = json_encode($remarkarr);
			$history_log->remark_status = '1';
			$history_log->user_id = $id;
			$history_log->page = 'edit_member';
			$history_log->save();
		}

		/* Histroy Log END*/

		/*Audit Log START*/
		$usersdata = new User;
		$post_value = $request->all();
		Helper::AuditLogfuncation($post_value, $usersdata, 'ID', $id, 'users', 'Member');
		/*Audit Log END*/

		/*Update member START*/
		$explode_member_code = explode("C", $request->MemberCode);
		$user_update->MemberCode = !empty($request->MemberCode) ? $explode_member_code[1] : NULL;
		$user_update->team_effiective_date = !empty($request->team_effiective_date) ? $request->team_effiective_date : $user_update->team_effiective_date;
		$user_update->team = !empty($request->team) ? $request->team : $user_update->team;
		$user_update->elite_team = !empty($request->elite_team) ? $request->elite_team : $user_update->elite_team;
		if ($user_update->team == "2") {
			$user_update->Specialty_Instructor = !empty($request->Specialty_Instructor) ? $request->Specialty_Instructor : $user_update->Specialty_Instructor;
			$user_update->Specialty_Instructor_text = !empty($request->Specialty_Instructor_text) ? $request->Specialty_Instructor_text : $user_update->Specialty_Instructor_text;
		} else {
			$user_update->Specialty_Instructor = NULL;
			$user_update->Specialty_Instructor_text = NULL;
		}
		$user_update->rank_effiective_date = !empty($request->rank_effiective_date) ? $request->rank_effiective_date : $user_update->rank_effiective_date;
		$user_update->Reference_number = !empty($request->Reference_number) ? $request->Reference_number : $user_update->Reference_number;
		$user_update->rank_team = !empty($request->rank_team) ? $request->rank_team : $user_update->rank_team;
		$user_update->Chinese_name = !empty($request->Chinese_name) ? $request->Chinese_name : NULL;
		$user_update->English_name = !empty($request->English_name) ? $request->English_name : NULL;
		$user_update->DOB = !empty($request->DOB) ? date('d/m/Y',strtotime($request->DOB)) : NULL;
		$user_update->age = !empty($request->age) ? $request->age : 0;
		$user_update->Gender = !empty($request->Gender) ? $request->Gender : NULL;
		$user_update->email = !empty($request->email) ? $request->email : NULL;
		$user_update->Contact_number = !empty($request->Contact_number) ? $request->Contact_number : NULL;
		$user_update->Contact_number_1 = !empty($request->Contact_number_1) ? $request->Contact_number_1 : NULL;
		$user_update->Contact_number_2 = !empty($request->Contact_number_2) ? $request->Contact_number_2 : NULL;
		$user_update->Chinese_address = !empty($request->Chinese_address) ? $request->Chinese_address : NULL;
		$user_update->English_address = !empty($request->English_address) ? $request->English_address : NULL;
		$user_update->Nationality = !empty($request->Nationality) ? $request->Nationality : NULL;
		$user_update->Occupation = !empty($request->Occupation) ? $request->Occupation : NULL;
		$user_update->ID_Number = !empty($request->ID_Number) ? $request->ID_Number : NULL;
		$user_update->Qualification = !empty($request->Qualification) ? $request->Qualification : NULL;
		$user_update->note = !empty($request->note) ? $request->note : NULL;
		$user_update->School_Name = !empty($request->School_Name) ? $request->School_Name : NULL;
		$user_update->Subject = !empty($request->Subject) ? $request->Subject : NULL;
		$user_update->Related_Activity_History = !empty($request->relatedactivity) ? $val : NULL;
		// $user_update->Other_experience = !empty($request->Other_experience) ? $request->Other_experience : NULL;
		$user_update->Specialty = !empty($request->specialty) ? $specialtys : NULL;
		$user_update->Health_declaration = !empty($request->Health_declaration) ? $request->Health_declaration : NULL;
		$user_update->Health_declaration_text = !empty($request->Health_declaration_text) ? $request->Health_declaration_text : NULL;
		$user_update->Emergency_contact_name = !empty($request->Emergency_contact_name) ? $request->Emergency_contact_name : NULL;
		$user_update->EmergencyContact = !empty($request->EmergencyContact) ? $request->EmergencyContact : NULL;
		$user_update->Relationship = !empty($request->Relationship) ? $request->Relationship : NULL;
		$user_update->Relationship_text = !empty($request->Relationship_text) ? $request->Relationship_text : NULL;
		$user_update->JoinDate = !empty($request->JoinDate) ? $request->JoinDate : NULL;
		$user_update->Remarks = !empty($request->Remarks) ? $request->Remarks : $user_update->Remarks;
		$user_update->Remarks_desc = !empty($request->Remarks_desc) ? $request->Remarks_desc : $user_update->Remarks_desc;
		$user_update->remark_date = !empty($request->remark_date) ? $request->remark_date : $user_update->remark_date;
		$user_update->lastactivity = date('Y-m-d');
		$user_update->hour_point = !empty($request->hour_point) ? $request->hour_point : NULL;
		$user_update->hour_point_rate = !empty($request->hour_point_rate) ? $request->hour_point_rate : NULL;
		$user_update->member_token = !empty($request->member_token) ? $request->member_token : '0';
		//$user_update->total_money = !empty($request->total_money) ? $request->total_money : '0';
		$user_update->Status = !empty($request->Status) ? $request->Status : NULL;
		if (isset($request->Attachment) && !empty($fullImagePath)) {
			$user_update->Attachment = implode(',', $fullImagePath);
		}
		if($request->otherexperience == 1){
			$user_update->is_other_experience = !empty($request->otherexperience) ? $request->otherexperience : NULL; 
			$user_update->Other_experience = !empty($request->other_experience_text) ? $request->other_experience_text : NULL;
		}else{
			$user_update->is_other_experience = !empty($request->otherexperience) ? $request->otherexperience : NULL; 
			$user_update->Other_experience = NULL;
		}
		$result = $user_update->save();
		/*Update member END*/

		/* member money update */
		$MemberTokenStatus = MemberTokenStatus::where('user_id', $id)->first();
		if (!empty($MemberTokenStatus)) {
			$MemberTokenStatus->total_money = !empty($request->total_money) ? $request->total_money : NULL;
			$MemberTokenStatus->save();
		} else {
			$NewMemberTokenStatus = new MemberTokenStatus;
			$NewMemberTokenStatus->user_id = !empty($id) ? $id : NULL;
			$NewMemberTokenStatus->total_money = !empty($request->total_money) ? $request->total_money : NULL;
			$saveMemberToken = $NewMemberTokenStatus->save();
		}
		/* member money update */
		if ($result) {
			if ($request->save) {
				return redirect('users/' . $id . '/edit')->with('success_msg', 'Member updated successfully.');
			} else {
				return redirect('users')->with('success_msg', 'Member updated successfully.');
			}
		} else {
			return back()->with('error_msg', 'Something went wrong.');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		if (in_array('members_delete', Helper::module_permission(Session::get('user')['role_id']))) {
			$user = User::where('ID', $id)->delete();
			if ($user) {
				$message = 'Member deleted successfully..';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}
			return response()->json(['status' => $status, 'message' => $message]);
		} else {
			$message = 'You do not have permission.';
			$status = false;
			return response()->json(['status' => $status, 'message' => $message]);
		}
	}

	public function checkEmail(Request $request) {
		if (!empty($request['query']['edit_email_address'])) {
			$email = !empty($request['query']['edit_email_address']) ? $request['query']['edit_email_address'] : '';
			$user_id = !empty($request['query']['user_id']) ? $request['query']['user_id'] : '';
			$user = User::where('email', $email)->whereNotIn('id', [$user_id])->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}

		if (!empty($request['query']['email_address'])) {
			$email = !empty($request['query']['email_address']) ? $request['query']['email_address'] : '';
			$user = User::where('email', $email)->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}

	public function checkChinesename(Request $request) {
		if (!empty($request['query']['edit_chinese_name'])) {
			$Chinesename = !empty($request['query']['edit_chinese_name']) ? $request['query']['edit_chinese_name'] : '';
			$user_id = !empty($request['query']['user_id']) ? $request['query']['user_id'] : '';
			$user = User::where('Chinese_name', $Chinesename)->whereNotIn('id', [$user_id])->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
		if (!empty($request['query']['chinese_name'])) {
			$Chinesename = !empty($request['query']['chinese_name']) ? $request['query']['chinese_name'] : '';
			$user = User::where('Chinese_name', $Chinesename)->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}

	public function checkEnglishname(Request $request) {
		if (!empty($request['query']['edit_english_name'])) {
			$Englishname = !empty($request['query']['edit_english_name']) ? $request['query']['edit_english_name'] : '';
			$user_id = !empty($request['query']['user_id']) ? $request['query']['user_id'] : '';
			$user = User::where('English_name', $Englishname)->whereNotIn('id', [$user_id])->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
		if (!empty($request['query']['english_name'])) {
			$Englishname = !empty($request['query']['english_name']) ? $request['query']['english_name'] : '';
			$user = User::where('English_name', $Englishname)->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}

	public function checkContactnumber(Request $request) {
		if (!empty($request->edit_contact_number)) {
			$contact_number = !empty($request->edit_contact_number) ? $request->edit_contact_number : '';
			$user_id = !empty($request->user_id) ? $request->user_id : '';
			$user = User::where('Contact_number', $contact_number)->whereNotIn('id', [$user_id])->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
		if (!empty($request->contact_number)) {
			$contact_number = !empty($request->contact_number) ? $request->contact_number : '';
			$user = User::where('Contact_number', $contact_number)->count();
			if ($user) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}

	public function historyTeamRank(Request $request) {
		$id = !empty($request->id) ? $request->id : '';
		$log = !empty($request->log) ? $request->log : '';

		$history = HistoryLog::find($id);
		if ($log == 'team') {
			$history->team_status = 0;
			$message = 'History of team deleted successfully..';
		} else if ($log == 'rank') {
			$history->rank_status = 0;
			$message = 'History of rank deleted successfully..';
		} else if ($log == 'remarks') {
			$history->remark_status = 0;
			$message = 'History of remarks deleted successfully..';
		}
		$result = $history->save();
		if ($result) {
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function membersList(Request $request) {
		$dateofbirth = !empty($request->dateofbirth) ? $request->dateofbirth : '';
		$age = !empty($request->age) ? $request->age : '';
		$joindate = !empty($request->filterjoindate) ? $request->filterjoindate : '';
		$filterverified = $request->filterverified;
		$filterelite = !empty($request->filterelite) ? $request->filterelite : '';
		$filtersubteam = !empty($request->filtersubteam) ? $request->filtersubteam : '';
		$filtergender = !empty($request->filtergender) ? $request->filtergender : '';
		$filterrole = !empty($request->filterrole) ? $request->filterrole : '';
		$filterstatus = $request->filterstatus;
		$filterrealtedactivity = !empty($request->filterrealtedactivity) ? $request->filterrealtedactivity : '';
		$filterspecialty = !empty($request->filterspecialty) ? $request->filterspecialty : '';
		$filterqualification = !empty($request->filterqualification) ? $request->filterqualification : '';
		$filterrank = !empty($request->filterrank) ? $request->filterrank : '';
		$filterhourpoint = !empty($request->filterhourpoint) ? $request->filterhourpoint : '';
		$users = '';

		$query = User::where('Role_ID', '!=', '1');

		if (isset($request->token)) {
			$tokenArry = explode('-', $request->token);
			if (!empty($tokenArry)) {
				$query->where('member_token', '>=', $tokenArry[0]);
				$query->where('member_token', '<=', $tokenArry[1]);
			}
		}

		if (isset($request->filterverified) && !empty($filterverified)) {
			$query->where('email_verified', $filterverified);
		}
		if (isset($request->filterstatus) && !empty($filterstatus)) {
			$query->where('Status', $filterstatus);
		}
		if (isset($request->filterelite) && !empty($filterelite)) {
			$query->where('team', $filterelite);
		}
		if (isset($request->filtersubteam) && !empty($filtersubteam)) {
			$query->where('elite_team', $filtersubteam);
		}
		if (isset($request->filtergender) && !empty($filtergender)) {
			$query->where('Gender', $filtergender);
		}
		if (isset($request->filterrole) && !empty($filterrole)) {
			$query->where('Role_ID', $filterrole);
		}
		if (isset($request->filterqualification) && !empty($filterqualification)) {
			$query->where('Qualification', $filterqualification);
		}
		if (isset($request->filterhourpoint) && !empty($filterhourpoint)) {
			$query->where('hour_point', $filterhourpoint);
		}

		if (isset($request->filterjoindate) && !empty($joindate)) {
			$expolde_joindate = array_map('trim',explode('-', $joindate));
			// $start_joindate = date('j F, Y', strtotime(Helper::dateFormatMDY('/','-',$expolde_joindate[0])));
			// $end_joindate = date('j F, Y', strtotime(Helper::dateFormatMDY('/','-',$expolde_joindate[1])));
			$start_joindate = $expolde_joindate[0];
			$end_joindate = $expolde_joindate[1];
			$query->whereBetween('JoinDate', array($start_joindate, $end_joindate));
			//$query->where('JoinDate','>=',$start_joindate)->where('JoinDate','=<',$end_joindate);
		}
		if (isset($request->age) && !empty($age)) {
			//$query->where('age',$age);
			$expolde_age = explode('-', $age);

			$query->whereBetween('age', array($expolde_age[0], $expolde_age[1]));
		}
		
		if (isset($request->dateofbirth) && !empty($request->dateofbirth)) {
			$expolde_birth = array_map('trim',explode('-',$dateofbirth));
			// $start_birth = Helper::dateConvertDDMMYYY('/','-',$expolde_birth[0]);			
			// $end_birth = Helper::dateConvertDDMMYYY('/','-',$expolde_birth[1]);
			$start_birth = $expolde_birth[0];
			$end_birth = $expolde_birth[1];
			$query->whereBetween('DOB', array($start_birth, $end_birth));
			//$query->where('DOB', '>=', $start_birth)->where('DOB', '<=', $end_birth);

		}
		if (isset($request->filterrank) && !empty($filterrank)) {
			$query->where('rank_team', $filterrank);
			// $expolde_rank = explode('-', $filterrank);
			// $start_rank = date('j F, Y',strtotime($expolde_rank[0]));
			// $end_rank = date('j F, Y',strtotime($expolde_rank[1]));
			// $query->whereBetween('rank_effiective_date',array($start_rank,$end_rank));
		}

		/*$users = $query->with('elite')->with('subteam')->with('rank')->with('Qualification')->with('Remarks')->get()->toArray();*/
		$users = $query->with('MemberTokenStatus')->with('elite')->with('subteam')->with('rank')->with('Qualification')->with('Remarks')->get()->toArray();

		$usersdata = array();
		$idsarr = array();
		if (!empty($filterrealtedactivity)) {
			foreach ($filterrealtedactivity as $activitys) {
				$users = User::selectRaw('ID,Related_Activity_History')->where('Role_ID', '2')->get()->toArray();
				foreach ($users as $key => $val) {
					$activity = unserialize($val['Related_Activity_History']);
					if (!empty($activity)) {
						if (array_key_exists($activitys, $activity)) {
							$idsarr[] = $val['ID'];
						}
					}
				}
			}
			if (!empty($idsarr)) {
				foreach (array_unique($idsarr) as $key => $row) {
					$usersdata[] = User::whereRaw('FIND_IN_SET(?, ID)', [$row])->where('Role_ID', '!=', '1')->with('elite')->with('subteam')->with('subteam')->with('rank')->with('Qualification')->with('Remarks')->get()->toArray();
				}
			}
		}

		if (!empty($filterspecialty)) {
			foreach ($filterspecialty as $specialtys) {
				if(isset($request->filterrank)){
					$users = User::selectRaw('ID,Specialty')->where('Role_ID', '2')->where('rank_team',$filterrank)->get()->toArray();
				}else{
					$users = User::selectRaw('ID,Specialty')->where('Role_ID', '2')->get()->toArray();
				}
				
				foreach ($users as $key => $val) {
					$specialty = unserialize($val['Specialty']);
					if (!empty($specialty)) {
						if (array_key_exists($specialtys, $specialty)) {
							$idsspecialtyarr[] = $val['ID'];
						}
					}
				}
			}
			if (!empty($idsspecialtyarr)) {
				foreach (array_unique($idsspecialtyarr) as $key => $row) {
					$usersdata[] = User::whereRaw('FIND_IN_SET(?, ID)', [$row])
									->where('Role_ID', '!=', '1')
									//->with('elite')
									//->with('rank')
									//->with('subteam')
									//->with('Qualification')
									//->with('Remarks')
									->get()
									->toArray();
				}
			}
		}
		$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
		$Specialty = Specialty::where('status', '1')->get()->toArray();
		$Qualification = QualificationModel::where('status', '1')->get()->toArray();
		$Ranks = SubElite::where('status', '1')->groupBy('subelite_' . app()->getLocale())->get()->toArray();
		$Teams = EilteModel::where('status', '1')->get()->toArray();
		$subteams = Subteam::where('status', '1')->get()->toArray();

		if (!empty($filterrealtedactivity) || !empty($filterspecialty)) {
			$users = [];
			if(isset($usersdata) && !empty($usersdata)){
				foreach($usersdata as $usersArr){
					if(isset($usersArr[0]) && !empty($usersArr[0])){
						$users[] = $usersArr[0];
					}
				}
			}
			return view('MemberManagement.filter_member_list_activity', compact('users', 'usersdata', 'RelatedActivityHistory', 'Specialty', 'Qualification', 'Ranks', 'Teams', 'subteams'));
		} else {
			return view('MemberManagement.filter_member_list', compact('users', 'RelatedActivityHistory', 'Specialty', 'Qualification', 'Ranks', 'Teams', 'subteams'));
		}
	}

	public function remarksData($id) {
		$html = '';
		if ($id != 0) {
			$Remarks = Remarks::find($id)->toArray();
			$language = 'remarks_' . app()->getLocale();
			$html .= '<div class="form-row">
			<div class="form-group col-md-6 mb-50">
			<input type="text" class="form-control" id="Remarks_desc" placeholder="' . $Remarks[$language] . '" value="" name="Remarks_desc">
			</div>
			<div class="form-group col-md-6 mb-50">
			<input type="text" class="form-control" id="remark_date" name="remark_date" value="' . date('d F, Y') . '">
			</div>
			</div>';
			return $html;
		} else {
			return $html;
		}
	}
	public function remarkseditData($id) {
		$Remarks = User::find($id)->toArray();
		if (!empty(Session::get('userData')['Remarks_desc'])) {
			$remark_desc = Session::get('userData')['Remarks_desc'];
		} else {
			$remark_desc = !empty($Remarks['Remarks_desc']) ? $Remarks['Remarks_desc'] : '';
		}
		$remark_date = !empty($Remarks['remark_date']) ? $Remarks['remark_date'] : '';
		$html = '';
		$html .= '<div class="form-row">
		<div class="form-group col-md-6 mb-50">
		<input type="text" class="form-control" id="Remarks_desc" value="' . $remark_desc . '" name="Remarks_desc">
		</div>
		<div class="form-group col-md-6 mb-50">
		<input type="text" class="form-control" id="remark_date" name="remark_date" value="' . $remark_date . '">
		</div>
		</div>';
		return $html;
	}

	public function elitedata($id) {
		$SubElite = SubElite::where('elite_id', $id)->get()->toArray();
		$Subteam = Subteam::where('elite_id', $id)->get()->toArray();
		$html = '';
		$rank_html = '';

		if (!empty($Subteam)) {
			if ($id == 2) {
				$html .= '<div class="row"><div class="form-group col-md-6 mb-50"><label class="text-bold-600" for="exampleInputUsername1">' . __('languages.member.Special_instructor') . '<span class="required-cls">*</span></label>
				<ul class="list-unstyled mb-0">
				<li class="d-inline-block mt-1 mr-1 mb-1">
				<fieldset>
				<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input" name="Specialty_Instructor" id="Specialty_Instructor1" value="1">
				<label class="custom-control-label" for="Specialty_Instructor1">' . __('languages.Yes') . '</label>
				</div>
				</fieldset>
				</li>
				<li class="d-inline-block my-1 mr-1 mb-1">
				<fieldset>
				<div class="custom-control custom-radio">
				<input type="radio" class="custom-control-input" name="Specialty_Instructor" id="Specialty_Instructor2" value="2">
				<label class="custom-control-label" for="Specialty_Instructor2">' . __('languages.No') . '</label>
				</div>
				</fieldset>
				</li>
				</ul>
				<div class="Specialty-Instructor-error-cls"></div>
				</div>
				<div class="form-group col-md-6 mb-50 speicals-instructor-cls" style="display:none;">
				<label class="text-bold-600" for="exampleInputUsername1"></label>
				<input type="text" class="form-control" id="Specialty_Instructor_text" name="Specialty_Instructor_text" placeholder="' . __('languages.member.Special_instructor') . '" value="">
				</div></div>';
				$html .= '<fieldset class="form-group elite-cls"><select class="form-control" id="elite_team" name="elite_team"><option value="">' . __('languages.member.select') . '</option>';
				foreach ($Subteam as $val) {
					$html .= '<option value="' . $val['id'] . '">' . $val['subteam_' . app()->getLocale()] . '</option>';
				}
				$html .= '</fieldset></select>';
			} else {
				$html .= '<fieldset class="form-group elite-cls"><select class="form-control" id="elite_team" name="elite_team"><option value="">' . __('languages.member.select') . '</option>';
				foreach ($Subteam as $val) {
					$html .= '<option value="' . $val['id'] . '">' . $val['subteam_' . app()->getLocale()] . '</option>';
				}
				$html .= '</fieldset></select>';
			}
		} else {
			$html .= '<fieldset class="form-group elite-cls"><select class="form-control" id="elite_team" name="elite_team"><option value="">' . __('languages.member.select') . '</option></fieldset></select>';
		}
		if (!empty($SubElite)) {
			$rank_html .= '<label class="text-bold-600" for="Rank">' . __('languages.member.Rank') . '</label><fieldset class="form-group rank-cls"><select class="form-control" id="rank_team" name="rank_team"><option value="">' . __('languages.member.select') . '</option>';
			foreach ($SubElite as $val) {
				$rank_html .= '<option value="' . $val['id'] . '">' . $val['subelite_' . app()->getLocale()] . '</option>';
			}
			$rank_html .= '</fieldset></select>';
		} else {
			$rank_html .= '<label class="text-bold-600" for="Rank">' . __('languages.member.Rank') . '</label><fieldset class="form-group rank-cls"><select class="form-control" id="rank_team" name="rank_team"><option value="">' . __('languages.member.select') . '</option></fieldset></select>';
		}
		return response()->json(['status' => 1, 'elite_team' => $html, 'rank_team' => $rank_html]);
	}

	/**
	 * USE : If user can select events option then display the html in to member list
	 */
	public function get_all_event() {
		if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
			$events = Events::select('event_name', 'id')->where('status', '1')->get()->toArray();
			$eventTypes = new EventType;
			$get_event_type_list = $eventTypes->get_event_type_select_list();

			$html = '';
			$html .= '<div class="event_main_clss">';
				$html .= '<div class="event_date_cls"><fieldset class="form-group position-relative has-icon-left"><input type="text" class="form-control filter_event" id="filter_event" name="filter_event" placeholder="' . __('languages.Select_date') . '" autocomplete="off"><div class="form-control-position"><i class="bx bx-calendar-check"></i></div></fieldset></div>';
					$html .= '<div class="event_date_cls events_type_clss">';
						$html .= '<fieldset class="form-group">';
							$html .= '<select class="form-control event_type_cls" id="events_type" name="events_type"><option value="">' . __('languages.event.Select_event_type') . '</option>';
									if (!empty($get_event_type_list)) {
										$html .= $get_event_type_list;
									}
								$html .= '</select>';
						$html .= '</fieldset>';
					$html .= '</div>';
			$html .= '<div class="filter_event_cls"></div>';
			/*$html .= '<div class="cost_method_cls"><fieldset class="form-group"><select class="form-control" id="cost_method" name="cost_method"><option value="">'.__('languages.event.Select_Cost_Method').'</option><option value=""</select></fieldset></div>';*/
			$html .= '<div class="event-id-cls event_name_cls">
			<fieldset class="form-group"><select class="form-control event_name_select" id="events_name" name="events_name"><option value="">' . __('languages.event.Select_Event') . '</option>';
			$html .= '</select></fieldset></div>';

			$html .= '<div class="event-id-cls event_post_cls">
			<fieldset class="form-group"><select class="form-control" id="events_post_type" name="events_post_type"><option value="">' . __('languages.event.Select_post_type') . '</option>';
			$html .= '</select></fieldset></div>';

			$html .= '<div class="form-row events-id-cls"><input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_event"></div>';
			$html .= '</div>';
			return response()->json(['status' => 1, 'html' => $html]);
		}
	}

	public function get_event_type(Request $request) {

		$eventTypeId = !empty($request->search_event_type) ? $request->search_event_type : '';
		$event_date = !empty($request->search_event_date) ? $request->search_event_date : '';
		$ids = array();
		$html = '';

		if (!empty($event_date)) {
			$expolde_event_date = explode('-', $event_date);
			$start_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[0]);
			$end_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[1]);
			//$search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE STR_TO_DATE(date, '%m/%d/%Y') BETWEEN STR_TO_DATE('" . $start_event_date . "', '%m/%d/%Y') AND STR_TO_DATE('" . $end_event_date . "', '%m/%d/%Y') AND status = 1 GROUP BY event_code"));
			$search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE STR_TO_DATE(date, '%m/%d/%Y') BETWEEN STR_TO_DATE('" . $start_event_date . "', '%m/%d/%Y') AND STR_TO_DATE('" . $end_event_date . "', '%m/%d/%Y') AND status = 1"));
			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'id');
			} else {
				$html .= '<div class="event-id-cls">
				<fieldset class="form-group"><select class="form-control event_name_select" id="events_name" name="events_name"><option value="">' . __('languages.event.Select_Event') . '</option>';
				$html .= '</select></fieldset></div>';

				$html .= '<div class="event-id-cls event_post_cls">
				<fieldset class="form-group"><select class="form-control" id="events_post_type" name="events_post_type"><option value="">' . __('languages.event.Select_post_type') . '</option>';
				$html .= '</select></fieldset></div>';

				$html .= '<div class="form-row events-id-cls"><input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_event"></div>';
				return $html;exit;
			}
		}

		$Select_db = DB::table('event_schedule')
			->select('event_schedule.*', 'events.event_name')
			->join('events', 'events.id', 'event_schedule.event_id')
		//->where('date',date('m/d/Y'))
			->where('event_schedule.status', 1);
		if (empty($request->search_event_date)) {
			$Select_db->where('event_schedule.date', date('m/d/Y'));
		}
		if (!empty($request->search_event_date)) {
			$Select_db->whereIn('event_schedule.id', $ids);
		}
		if (isset($request->search_event_type) && !empty($eventTypeId)) {
			$Select_db->where('events.event_type', $eventTypeId);
		}
		$events = $Select_db->get();

		$html .= '<div class="event-id-cls">
		<fieldset class="form-group"><select class="form-control event_name_select" id="events_name" name="events_name"><option value="">' . __('languages.event.Select_Event') . '</option>';

		foreach ($events as $key => $event) {
			$totalhour = Helper::totalhourEvent($event->event_code);
			/*$minstartDate = !empty($totalhour['minstartDate']) ? DateTime::createFromFormat('l,d F,Y', $totalhour['minstartDate']) : '';
			$maxendDate = !empty($totalhour['maxendDate']) ? DateTime::createFromFormat('l,d F,Y', $totalhour['maxendDate']) : '';*/
			$scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $event->event_id)->groupBy('date')->get()->toArray();

			$dates = [];
			foreach ($scheduleData as $val) {
				$dates[] = $val['date'];
			}

			$minstartDate = $dates[0];
			$maxendDate = end($dates);
			$current_time = strtotime(date('H:i'));
			$event_time = strtotime($event->start_time);
			$diff = $event_time - $current_time;

			if ($diff > 0 && empty($request->search_event_date)) {
				/*$html .= '<option value="'.$event->event_code.'">'.$event->event_name.' - '.$minstartDate->format('m/d/Y').' - '.$maxendDate->format('m/d/Y').'</option>';*/
				$html .= '<option data-id="' . $event->event_id . '" value="' . $event->event_code . '">' . $event->event_name . ' - ' . $minstartDate . ' - ' . $maxendDate . '</option>';
			} else {
				$html .= '<option data-id="' . $event->event_id . '" value="' . $event->event_code . '">' . $event->event_name . ' - ' . $minstartDate . ' - ' . $maxendDate . '</option>';

			}

		}
		$html .= '</select></fieldset></div>';

		$html .= '<div class="event-id-cls event_post_cls">
				<fieldset class="form-group"><select class="form-control" id="events_post_type" name="events_post_type"><option value="">' . __('languages.event.Select_post_type') . '</option>';

		$html .= '</select></fieldset></div>';

		$html .= '<div class="form-row events-id-cls"><input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_event"></div>';
		return $html;
	}

	/**
	 * USE : Get list of cost type for assign events to members
	 */
	public function get_event_post_type(Request $request) {
		$html = '';
		$html .= '<fieldset class="form-group"><select class="form-control events_post_cls" id="events_post_type" name="events_post_type"><option value="">' . __('languages.event.Select_post_type') . '</option>';
		//$eventsPostType = DB::table('event_post_type')->where('event_id', $request->event_id)->get()->toArray();
		$eventsPostType = EventPosttypeModel::where('event_id', $request->event_id)->get();
		if(!empty($eventsPostType)){
			foreach ($eventsPostType as $key => $posttype) {
				if($posttype->post_type == 1) {
					$html .= '<option value="'.$posttype->id.'" data-id="1">' . __('languages.event.Money') . ' - ' . $posttype->post_value . '</option>';
				} else if ($posttype->post_type == 2) {
					$html .= '<option value="'.$posttype->id.'" data-id="2">' . __('languages.event.Tokens') . ' - ' . $posttype->post_value . '</option>';
				} else if ($posttype->post_type == 3) {
					$moneyToken = explode('+', $posttype->post_value);
					$html .= '<option value="'.$posttype->id.'" data-id="3">' . __('languages.event.Money') . ' - ' . $moneyToken[0] . ' + ' . __('languages.event.Tokens') . ' - ' . $moneyToken[1] . '</option>';
				}
			}
		}
		$html .= '</select></fieldset>';
		$html .= '<div class="event_remark_cls"><input class="form-control" type="text" name="remarks" id="remarksEvent" placeholder="' . __('languages.member.Add Notes') . '"></div>';
		return $html;
	}

	public function get_product_cost_type(Request $request) {
		$html = '';
		$html .= '<fieldset class="form-group"><select class="form-control product_cost_cls" id="product_cost_type" name="product_cost_type"><option value="">' . __('languages.event.Select_post_type') . '</option>';
		$productCostType = ProductCosttypeModel::where('product_id', $request->product_id)->get();
		if(!empty($productCostType)){
			foreach ($productCostType as $key => $costtype) {
				if ($costtype->cost_type == 1) {
					$selected = '';
					if($costtype->cost_type == 1 && $costtype->cost_value == 0){
						$selected = 'selected';
					}
					$html .= '<option value="'.$costtype->id.'" '.$selected.'>' . __('languages.event.Money') . ' - ' . $costtype->cost_value . '</option>';
				} else if ($costtype->cost_type == 2) {
					$html .= '<option value="'.$costtype->id.'">' . __('languages.event.Tokens') . ' - ' . $costtype->cost_value . '</option>';
				} else if ($costtype->cost_type == 3) {
					$moneyToken = explode('+', $costtype->cost_value);
					$html .= '<option value="'.$costtype->id.'">' . __('languages.event.Money') . ' - ' . $moneyToken[0] . ' + ' . __('languages.event.Tokens') . ' - ' . $moneyToken[1] . '</option>';
				}
			}
		}		
		$html .= '</select></fieldset>';
		$html .= '<div class="product_remark_cls"><input class="form-control" type="text" name="remarks" id="remarksEvent" placeholder="' . __('languages.member.Add Notes') . '"></div>';
		$html .= '<div class="form-row events-id-cls1"><input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_product"></div>';
		return $html;
	}

	/**
	 * USE : Get the product by child product details
	 */
	public function get_child_product_prefix_suffix(Request $request){
		$html = '';
		$html .='<fieldset class="form-group">';
		$html .='<select class="form-control" name="child_product_select[]" id="child_product_select" multiple>';
		// $html .='<option>'. __("languages.member.select_product_code").'</option>';
		//check selected product is combo product or not
		$Product = ProductModel::find($request->product_id);
		if(isset($Product->combo_product_ids) && !empty($Product->combo_product_ids)){
			$comboProductIds = explode(',',$Product->combo_product_ids);
			if(isset($comboProductIds) && !empty($comboProductIds)){
				foreach($comboProductIds as $ProductId){
					$MainProduct = ProductModel::find($ProductId);
					if(isset($MainProduct) && !empty($MainProduct->product_name)){
						$html .='<optgroup label="'.$MainProduct->product_name.'">';
					}
					$childProducts = ChildProduct::with('Product')->where('main_product_id', $ProductId)->get();
					// Select child Product Dropdowns
					if(!empty($childProducts)){
						foreach($childProducts as $product){
							if(!empty($product->product_suffix) && !empty($product->product_suffix_name)){
								$html .='<option value="'.$product->id.'" mainproductid="'.$ProductId.'">'.$product->product_suffix.' + '.$product->product_suffix_name.'</option>';
							}
						}
					}
				}
			}
		}else{
			$childProducts = ChildProduct::where('main_product_id', $request->product_id)->get();
			// Select child Product Dropdowns
			if(!empty($childProducts)){
				foreach($childProducts as $product){
					if(!empty($product->product_suffix) && !empty($product->product_suffix_name)){
						$html .='<option value="'.$product->id.'" mainproductid="'.$product->id.'">'.$product->product_suffix.' + '.$product->product_suffix_name.'</option>';
					}
				}
			}
		}
		$html .='</select>';
		$html .='</fieldset>';
		return $html;	
	}

	public function update_status(Request $request) {
		$user_id = $request->user_id;
		$status = $request->value;
		$update_status = User::find($user_id);
		$update_status->status = $status;
		$result = $update_status->save();
		if (!empty($result)) {
			$message = 'Status updated successfully.';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	/**
	 * USE : Update status for the multiple user 
	 */
	public function multiple_user_update_status(Request $request) {
		$update_status = User::whereIn('ID',$request->userIds)->update(['status' => $request->status]);
		if (!empty($update_status)) {
			$message = 'Status updated successfully.';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	/**
	 * USE : User delete document
	 **/
	public function ajaxDeleteDocument(Request $request) {
		if ($request->id && $request->imageName) {
			$response = $this->deleteDocument($request);
			$img = implode(",", $response);
			$User = User::find($request->id);
			$User->Attachment = (!empty($response)) ? $img : null;
			if (empty($response)) {
				$User->Attachment = null;
			}
			$User->save();
			return response()->json(['status' => true]);
		}
	}

	/**
	 * USE : Delete image delete databse with unlink folder
	 **/
	public function deleteDocument($request) {
		$newImageArray = [];
		$result = User::select('Attachment')->where('ID', $request->id)->first();
		$imageArray = explode(',', $result['Attachment']);
		$imgPath = public_path('/assets/image/') . $request->imageName;
		if (file_exists($imgPath)) {
			unlink($imgPath);
		}
		$newImageArray = $this->remove_element($imageArray, 'assets/image/' . $request->imageName);
		return $newImageArray;
	}

	/**
	 * USE : Remove array in element
	 */
	function remove_element($array, $value) {
		foreach ($array as $key => $ArrayValue) {
			if ($ArrayValue == $value) {
				unset($array[$key]);
			}
		}
		return $array;
	}

	public function exportCSV(Request $request) {
		$selectedUserIds = array();
		if(isset($request->selectedUserIds) && !empty($request->selectedUserIds)){
			$selectedUserIds = $request->selectedUserIds;
		}
		parse_str($_POST['formData'], $request);
		$request['selectedUserIds'] = $selectedUserIds;
		$dateofbirth = !empty($request->dateofbirth) ? $request->dateofbirth : '';
		$age = !empty($request->age) ? $request->age : '';
		$joindate = !empty($request->filterjoindate) ? $request->filterjoindate : '';
		$filterelite = !empty($request->filterelite) ? $request->filterelite : '';
		$filtersubteam = !empty($request->filtersubteam) ? $request->filtersubteam : '';
		$filtergender = !empty($request->filtergender) ? $request->filtergender : '';
		$filterrole = !empty($request->filterrole) ? $request->filterrole : '';
		$filterstatus = !empty($request->filterstatus) ? $request->filterstatus : '';
		$filterrealtedactivity = !empty($request->filterrealtedactivity) ? $request->filterrealtedactivity : '';
		$filterspecialty = !empty($request->filterspecialty) ? $request->filterspecialty : '';
		$filterqualification = !empty($request->filterqualification) ? $request->filterqualification : '';
		$filterrank = !empty($request->filterrank) ? $request->filterrank : '';
		$filterhourpoint = !empty($request->filterhourpoint) ? $request->filterhourpoint : '';
		$users = '';

		$query = User::where('Role_ID', '!=', '1');
		if(isset($request['selectedUserIds']) && !empty($request['selectedUserIds'])){
			$query->whereIn('ID',$request['selectedUserIds']);
		}
		if (isset($request->filterstatus) && !empty($filterstatus)) {
			$query->where('Status', $filterstatus);
		}
		if (isset($request->filterelite) && !empty($filterelite)) {
			$query->where('team', $filterelite);
		}
		if (isset($request->filtersubteam) && !empty($filtersubteam)) {
			$query->where('elite_team', $filtersubteam);
		}
		if (isset($request->filtergender) && !empty($filtergender)) {
			$query->where('Gender', $filtergender);
		}
		if (isset($request->filterrole) && !empty($filterrole)) {
			$query->where('Role_ID', $filterrole);
		}
		if (isset($request->filterqualification) && !empty($filterqualification)) {
			$query->where('Qualification', $filterqualification);
		}
		if (isset($request->filterhourpoint) && !empty($filterhourpoint)) {
			$query->where('hour_point', $filterhourpoint);
		}

		if (isset($request->filterjoindate) && !empty($request->filterjoindate)) {
			$expolde_joindate = array_map('trim',explode('-', $joindate));
			$start_joindate = $expolde_joindate[0];
			$end_joindate = $expolde_joindate[1];
			$query->whereBetween('JoinDate', array($start_joindate, $end_joindate));
			// $start_joindate = date('j F, Y', strtotime($expolde_joindate[0]));
			// $end_joindate = date('j F, Y', strtotime($expolde_joindate[1]));
			// $query->whereBetween('JoinDate', array($start_joindate, $end_joindate));
		}
		if (isset($request->age) && !empty($age)) {
			$expolde_age = explode('-', $age);
			$query->whereBetween('age', array($expolde_age[0], $expolde_age[1]));
		}
		if (isset($request->dateofbirth) && !empty($dateofbirth)) {
			$expolde_birth = explode('-', $dateofbirth);
			$start_birth = $expolde_birth[0];
			$end_birth = $expolde_birth[1];
			$query->whereBetween('DOB', array($start_birth, $end_birth));
			// $start_birth = date('m/d/Y', strtotime($expolde_birth[0]));
			// $end_birth = date('m/d/Y', strtotime($expolde_birth[1]));
			// $query->whereBetween('DOB', array($start_birth, $end_birth));
		}
		if (isset($request->filterrank) && !empty($filterrank)) {
			$query->where('rank_team', $filterrank);
		}
		$users = $query->with('elite')->with('subteam')->with('rank')->with('Qualification')->with('Remarks')->get()->toArray();
		
		if (!empty($users)) {
			$rows = [];
			
			
			foreach ($users as $key => $value) {
				$related_activity = unserialize($value['Related_Activity_History']);
				if (!empty($related_activity)) {
					$related_activity_key = array_keys($related_activity);
					$related_activity_text = str_replace("_", " ", $related_activity_key);
				}
				$Specialty = unserialize($value['Specialty']);
				if (!empty($Specialty)) {
					$Specialty_key = array_keys($Specialty);
					$Specialty_text = str_replace("_", " ", $Specialty_key);
				}
				if (!empty($value['Relationship'])) {
					if ($value['Relationship'] == '1') {
						$Relationship = 'Father/Son';
					} elseif ($value['Relationship'] == '2') {
						$Relationship = 'Mother/Son';
					} elseif ($value['Relationship'] == '3') {
						$Relationship = 'Father/Daugther';
					} elseif ($value['Relationship'] == '4') {
						$Relationship = 'Mother/Daugther';
					} elseif ($value['Relationship'] == '5') {
						$Relationship = 'Brother/sister';
					} else {
						$Relationship = 'Other relationship';
					}

				}
				//Added By Vishnu Sir
				//echo $value['ID']."<br/>";
				//echo "email".$value['email']."<br/>";
				
				//echo "Uname".$value['UserName'];

				$user_id = base64_encode($value['ID']);
                			$email_add = base64_encode($value['email']);
                			$QRString = $user_id . "/" . $email_add . "/" . trim($value['UserName']);

				$member_qr_code=$QRString ?? '';
				//End

				$rows[$key]['2'] = ($value['MemberCode']) ? 'C' . $value['MemberCode'] : '';
				$rows[$key]['3'] = isset($value['elite']['elite_' . app()->getLocale()]) ? $value['elite']['elite_' . app()->getLocale()] : '';
				$rows[$key]['4'] = isset($value['subteam']['subteam_' . app()->getLocale()]) ? $value['subteam']['subteam_' . app()->getLocale()] : '';
				$rows[$key]['5'] = ($value['Specialty_Instructor'] == '1') ? 'Yes' : 'No';
				$rows[$key]['6'] = $value['Specialty_Instructor_text'];
				$rows[$key]['7'] = (isset($value['team_effiective_date']) && $value['team_effiective_date']!="") ? date('d/m/Y', strtotime($value['team_effiective_date'])) : '';
				$rows[$key]['8'] = isset($value['rank']['subelite_' . app()->getLocale()]) ? $value['rank']['subelite_' . app()->getLocale()] : '';
				$rows[$key]['9'] =  (isset($value['rank_effiective_date']) && $value['rank_effiective_date']!="") ? $value['rank_effiective_date'] : '';
				$rows[$key]['10'] = $value['Reference_number'];
				$rows[$key]['11'] = $value['Chinese_name'];
				$rows[$key]['12'] = $value['English_name'];
				$rows[$key]['13'] = $value['ID_Number'];
				$rows[$key]['14'] = ($value['Gender'] == '1') ? 'Male' : 'Female';
				$rows[$key]['15'] = $value['age'];
				$rows[$key]['16'] = (isset($value['DOB']) && $value['DOB']!="") ? $value['DOB'] : '';
				$rows[$key]['17'] = $value['Nationality'];
				$rows[$key]['18'] = $value['email'];
				$rows[$key]['19'] = $value['Contact_number'];
				$rows[$key]['20'] = $value['Contact_number_1'];
				$rows[$key]['21'] = $value['Contact_number_2'];
				$rows[$key]['22'] = $value['Chinese_address'];
				$rows[$key]['23'] = $value['English_address'];
				$rows[$key]['24'] = $value['Occupation'];
				//$rows[$key]['25'] = ($value['Role_ID'] == '2') ? 'Member' : '';
				$rows[$key]['25'] = isset($value['qualification']['qualification_' . app()->getLocale()]) ? $value['qualification']['qualification_' . app()->getLocale()] : '';
				$rows[$key]['26'] = $value['School_Name'];
				$rows[$key]['27'] = $value['Subject'];
				$rows[$key]['28'] = ($related_activity) ? implode(',', $related_activity_text) : '';
				$rows[$key]['29'] = $value['Other_experience'];
				$rows[$key]['30'] = ($value['Health_declaration'] == '1') ? 'Yes' : 'No';
				$rows[$key]['31'] = $value['Health_declaration_text'];
				$rows[$key]['32'] = $value['Emergency_contact_name'];
				$rows[$key]['33'] = $value['EmergencyContact'];
				$rows[$key]['34'] = isset($Relationship) ? $Relationship : '';
				$rows[$key]['35'] = $value['JoinDate'];
				$rows[$key]['36'] = '123';
				$rows[$key]['37'] = $value['Remarks_desc'];
				$rows[$key]['38'] = (isset($value['remark_date']) && $value['remark_date'] !='') ? date('d/m/Y', strtotime($value['remark_date'])) : '';
				$rows[$key]['39'] = isset($value['Status']) ? ($value['Status'] == 1) ? 'Active' : 'InActive' : '';
				$rows[$key]['40'] = isset($value['lastactivity']) ? $value['lastactivity'] : '';
				$rows[$key]['41'] = $value['hour_point'];
				$rows[$key]['42'] = isset($value['member_token']) ? $value['member_token'] : '';
				$rows[$key]['43'] = ($Specialty) ? implode(',', $Specialty_text) : '';
				$rows[$key]['44'] = $member_qr_code;


				/*$rows[$key]['29'] = ($remarks) ? $value['remarks']['remarks_'.app()->getLocale()] : '';*/
			}

			$all_col_name = array(
								'2' => __("languages.export_member.member_number"),
								'3' => __("languages.export_member.team"),
								'4' => __("languages.export_member.sub_team"),
								'5' => __("languages.export_member.special_instructor"),
								'6' => __("languages.export_member.special_instructor_text"),
								'7' => __("languages.export_member.team_effective_date"),
								'8' => __("languages.export_member.rank"),
								'9' => __("languages.export_member.rank_effective_date"),
								'10' => __("languages.member.Reference_number"),
								'11' => __("languages.export_member.chinese_name"),
								'12' =>  __("languages.export_member.english_name"),
								'13' => __("languages.export_member.id_number"),
								'14' =>  __("languages.export_member.gender"),
								'15' =>  __("languages.export_member.age"),
								'16' =>  __("languages.export_member.date_of_birth"),
								'17' => __("languages.export_member.nationality"),
								'18' =>  __("languages.export_member.email_address"),
								'19' => __("languages.export_member.contact_number_main"),
								'20' => __("languages.export_member.contact_number"),
								'21' => __("languages.export_member.contact_number_2"),
								'22' =>  __("languages.export_member.chinese_address"),
								'23' =>  __("languages.export_member.english_address"),
								'24' => __("languages.export_member.occupation"),
								'25' => __("languages.export_member.highest_education"),
								'26' => __("languages.export_member.school_name"),
								'27' => __("languages.export_member.subject"),
								'28' => __("languages.export_member.related_activity_experiance"),
								'29' => __("languages.export_member.other_experiance"),
								'30' =>  __("languages.export_member.helth_declaration"),
								'31' => __("languages.export_member.helth_declaration_text"),
								'32' => __("languages.export_member.emergency_contact_name"),
								'33' => __("languages.export_member.emergency_number"),
								'34' => __("languages.export_member.relationship"),
								'35' => __("languages.export_member.join_date"),
								'36' => __("languages.export_member.remark"),
								'37' => __("languages.export_member.remark_description"),
								'38' => __("languages.export_member.remark_date"),
								'39' => __("languages.export_member.status"),
								'40' => __("languages.export_member.last_activity"),
								'41' => __("languages.export_member.hour_point"),
								'42' => __("languages.export_member.Tokens"),
								'43' => __("languages.export_member.specialty"),
								'44' => __("languages.export_member.Qr_code")
							);
			$exportColumn = explode(",", $request['export_filter']);
			$i = 0;
			$final_array = [];
			$export_col_name = [];
			//print_r($request['export_filter']);die;

			foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
				$export_col_name[] = $all_col_name[$exportColumnValue];
			}

			foreach ($rows as $key => $value) {
				$newArr = [];
				foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
					$newArr[] = $value[$exportColumnValue];
				}
				$final_array[$i] = $newArr;
				$i++;
			}
			$fileName = 'tasks.csv';
			$headers = array(
				"Content-type" => "text/csv",
				"Content-Disposition" => "attachment; filename=$fileName",
				"Pragma" => "no-cache",
				"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
				"Expires" => "0",
			);
			$columns = $export_col_name;

			
			$callback = function () use ($final_array, $columns) {
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);
				foreach ($final_array as $task) {
					fputcsv($file, $task);
				}
				fclose($file);
			};
			return response()->stream($callback, 200, $headers);
		}
	}

	public function updatemembercode(){
		$Users = User::get();
		foreach($Users as $key => $User){
			if($key != 0){
				User::find($User->ID)->Update([
					'MemberCode' => str_replace("C","",$User->MemberCode)
				]);
			}
		}
	}

	/**
	 * Member List Page
	 */
	public function MemberList(Request $request){
		if(!session()->has('user')){
			return redirect('/login');
		}
		// Get all master table values
		$Ranks = SubElite::where('status', '1')->groupBy('subelite_' . app()->getLocale())->get()->toArray();
		$Teams = EilteModel::where('status', '1')->get()->toArray();
		$subteams = Subteam::where('status', '1')->get()->toArray();
		$RelatedActivityHistory = RelatedActivityHistory::where('status', '1')->get()->toArray();
		$Specialty = Specialty::where('status', '1')->get()->toArray();
		$Qualification = QualificationModel::where('status', '1')->get()->toArray();

		$items = $request->items ?? 10;
        $Query = User::sortable(['ID'=>'DESC']);
		if(isset($request->submit)){
			if(isset($request->filter_gender)){
				$Query->where('Gender',$request->filter_gender);
			}
			if(isset($request->user_status)){
				$Query->where('Status',$request->user_status);
			}
			if(isset($request->search_text)){
				$Query->Where(function($query) use($request){
					$query->where('email','Like','%'.$request->search_text.'%')
					->orWhere('UserName','Like','%'.$request->search_text.'%')
					->orWhere('English_name','Like','%'.$request->search_text.'%')
					->orWhere('Chinese_name','Like','%'.$request->search_text.'%')
					->orWhere('Contact_number','Like','%'.$request->search_text.'%')
					->orWhere('Contact_number_1','Like','%'.$request->search_text.'%')
					->orWhere('Contact_number_2','Like','%'.$request->search_text.'%');
				});
			}

			//Filter by elite team
			if (isset($request->filterelite) && !empty($request->filterelite)) {
				$Query->where('team',$request->filterelite);
			}
			// Filter by sub-team
			if (isset($request->filtersubteam) && !empty($request->filtersubteam)) {
				$Query->where('elite_team',$request->filtersubteam);
			}

			// Filter by member rank
			if (isset($request->filterrank) && !empty($request->filterrank)) {
				$Query->where('rank_team',$request->filterrank);
			}

			// Filter by date-of-birth
			if (isset($request->dateofbirth) && !empty($request->dateofbirth)) {
				$expolde_birth = array_map('trim',explode('-',$request->dateofbirth));
				$start_birth = $expolde_birth[0];
				$end_birth = $expolde_birth[1];
				$Query->whereBetween('DOB', array($start_birth, $end_birth));	
			}

			//Filter by member age
			if (isset($request->age) && !empty($request->age)) {
				$expolde_age = explode('-', $request->age);
				$Query->whereBetween('age', array($expolde_age[0], $expolde_age[1]));
			}
			
			// Filter by qualification
			if (isset($request->filterqualification) && !empty($request->filterqualification)) {
				$Query->where('Qualification',$request->filterqualification);
			}

			// Filter by join date
			if (isset($request->filterjoindate) && !empty($request->filterjoindate)) {
				$expolde_joindate = array_map('trim',explode('-', $request->filterjoindate));
				$start_joindate = $expolde_joindate[0];
				$end_joindate = $expolde_joindate[1];
				$Query->whereBetween('JoinDate', array($start_joindate, $end_joindate));
			}

			// Filter by hour points
			if (isset($request->filterhourpoint) && !empty($request->filterhourpoint)) {
				$Query->where('hour_point',$request->filterhourpoint);
			}

			// Filter by token
			if (isset($request->token)) {
				$tokenArry = explode('-', $request->token);
				if (!empty($tokenArry)) {
					$Query->where('member_token', '>=', $tokenArry[0]);
					$Query->where('member_token', '<=', $tokenArry[1]);
				}
			}

			// Filter by related acitivity
			if(isset($request->filterrealtedactivity) && !empty($request->filterrealtedactivity)){
				$idsarr = array();
				foreach ($request->filterrealtedactivity as $activitys) {
					$users = User::selectRaw('ID,Related_Activity_History')->where('Role_ID', '2')->get()->toArray();
					foreach ($users as $key => $val) {
						$activity = unserialize($val['Related_Activity_History']);
						if (!empty($activity)) {
							if (array_key_exists($activitys, $activity)) {
								$idsarr[] = $val['ID'];
							}
						}
					}
				}
				$idsarr = array_unique($idsarr);
				if(isset($idsarr) && !empty($idsarr)){
					$Query->whereIn('ID',$idsarr);
				}
			}

			// Filter by Specialty member
			if (isset($request->filterspecialty) && !empty($request->filterspecialty)) {
				$idsspecialtyarr = [];
				foreach ($request->filterspecialty as $specialtys) {
					if(isset($request->filterrank) && !empty($request->filterrank)){
						$users = User::selectRaw('ID,Specialty')->where('Role_ID', '2')->where('rank_team',$filterrank)->get()->toArray();
					}else{
						$users = User::selectRaw('ID,Specialty')->where('Role_ID', '2')->get()->toArray();
					}
					
					foreach($users as $key => $val) {
						$specialty = unserialize($val['Specialty']);
						if (!empty($specialty)) {
							if (array_key_exists($specialtys, $specialty)) {
								$idsspecialtyarr[] = $val['ID'];
							}
						}
					}
				}
				$idsspecialtyarr = array_unique($idsspecialtyarr);
				if(isset($idsspecialtyarr) && !empty($idsspecialtyarr)){
					$Query->whereIn('ID',$idsspecialtyarr);
				}
			}
		}

		$Query->where('Role_ID', '2');
		
		$userData = $Query->paginate($items);

        return view('NewMemberManagement.members_list',compact('userData','items','Ranks','Teams','subteams','RelatedActivityHistory','Specialty','Qualification'));  
    }
}