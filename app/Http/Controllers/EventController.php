<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Models\Attendance;
use App\Http\Models\EventAssignModel;
use App\Http\Models\EventPosttypeModel;
use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\MemberToken;
use App\Http\Models\MemberTokenStatus;
use App\Http\Models\MemberUsedToken;
use App\Http\Models\Settings;
use App\Http\Models\User;
use App\Jobs\SendEmailJob;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Http\Models\EventTokenManage;

class EventController extends Controller {

	public function __construct() {
		$sitesettings = Helper::getsitesettings();
		if (!empty($sitesettings->min_hour)) {
			$this->globalmin = $sitesettings->min_hour;
		} else {
			$this->globalmin = 30;
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		if (in_array('event_management_read', Helper::module_permission(Session::get('user')['role_id']))) {
			$eventTypes = new EventType;
			$get_event_type_list = $eventTypes->get_event_type_select_list_filter();
			if (Session::get('user')['role_id'] != '1') {
				$events = Events::with('eventType')->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)')->groupBy('event_code')->orderBy('id', 'desc')->get()->toArray();
			} else {
				$events = Events::with('eventType')->orderBy('id', 'DESC')->get()->toArray();
			}
			if (!empty($events)) {
				foreach ($events as $key => $value) {
					$events[$key]['scheduleData'] = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
					$scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
					$totaleventhour = 0;
					$dates = [];
					foreach ($scheduleData as $val) {
						$dates[] = $val['date'];
						if(!empty($val['event_hours']) && $val['event_hours'] != 'NaN'){
							$totaleventhour += $val['event_hours'];
						}
						
					}
					
					$events[$key]['totaleventhour'] = $totaleventhour;
					if(!empty($dates)){
						$events[$key]['event_start_date'] = date('d/m/Y', strtotime($dates[0]));
						$events[$key]['event_end_date'] = date('d/m/Y', strtotime(end($dates)));
					}else{
						$events[$key]['event_start_date'] = '';
						$events[$key]['event_end_date'] = '';
					}
				}
			}			
			return view('EventManagement.event_list', compact('events', 'get_event_type_list'));
		} else {
			return redirect('/');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		if (in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id']))) {
			$date = date('Y-m-d');
			$today_date = date('dS', strtotime($date));
			$number_today_date = date('d', strtotime($date));
			$today_day = date('l', strtotime($date));
			$number_today = date('N', strtotime($date));
			// $get_last_unique_number = Helper::getLastEventNumber();
			// $unique_id = (1 + $get_last_unique_number);
			$weekofday = $this->getWeeks($date, "sunday");
			// $eventTypes = EventType::where('status','1')->get()->toArray();
			$eventTypes = new EventType;
			$get_event_type_list = $eventTypes->get_event_type_select_list();
			return view('EventManagement.event_add', compact('today_date', 'today_day', 'number_today', 'number_today_date', 'weekofday', 'eventTypes', 'get_event_type_list'));
		} else {
			return redirect('/');
		}
	}

	public function setPostType(Request $request) {
		if ($request->postTypeID == 1) {
			echo $request->event_money;exit;
		} else if ($request->postTypeID == 2) {
			echo $request->event_token;exit;

		} else if ($request->postTypeID == 3) {
			echo $request->event_money;
			echo $request->event_token;exit;
		}
	}

	public function recurringEvent(Request $request) {
		if (empty($request->update_event_id)) {
			$allDates = explode(",", $request->event_dates);
			// $eventenddate = !empty($allDates) ? date('l,d F,Y', strtotime(max($allDates))) : '';
			// $startdate = !empty($allDates) ? date('l,d F,Y', strtotime(min($allDates))) : '';
			$eventenddate = !empty($allDates) ? Helper::FulldateFormat('/','-',max($allDates)) : '';
			$startdate = !empty($allDates) ? Helper::FulldateFormat('/','-',min($allDates))  : '';
			$events = new Events;
			$events->event_name = !empty($request->event_name) ? $request->event_name : NULL;
			$events->event_type = !empty($request->event_type) ? $request->event_type : NULL;
			$events->event_code = !empty($request->event_code) ? $request->event_code : NULL;
			$events->assessment = !empty($request->assessment) ? $request->assessment : NULL;
			$events->assessment_text = !empty($request->assessment_text) ? $request->assessment_text : NULL;
			$events->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
			$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;
			$events->startdate = !empty($allDates) ? $startdate : NULL;
			$events->enddate = !empty($allDates) ? $eventenddate : NULL;
			$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
			$events->no_of_dates = !empty($allDates) ? ($events->no_of_dates + count($allDates)) : 0;

			$events->multiple_event = !empty($request->eventselect) ? $request->eventselect : NULL;
			$events->occurs = 'Once';
			$events->status = "2";
			$result = $events->save(); // save data
			if (!empty($request->event_money)) {
				foreach ($request->event_money as $key => $money) {
					if($money){
						$eventTypeModel = new EventPosttypeModel;
						$eventTypeModel->event_id = $events->id;
						$eventTypeModel->event_code = $events->event_code;
						$eventTypeModel->post_type = 1;
						$eventTypeModel->post_value = ($money) ? $money : null;
						$eventTypeModel->save();		
					}
				}
			}
			
			if (!empty($request->event_token)) {
				foreach ($request->event_token as $key => $token) {
					if($token){
						$eventTypeModel = new EventPosttypeModel;
						$eventTypeModel->event_id = $events->id;
						$eventTypeModel->event_code = $events->event_code;
						$eventTypeModel->post_type = 2;
						$eventTypeModel->post_value = ($token) ? $token : null;
						$eventTypeModel->save();
					}
				}
			}

			if (!empty($request->event_money_token)) {
				foreach ($request->event_money_token as $key => $moneytoken) {
					if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
						$eventTypeModel = new EventPosttypeModel;
						$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
						$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
						$eventTypeModel->event_id = $events->id;
						$eventTypeModel->event_code = $events->event_code;
						$eventTypeModel->post_type = 3;
						$eventTypeModel->post_value = $money . "+" . $token;
						$eventTypeModel->save();
					}
				}
			}

			if (!empty($allDates)) {
				foreach ($allDates as $date_key => $date_value) {
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
					$EventSchedule->end_time = !empty($request->endtime) ? $request->endtime : NULL;
					$EventSchedule->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
					$EventSchedule->date = Helper::dateFormatMDY('/','-',$date_value);
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = "2";
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->save();

					$events->no_of_dates = count($allDates);
					$events->update();
				}
			}
		} else {
			$allDates = explode(",", $request->event_dates);
			
			// $eventenddate = !empty($allDates) ? date('l,d F,Y', strtotime($allDates[0])) : '';
			// $startdate = !empty($allDates) ? date('l,d F,Y', strtotime($allDates[0])) : '';
			$eventenddate = !empty($allDates) ? Helper::FulldateFormat('/','-',$allDates[0]) : '';
			$startdate = !empty($allDates) ?  Helper::FulldateFormat('/','-',$allDates[0]) : '';

			$events = Events::find($request->update_event_id);
			$events->event_name = !empty($request->event_name) ? $request->event_name : NULL;
			$events->event_type = !empty($request->event_type) ? $request->event_type : NULL;
			$events->event_code = !empty($request->event_code) ? $request->event_code : NULL;
			$events->assessment = !empty($request->assessment) ? $request->assessment : NULL;
			$events->assessment_text = !empty($request->assessment_text) ? $request->assessment_text : NULL;
			$events->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
			$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;

			$events->startdate = !empty($allDates) ? $startdate : NULL;
			$events->enddate = !empty($allDates) ? $eventenddate : NULL;

			$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
			$events->no_of_dates = !empty($allDates) ? ($events->no_of_dates + count($allDates)) : 0;

			/*$events->event_money = !empty($request->event_money) ? str_replace("HKD","",$request->event_money) : '0';*/
			$events->event_money = !empty($request->event_money) ? $request->event_money : '0';
			$events->event_token = !empty($request->event_token) ? (str_replace("HKD", "", $request->event_token)) : NULL;
			$events->multiple_event = !empty($request->eventselect) ? $request->eventselect : NULL;
			$events->occurs = 'Once';
			$events->status = "2";
			
			$result = $events->save(); // save data
			if (!empty($events)) {
				$scheduleData = EventSchedule::where('event_id', $events->id)->get();
				foreach ($scheduleData as $key => $value) {
					$updateSchedule = EventSchedule::where('id', $value->id)->first();
					$updateSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$updateSchedule->status = !empty($request->status) ? $request->status : NULL;
					$result = $updateSchedule->save();
				}
			}
			$allDates12 = explode(",", $request->event_dates);
			if (!empty($request->event_dates)) {
				foreach ($allDates12 as $date_key => $date_value) {
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
					$EventSchedule->end_time = !empty($request->endtime) ? $request->endtime : NULL;
					$EventSchedule->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
					$EventSchedule->date = Helper::dateFormatMDY('/','-',$date_value);
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = $request->status;
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->save();
				}
			}
		}
		if ($result) {
			$message = 'Event added successfully..';
			$status = true;
			$data = $events;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message, 'data' => $data]);
	}

	public function getWeeks($date, $rollover) {
		$cut = substr($date, 0, 8);
		$daylen = 86400;

		$timestamp = strtotime($date);
		$first = strtotime($cut . "00");
		$elapsed = ($timestamp - $first) / $daylen;

		$weeks = 1;

		for ($i = 1; $i <= $elapsed; $i++) {
			$dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
			$daytimestamp = strtotime($dayfind);

			$day = strtolower(date("l", $daytimestamp));

			if ($day == strtolower($rollover)) {
				$weeks++;
			}

		}

		return $weeks;
	}

	public function edit($id) {
		if (in_array('event_management_read', Helper::module_permission(Session::get('user')['role_id']))) {
			$date = date('Y-m-d');
			$today_date = date('dS', strtotime($date));
			$number_today_date = date('d', strtotime($date));
			$today_day = date('l', strtotime($date));
			$weekofday = $this->getWeeks($date, "sunday");
			$edit_event = Events::where('id', $id)->first();
			$postType = EventPosttypeModel::where('event_id', $id)->get();
			$eventTypes = new EventType;
			$get_event_type_list = $eventTypes->get_event_type_select_list($edit_event->event_type);
			if (!empty($edit_event)) {
				$scheduleData = EventSchedule::where('event_id', $id)->get()->toArray();
				if (!empty($scheduleData)) {
					$schedule_dates = array_column($scheduleData, 'date');
					$schedule_dates = array_map(function ($val) {
						return date('d/m/Y', strtotime($val));
					}, $schedule_dates);
					$edit_event['schedule_dates'] = implode(",", $schedule_dates);
				}
				return view('EventManagement.event_edit', compact('edit_event', 'postType', 'today_date', 'today_day', 'number_today_date', 'weekofday', 'eventTypes', 'get_event_type_list'));
			} else {
				return redirect('eventManagement');
			}
		} else {
			return redirect('/');
		}
	}

	public function update(Request $request, $id) {
		$isFreeEvent=false;
		$events = Events::find($id);
		/*Audit Log START*/
		$usersdata = new Events;
		$post_value = $request->all();

		$allDates = explode(",", $request->event_dates);

		Helper::AuditLogfuncation($post_value, $usersdata, 'id', $id, 'events', 'Event');
		/*Audit Log END*/
		$events->event_name = !empty($request->event_name) ? $request->event_name : NULL;
		$events->event_type = !empty($request->event_type) ? $request->event_type : NULL;
		$events->event_code = !empty($request->event_code) ? $request->event_code : NULL;
		$events->assessment = !empty($request->assessment) ? $request->assessment : NULL;
		$events->assessment_text = !empty($request->assessment_text) ? $request->assessment_text : NULL;
		$events->event_money = !empty($request->event_money) ? $request->event_money : '0';
		$events->event_token = !empty($request->event_token) ? (str_replace("HKD", "", $request->event_token)) : NULL;
		$events->status = isset($request->status) ? $request->status : "0";
		$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
		$oldDates = explode(',',$request->old_event_dates);
		$resultDate = array_diff($allDates,$oldDates);
		$events->no_of_dates = !empty($resultDate) ? ($events->no_of_dates + count($resultDate)) :  count($oldDates);

		if(isset($request->post_type) && empty($request->post_type)){
			$isFreeEvent=true;
			$events->is_free_event=1;
		}
		$result = $events->update(); // save data
		if($result){
			EventSchedule::where('event_id',$id)->update(['status' => $request->status]);
		}

		// $allDates = explode(",", $request->event_dates);

		if (!empty($request->event_dates) && $request->status != '1') {
			foreach ($allDates as $date_key => $date_value) {
				$date_value = Helper::dateFormatMDY('/','-',$date_value);
				if(EventSchedule::where('event_id',$id)->where('date',$date_value)->exists()){
					$EventSchedule = EventSchedule::where('event_id',$id)->where('date',$date_value)->first();
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->status = !empty($request->status) ? $request->status : NULL;
					$EventSchedule->update();
				}else{
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $id;
					$EventSchedule->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
					$EventSchedule->end_time = !empty($request->endtime) ? $request->endtime : NULL;
					$EventSchedule->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
					$EventSchedule->date = $date_value;
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = $request->status;
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->save();
				}
			}
		}

		$checkEventCost = DB::table('event_post_type')->where('event_id', $request->event_main_id)->first();
		if (empty($checkEventCost)) {
			if (!empty($request->event_money)) {
				foreach ($request->event_money as $key => $money) {
					if($money){
						$eventTypeModel = new EventPosttypeModel;
						$eventTypeModel->event_id = $request->event_main_id;
						$eventTypeModel->event_code = $request->event_code;
						$eventTypeModel->post_type = 1;
						$eventTypeModel->post_value = ($money) ? $money : null;
						$eventTypeModel->save();		
					}
				}
			}
			if (!empty($request->event_token)) {
				foreach ($request->event_token as $key => $token) {
					if($token){
						$eventTypeModel = new EventPosttypeModel;
						$eventTypeModel->event_id = $request->event_main_id;
						$eventTypeModel->event_code = $request->event_code;
						$eventTypeModel->post_type = 2;
						$eventTypeModel->post_value = ($token) ? $token : null;
						$eventTypeModel->save();
					}
				}
			}

			if (!empty($request->event_money_token)) {
				foreach ($request->event_money_token as $key => $moneytoken) {
					if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
						$eventTypeModel = new EventPosttypeModel;
						$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
						$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
						$eventTypeModel->event_id = $request->event_main_id;
						$eventTypeModel->event_code = $request->event_code;
						$eventTypeModel->post_type = 3;
						$eventTypeModel->post_value = $money . "+" . $token;
						$eventTypeModel->save();
					}
				}
			}
			
		} else {
			$delete = DB::table('event_post_type')->where('event_id', $request->event_main_id)->delete();
			if ($delete) {

				if(!empty($request->event_money)) {
					foreach ($request->event_money as $key => $money) {
						if($money){
							$eventTypeModel = new EventPosttypeModel;
							$eventTypeModel->event_id = $request->event_main_id;
							$eventTypeModel->event_code = $request->event_code;
							$eventTypeModel->post_type = 1;
							$eventTypeModel->post_value = ($money) ? $money : null;
							$eventTypeModel->save();		
						}
					}
				}


				if(!empty($request->event_token)) {
					foreach ($request->event_money as $key => $token) {
						if($token){
							$eventTypeModel = new EventPosttypeModel;
							$eventTypeModel->event_id = $request->event_main_id;
							$eventTypeModel->event_code = $request->event_code;
							$eventTypeModel->post_type = 2;
							$eventTypeModel->post_value = ($token) ? $token : null;
							$eventTypeModel->save();
						}
					}
				}

				if(!empty($request->event_money_token)) {
					foreach ($request->event_money_token as $key => $moneytoken) {
						if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
							$eventTypeModel = new EventPosttypeModel;
							$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
							$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
							$eventTypeModel->event_id = $request->event_main_id;
							$eventTypeModel->event_code = $request->event_code;
							$eventTypeModel->post_type = 3;
							$eventTypeModel->post_value = $money . "+" . $token;
							$eventTypeModel->save();
						}
					}
				}
			}
		}
		if ($result) {
			return redirect('eventManagement')->with('success_msg', 'Events updated successfully.');
		} else {
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	public function submitEvent(Request $request) {
		$isFreeEvent = false;
		if (!empty($request->update_event_id)) {
			$allDates = explode(",", $request->old_event_dates);

			$maxDate = date('d-m-Y', strtotime(str_replace('/', '-', max($allDates))));
			$minDate = date('d-m-Y', strtotime(str_replace('/', '-', min($allDates))));
			// $eventenddate = !empty($allDates) ? date('l,d F,Y', strtotime($maxDate)) : '';
			// $startdate = !empty($allDates) ? date('l,d F,Y', strtotime($minDate)) : '';
			$eventenddate = !empty($allDates) ? Helper::FulldateFormat('/','-',$maxDate) : '';
			$startdate = !empty($allDates) ? Helper::FulldateFormat('/','-',$minDate) : '';

			$events = Events::find($request->update_event_id);
			$events->event_name = !empty($request->event_name) ? $request->event_name : NULL;
			$events->event_type = !empty($request->event_type) ? $request->event_type : NULL;
			$events->event_code = !empty($request->event_code) ? $request->event_code : NULL;
			$events->assessment = !empty($request->assessment) ? $request->assessment : NULL;
			$events->assessment_text = !empty($request->assessment_text) ? $request->assessment_text : NULL;
			$events->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
			$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;
			$events->startdate = !empty($allDates) ? $startdate : NULL;
			$events->enddate = !empty($allDates) ? $eventenddate : NULL;
			$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
			// $events->no_of_dates = !empty($request->event_dates) ? ($events->no_of_dates + count($request->event_dates)) : 0;
			if(isset($request->post_type) && empty($request->post_type)){
				$isFreeEvent = true;
				$events->is_free_event = 1;
			}
			/*$events->event_money = !empty($request->event_money) ? str_replace("HKD","",$request->event_money) : '0';*/
			$events->event_money = !empty($request->event_money) ? $request->event_money : '0';
			$events->event_token = !empty($request->event_token) ? (str_replace("HKD", "", $request->event_token)) : NULL;
			$events->multiple_event = !empty($request->eventselect) ? $request->eventselect : NULL;
			$events->occurs = 'Once';
			$events->status = "2";
			$result = $events->save(); // save data
			if (!empty($events)) {
				$scheduleData = EventSchedule::where('event_id', $events->id)->get();
				foreach ($scheduleData as $key => $value) {
					$updateSchedule = EventSchedule::where('id', $value->id)->first();
					$updateSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$updateSchedule->status = "2";
					$result = $updateSchedule->save();
				}

				if($isFreeEvent==false){
					// Add events Money
					if (!empty($request->event_money)) {
						foreach ($request->event_money as $key => $money) {
							if($money){
								$eventTypeModel = new EventPosttypeModel;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 1;
								$eventTypeModel->post_value = ($money) ? $money : null;
								$eventTypeModel->save();		
							}
						}
					}
					
					// Add events Tokens
					if (!empty($request->event_token)) {
						foreach ($request->event_token as $key => $token) {
							if($token){
								$eventTypeModel = new EventPosttypeModel;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 2;
								$eventTypeModel->post_value = ($token) ? $token : null;
								$eventTypeModel->save();
							}
						}
					}
		
					// Add Event money + token
					if (!empty($request->event_money_token)) {
						foreach ($request->event_money_token as $key => $moneytoken) {
							if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
								$eventTypeModel = new EventPosttypeModel;
								$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
								$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 3;
								$eventTypeModel->post_value = $money . "+" . $token;
								$eventTypeModel->save();
							}
						}
					}
				}
			}
			$allDates12 = explode(",", $request->event_dates);
			
			$events->no_of_dates = !empty($request->event_dates) ? ($events->no_of_dates + count($allDates12)) : 0;
			$events->save();

			if (!empty($request->event_dates)) {

				foreach ($allDates12 as $date_key => $date_value) {
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
					$EventSchedule->end_time = !empty($request->endtime) ? $request->endtime : NULL;
					$EventSchedule->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
					//$EventSchedule->date = date('m/d/Y', strtotime($date_value));
					$EventSchedule->date = Helper::dateFormatMDY('/','-',$date_value);
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = ($request->status != "") ? $request->status : '2';
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->save();
				}
			}
		} else {
			$allDates = explode(",", $request->event_dates);
			$eventenddate = !empty($allDates) ? Helper::FulldateFormat('/','-',$allDates[0]) : '';
			$startdate = !empty($allDates) ? Helper::FulldateFormat('/','-',$allDates[0]) : '';

			$events = new Events;
			$events->event_name = !empty($request->event_name) ? $request->event_name : NULL;
			$events->event_type = !empty($request->event_type) ? $request->event_type : NULL;
			$events->event_code = !empty($request->event_code) ? $request->event_code : NULL;
			$events->assessment = !empty($request->assessment) ? $request->assessment : NULL;
			$events->assessment_text = !empty($request->assessment_text) ? $request->assessment_text : NULL;
			$events->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
			$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;
			$events->startdate = !empty($allDates) ? $startdate : NULL;
			$events->enddate = !empty($allDates) ? $eventenddate : NULL;
			$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
			$events->no_of_dates = !empty($allDates) ? count($allDates) : 0;
			if(isset($request->post_type) && empty($request->post_type)){
				$isFreeEvent = true;
				$events->is_free_event = 1;
			}
			//$events->event_money = !empty($request->event_money) ? $request->event_money : '0';
			//$events->event_token = !empty($request->event_token) ? (str_replace("HKD", "", $request->event_token)) : NULL;
			$events->multiple_event = !empty($request->eventselect) ? $request->eventselect : NULL;
			$events->occurs = 'Once';
			$events->status = "2";
			$result = $events->save(); // save data
			if (!empty($allDates)) {
				foreach ($allDates as $date_key => $date_value) {
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->start_time = !empty($request->eventstarttime) ? $request->eventstarttime : NULL;
					$EventSchedule->end_time = !empty($request->endtime) ? $request->endtime : NULL;
					$EventSchedule->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
					$EventSchedule->date = Helper::dateFormatMDY('/','-',$date_value);
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = "2";
					$EventSchedule->event_code = !empty($request->event_code) ? $request->event_code : NULL;
					$EventSchedule->save();
				}
			}
			if($result){
				if($isFreeEvent==false){
					if (!empty($request->event_money)) {
						foreach ($request->event_money as $key => $money) {
							if($money){
								$eventTypeModel = new EventPosttypeModel;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 1;
								$eventTypeModel->post_value = ($money) ? $money : null;
								$eventTypeModel->save();		
							}
						}
					}
					
					if (!empty($request->event_token)) {
						foreach ($request->event_token as $key => $token) {
							if($token){
								$eventTypeModel = new EventPosttypeModel;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 2;
								$eventTypeModel->post_value = ($token) ? $token : null;
								$eventTypeModel->save();
							}
						}
					}
		
					if (!empty($request->event_money_token)) {
						foreach ($request->event_money_token as $key => $moneytoken) {
							if ($moneytoken['money'] != "" && $moneytoken['token'] != "") {
								$eventTypeModel = new EventPosttypeModel;
								$money = ($moneytoken['money']) ? $moneytoken['money'] : 0;
								$token = ($moneytoken['token']) ? $moneytoken['token'] : 0;
								$eventTypeModel->event_id = $events->id;
								$eventTypeModel->event_code = $events->event_code;
								$eventTypeModel->post_type = 3;
								$eventTypeModel->post_value = $money . "+" . $token;
								$eventTypeModel->save();
							}
						}
					}
				}
			}
		}
		if ($result) {
			return redirect('eventManagement')->with('success_msg', 'Event added successfully..');
		} else {
			return back()->with('error_msg', 'Something went wrong.');
		}
	}

	public function destroy($id) {
		if (in_array('event_management_delete', Helper::module_permission(Session::get('user')['role_id']))) {
			$events = Events::where('id', $id)->delete();
			$deleteevents = EventSchedule::where('event_id', $id)->delete();
			if ($events) {
				$message = 'Event deleted successfully..';
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

	/**
	 * USE : Delete Post type value from event
	 */
	public function deletePostType(Request $request, $postType){
		if($postType){
			$delete = EventPosttypeModel::where('event_id',$request->event_id)->where('id',$request->post_id)->delete();
			if ($delete) {
				$message = 'Cost Type deleted successfully..';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}
			return response()->json(['status' => $status, 'message' => $message]);
		}
	}

	public function deleteEventSchedule($id) {
		$scheduleData = EventSchedule::where('id', $id)->first();

		if (in_array('event_management_delete', Helper::module_permission(Session::get('user')['role_id']))) {
			$deleteevents = EventSchedule::where('id', $id)->delete();
			if ($deleteevents) {
				$schedules = EventSchedule::where('event_id', $scheduleData->event_id)->get()->toArray();
				if (!empty($schedules)) {
					$schedules = array_column($schedules, 'date');
					$schedules = array_map(function ($val) {
						return date('d/m/Y', strtotime($val));
					}, $schedules);

					$old_dates = implode(",", $schedules);

				}

				$message = 'Event deleted successfully..';
				$status = true;
				$old_dates = $old_dates;
			} else {
				$message = 'Please try again';
				$status = false;
				$old_dates = '';
			}
			return response()->json(['status' => $status, 'message' => $message, 'old_dates' => $old_dates]);
		} else {
			$message = 'You do not have permission.';
			$status = false;
			return response()->json(['status' => $status, 'message' => $message]);
		}
	}

	public function Eventget() {
		if (Session::get('user')['role_id'] != '1') {
			$eventget = Events::whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)')->get()->toArray();
		} else {
			$eventget = Events::get()->toArray();
			if (!empty($eventget)) {
				foreach ($eventget as $key => $value) {
					$eventget[$key]['scheduleData'] = EventSchedule::select('date', 'start_time', 'end_time')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
				}
			}
		}
		echo json_encode($eventget);
	}

	public function reRescheduleEvent(Request $request) {
		$eventid = $request->eventid;
		$RescheduleEvent = Events::selectRaw('id,startdate,start_time,end_time')->find($eventid);
		$RescheduleEvent->startdate = $request->reeventstartdate;
		$RescheduleEvent->start_time = $request->reeventstarttime;
		$RescheduleEvent->end_time = $request->reeventeventendtime;
		$result = $RescheduleEvent->save();
		if ($result) {
			$message = 'Event Reschedule successfully..';
			$status = true;
			$data = $RescheduleEvent;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message, 'data' => $data]);

	}

	public function eventstatusUpdate(Request $request, $id) {
		$eventStatus = Events::find($id);
		$eventStatus->status = $request->eventstaus;
		$result = $eventStatus->save();
		$EventSchedule = EventSchedule::where('event_id', $id)->update(['status' => $request->eventstaus]);
		if ($result) {
			if ($request->eventstaus == '4') {
				$attendance = Attendance::where('event_id', $id)->where('out_time', '!=', '-')->where('hours', '!=', '-')->get();
				if (!$attendance->isEmpty()) {
					$attendance = $attendance->toArray();
					/*$users = array_column($attendance, 'user_id');*/
					foreach ($attendance as $key => $value) {

						$deduct_hour = 0;
						if ($value['late_min'] != NULL && $this->globalmin < $value['late_min']) {
							$deduct_hour++;
						}
						if ($this->globalmin < $value['early_min']) {
							$deduct_hour++;
						}
						$deduct_hour = $deduct_hour . ':00';

						$diff_deduct_minit = abs(strtotime($value['hours']) - strtotime($deduct_hour)) / 60;
						$diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);

						$Setting = Settings::first();
						// Add Member tokens 'Per Hours 1 Token Incresed'
						$MemberToken = new MemberToken;
						$MemberToken->user_id = !empty($value['user_id']) ? $value['user_id'] : NULL;
						$MemberToken->event_id = !empty($id) ? $id : NULL;
						$MemberToken->token = (date('H', strtotime($diff_deduct_hour)));
						$MemberToken->remaining_token = (date('H', strtotime($diff_deduct_hour)));
						$MemberToken->expired_at = date('Y-m-d h:i:s', strtotime('+' . $Setting->token_expire_day . ' days'));
						$saveMemberToken = $MemberToken->save();

						$MemberTokenStatus = MemberTokenStatus::where('user_id', $value['user_id'])->first();
						if (!empty($MemberTokenStatus)) {
							$MemberTokenStatus->total_token = ($MemberTokenStatus->total_token + date('H', strtotime($diff_deduct_hour)));
							$MemberTokenStatus->save();
						} else {
							$MemberTokenStatus = new MemberTokenStatus;
							$MemberTokenStatus->user_id = !empty($value['user_id']) ? $value['user_id'] : NULL;
							$MemberTokenStatus->total_token = (date('H', strtotime($diff_deduct_hour)));
							$saveMemberToken = $MemberTokenStatus->save();
						}

					}
				}
			}
			$message = 'Event Status Updated successfully..';
			$status = true;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);
	}

	public function EventReport(Request $request, $id) {
		$eventReport = Attendance::where('event_id', $id)->with('event_reports')->with('users')->get()->toArray();
		$hourtotal = Attendance::where('event_id', $id)->sum('hours');
		$events = Events::where('id', $id)->first()->toArray();
		return view('EventManagement.event_report', compact('eventReport', 'events', 'hourtotal'));
	}

	/**
	 * USE : Admin can assign event to users then check users token is available or not
	 */
	public function countUsersExistingTokens($user_id){
		$currentDate = date('Y-m-d');
		$ExpireDate = date('Y-m-d', strtotime('-30days'));
		// Get the count og user available tokens
		$CountEventTotalToken = EventTokenManage::where('user_id',$user_id)->where('status','active')->sum('remaining_token');
		$MemberData = User::find($user_id);
		if(isset($MemberData) && !empty($MemberData->member_token)){
			$CountEventTotalToken = ($CountEventTotalToken + $MemberData->member_token);
		}
		return $CountEventTotalToken;
	}

	/**
	 * USE : After admin can assign event to user then update tokens users
	 */
	public function updateUsersEventTokens($request, $user_id, $eventId){
		$postType = EventPosttypeModel::where('event_id',$eventId)->where('post_type', $request->postType)->first('post_value');
		if(!empty($postType)){
			if($request->postType == 2){  // Post type = 2 = Tokens
				$requiredEventToken = $postType->post_value;
			}
			if($request->postType == 3){  // Post type = 3 = Money + Tokens 
				$moneyTokenvalue = explode("+", $postType->post_value);
				$requiredEventToken = ($moneyTokenvalue[1]) ? $moneyTokenvalue[1] : 0;
			}
		}
		
		$currentDate = date('Y-m-d');
		$remainingToken = $requiredEventToken;
		// Get the all active token data
		$EventTokenData = EventTokenManage::where('user_id',$user_id)->whereDate('expire_Date', '>=', $currentDate)->where('status','active')->get();
		if(!empty($EventTokenData)){
			foreach($EventTokenData as $TokenData){
				// After Required remaining token is 0 then stop foreach loop
				if($remainingToken == 0){
					break;
				}
				if($remainingToken != 0){
					if(!empty($TokenData->remaining_token)){
						if($remainingToken <= $TokenData->remaining_token){
							$usedToken = ($TokenData->used_token + $remainingToken);
							$userRemainToken = ($TokenData->remaining_token - $remainingToken);
							$remainingToken = 0;
							// Update used token status
							if(!empty($userRemainToken)){
								EventTokenManage::where('id',$TokenData->id)->update(['used_token' => $usedToken, 'remaining_token' => $userRemainToken]);
							}else{
								EventTokenManage::where('id',$TokenData->id)->update(['used_token' => $usedToken, 'remaining_token' => $userRemainToken, 'status' => 'expired']);
							}
						}
						if($TokenData->remaining_token <= $remainingToken){
							$usedToken = $TokenData->remaining_token;
							$userRemainToken = 0;
							$remainingToken = ($remainingToken - $TokenData->remaining_token);
							// Update used token status
							EventTokenManage::where('id',$TokenData->id)->update(['used_token' => $usedToken, 'remaining_token' => $userRemainToken, 'status' => 'expired']);
						}
					}
				}
			}
		}
	}

	public function eventAssignUser(Request $request) {
		$eventid = $request->eventid;
		$assingUser = $request->user_id;
		$events = Events::where('event_code', $eventid)->get();
		$assignExisting = false;
		foreach ($events as $val) {
			$events_type = Events::with('eventType')->find($val->id)->toArray();
			$events = Events::find($val->id);
			$event_type = 'event_type_name_' . app()->getLocale();
			$event_name = $events->event_name;
			$event_type = $events_type['event_type'][$event_type];
			$startdate = $events->startdate;
			$enddate = $events->enddate;
			$starttime = $events->start_time;
			$endtime = $events->end_time;
			/*if(empty($events->event_assign_user)){
					$events->event_assign_user = implode(',', $assingUser);
				}else{
					array_push($assingUser,$events->event_assign_user);
					$events->event_assign_user = implode(',', array_unique($assingUser));
			*/
			$result = $events->save();

			if (!empty($assingUser)) {
				foreach ($assingUser as $key => $user_id) {
					// Check Event is already assign to users or not
					$AlreadyAssigned = EventAssignModel::where('user_id', $user_id)->where('event_id', $val->id)->first();
					if(empty($AlreadyAssigned)){
						$eventId = $val->id;
						if ($request->postType) {
							$postType = EventPosttypeModel::where('event_id', $val->id)->where('post_type', $request->postType)->first('post_value');
							$userMoney = User::where('ID', $user_id)->first();
							if ($request->postType == 1) { // Post Type 1 = Money
								$eventAssign = EventAssignModel::where('user_id', $user_id)->where('event_id', $val->id)->first();
								if (empty($eventAssign)) {
									$assign = new EventAssignModel;
									$assign->event_id = !empty($val->id) ? $val->id : NULL;
									$assign->user_id = !empty($user_id) ? $user_id : NULL;
									$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
									$assign->cost_type = !empty($request->postType) ? $request->postType : NULL;
									$assign->cost_type_id = !empty($request->posttypeId) ? $request->posttypeId : NULL;
									$assign->save();
									$id = $assign->id;
									self::eventAssignLog($user_id, $id, $event_name, $event_type);

									// Member used money
									$MemberUsedMoney = new MemberUsedToken;
									$MemberUsedMoney->user_id = !empty($user_id) ? $user_id : NULL;
									$MemberUsedMoney->event_id = !empty($val->id) ? $val->id : NULL;
									$MemberUsedMoney->money = !empty($postType->post_value) ? $postType->post_value : NULL;
									$saveMemberUsedMoney = $MemberUsedMoney->save();
									// if($saveMemberUsedMoney){
									// 	$remaningMoney = $userMoney->total_money - $postType->post_value;
									// 	User::where('ID' , '=' , $user_id)->update(['total_money' => $remaningMoney]);
									// }

									$assignExisting = true;
								}

							} else if ($request->postType == 2) { // Post Type 2 = Token
								//if ($userMoney->member_token != "" && $userMoney->member_token != 0 && $postType->post_value <= $userMoney->member_token) {
								if ($postType->post_value <= $this->countUsersExistingTokens($user_id)) {
									/** Start Update User tokens **/
									$this->updateUsersEventTokens($request, $user_id, $eventId);
									/** End Update User tokens **/
									$eventAssign = EventAssignModel::where('user_id', $user_id)->where('event_id', $val->id)->first();
									if (empty($eventAssign)) {
										$assign = new EventAssignModel;
										$assign->event_id = !empty($val->id) ? $val->id : NULL;
										$assign->user_id = !empty($user_id) ? $user_id : NULL;
										$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
										$assign->cost_type = !empty($request->postType) ? $request->postType : NULL;
										$assign->cost_type_id = !empty($request->posttypeId) ? $request->posttypeId : NULL;
										$assign->save();
										$id = $assign->id;
										self::eventAssignLog($user_id, $id, $event_name, $event_type);

										// Member used token
										$MemberUsedToken = new MemberUsedToken;
										$MemberUsedToken->user_id = !empty($user_id) ? $user_id : NULL;
										$MemberUsedToken->event_id = !empty($val->id) ? $val->id : NULL;
										$MemberUsedToken->token = !empty($postType->post_value) ? $postType->post_value : NULL;
										$saveMemberUsedToken = $MemberUsedToken->save();
										if ($saveMemberUsedToken) {
											$remaningToken = $userMoney->member_token - $postType->post_value;
											User::where('ID', '=', $user_id)->update(['member_token' => $remaningToken]);
										}
										$assignExisting = true;
									}
								} else {
									return response()->json(['status' => false, 'message' => "No enough token."]);
								}
							} else if ($request->postType == 3) { // Post Type 3 = Money + Token
								$moneyTokenvalue = explode("+", $postType->post_value);

								//if ($userMoney->member_token != "" && $userMoney->member_token != 0 && $moneyTokenvalue[1] <= $userMoney->member_token) {
								if($moneyTokenvalue[1] <= $this->countUsersExistingTokens($user_id)) {
									/** Start Update User tokens **/
									$this->updateUsersEventTokens($request, $user_id, $eventId);
									/** End Update User tokens **/
									$eventAssign = EventAssignModel::where('user_id', $user_id)->where('event_id', $val->id)->first();
									if (empty($eventAssign)){
										$assign = new EventAssignModel;
										$assign->event_id = !empty($val->id) ? $val->id : NULL;
										$assign->user_id = !empty($user_id) ? $user_id : NULL;
										$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
										$assign->cost_type = !empty($request->postType) ? $request->postType : NULL;
										$assign->cost_type_id = !empty($request->posttypeId) ? $request->posttypeId : NULL;
										$assign->save();
										$id = $assign->id;
										self::eventAssignLog($user_id, $id, $event_name, $event_type);

										// Member used money + token
										$MemberUsedMoneyToken = new MemberUsedToken;
										$MemberUsedMoneyToken->user_id = !empty($user_id) ? $user_id : NULL;
										$MemberUsedMoneyToken->event_id = !empty($val->id) ? $val->id : NULL;
										$MemberUsedMoneyToken->money = !empty($moneyTokenvalue[0]) ? $moneyTokenvalue[0] : NULL;
										$MemberUsedMoneyToken->token = !empty($moneyTokenvalue[1]) ? $moneyTokenvalue[1] : NULL;
										$saveMemberUsedMoneyToken = $MemberUsedMoneyToken->save();
										if ($saveMemberUsedMoneyToken) {
											//$remaningMoney = $userMoney->total_money - $moneyTokenvalue[0];
											$remaningToken = $userMoney->member_token - $moneyTokenvalue[1];
											//User::where('ID' , '=' , $user_id)->update(['total_money' => $remaningMoney,'member_token' => $remaningToken]);
											User::where('ID', '=', $user_id)->update(['member_token' => $remaningToken]);
										}
										$assignExisting = true;
									}
								} else {
									return response()->json(['status' => false, 'message' => "No enough token."]);
								}
							}
						} else {
							$eventAssign = EventAssignModel::where('user_id', $user_id)->where('event_id', $val->id)->first();
							if (empty($eventAssign)) {
								$assign = new EventAssignModel;
								$assign->event_id = !empty($val->id) ? $val->id : NULL;
								$assign->user_id = !empty($user_id) ? $user_id : NULL;
								$assign->remark = !empty($request->remarks) ? $request->remarks : NULL;
								$assign->save();
								$id = $assign->id;
								self::eventAssignLog($user_id, $id, $event_name, $event_type);
								$assignExisting = true;
							}
						}
					}else{
						$assignExisting = false;
					}
				}
			}
		}
		try {
			foreach ($assingUser as $val) {
				$user = User::selectRaw('ID,email,UserName,English_name,Chinese_name')->where("ID", $val)->first();
				if ($user->UserName != '') {
					$name = $user->UserName;
				} else {
					$name = $user->Chinese_name . ' & ' . $user->English_name;
				}

				$dataarr = array(
					'name' => $name,
					'email' => $user->email,
					'event_name' => $event_name,
					'event_type' => $event_type,
					'start_date' => $startdate,
					'end_date' => $enddate,
					'start_time' => $starttime,
					'end_time' => $endtime,
				);

				/** Email Functionality **/
				$EmailData['subject'] = 'Event Invitation';
				$EmailData['email'] = $user->email;
				$EmailData['data'] = $dataarr;
				$EmailData['emailpage'] = 'email.eventassignuser';
				// Email sent using Queue Job
				dispatch(new SendEmailJob($EmailData));

				/** Email functionality end **/

				//$usersemail = $user->email;
				// $sendMail = Mail::send('email.eventassignuser',$dataarr, function ($message) use ($dataarr,$usersemail) {
				// 	$message->subject('Event Invitation');
				// 	$message->to($usersemail);
				// });
			}
			if ($assignExisting) {
				return response()->json(['status' => true, 'message' => "Assign User successfully."]);
			} else {
				return response()->json(['status' => false, 'message' => "Assign user allready exists."]);
			}

		} catch (Exception $e) {
			return response()->json(['status' => false, 'message' => "Something went wrong.", 'data' => $e]);
		}
	}

	public function recurringeventEdit($id, $date = NULL) {

		$recurringEvent = Events::find($id)->toArray();
		if (!empty($recurringEvent)) {
			$date = date("m/d/Y", strtotime($date));
			$recurringEvent['eventScheduleData'] = EventSchedule::where('event_id', $id)->where('date', $date)->first()->toArray();
			return response()->json(['status' => true, 'message' => "Get Event successfully.", 'response' => $recurringEvent]);
		} else {
			return response()->json(['status' => false, 'message' => "Something went wrong."]);
		}
	}

	/*public function recurringeventUpdate(Request $request){
		$events = Events::find($request->eventid);
		$eventstartdate = !empty($request->eventstartdate) ? DateTime::createFromFormat('d F, Y', $request->eventstartdate) : '';
		$eventenddate = !empty($request->eventenddate) ? DateTime::createFromFormat('d F, Y', $request->eventenddate) : '';
		$events->startdate = !empty($request->eventstartdate) ? $eventstartdate->format('l,d F,Y') : NULL;
		$events->start_time = !empty($request->starttime) ? $request->starttime : NULL;
		if($request->occurs == 'Once'){
			$events->enddate = !empty($request->eventstartdate) ? $eventstartdate->format('l,d F,Y') : NULL;
		}else{
			$events->enddate = !empty($request->eventenddate) ? $eventenddate->format('l,d F,Y') : NULL;
		}
		$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;
		$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
		$events->occurs = !empty($request->occurs) ? $request->occurs : NULL;
		$events->occurs_weekly = !empty($request->weekly_occurs) ? implode(',', $request->weekly_occurs) : NULL;
		$events->weekly_date = !empty($request->weeklydates) ?  $request->weeklydates : NULL;
		$events->occurs_monthly = !empty($request->monthly_occurs) ? $request->monthly_occurs : NULL;
		if(!empty($request->monthweekdate)){
			$events->monthweekdate = !empty($request->monthweekdate) ? $request->monthweekdate : NULL;
		}else{
			$events->monthweekdate = !empty($request->monthdates) ? $request->monthdates : NULL;
		}
		$events->daily_date = !empty($request->dailydates) ? $request->dailydates : NULL;
		$events->status = isset($request->status) ? $request->status : "0";
		$result = $events->save();  // save data
		if($request->occurs == 'Once'){
			if(!empty($request->eventstartdate)){
				$deletOnceeevents = EventSchedule::where('event_id',$request->eventid)->delete();
				$EventSchedule = new EventSchedule();
				$EventSchedule->event_id = $events->id;
				$EventSchedule->date = date('m/d/Y',strtotime($request->eventstartdate));
				$EventSchedule->occurs = $request->occurs;
				$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
				$EventSchedule->status = isset($request->status) ? $request->status : "0";
				$EventSchedule->save();
			}
		}
		if($request->occurs == 'Daily'){
			if(!empty($request->dailydates)){
				$daily_dates = explode(",", $request->dailydates);
				$deletdailyeevents = EventSchedule::where('event_id',$request->eventid)->delete();
				foreach($daily_dates as $row){
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->date = $row;
					$EventSchedule->occurs = $request->occurs;
					$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
					$EventSchedule->status = isset($request->status) ? $request->status : "0";
					$EventSchedule->save();
				}
			}
		}
		if($request->occurs == 'Weekly'){
			if(!empty($request->weeklydates)){
				$deleteevents = EventSchedule::where('event_id',$request->eventid)->delete();
				$weekly_dates = !empty($request->weeklydates) ? explode(",", $request->weeklydates) : '';
				foreach($weekly_dates as $val){
					$EventSchedule = new EventSchedule;
					$EventSchedule->event_id = $events->id;
					$EventSchedule->date = $val;
					$EventSchedule->occurs = $request->occurs;
					$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
					$EventSchedule->status = isset($request->status) ? $request->status : "0";
					$EventSchedule->save();
				}
			}
		}
		if($request->occurs == 'Monthly'){
			if(!empty($request->monthweekdate)){
				$montlyDates = explode(",", $request->monthweekdate);
				array_push($montlyDates,date('m/d/Y',strtotime($request->eventstartdate)));
				$deletemontlyevents = EventSchedule::where('event_id',$request->eventid)->delete();
				foreach($montlyDates as $value){
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->date = $value;
					$EventSchedule->occurs = $request->occurs;
					$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
					$EventSchedule->status = isset($request->status) ? $request->status : "0";
					$EventSchedule->save();
				}

			}else{
				$deletemontlyevents = EventSchedule::where('event_id',$request->eventid)->delete();
				$EventSchedule = new EventSchedule();
				$EventSchedule->event_id = $events->id;
				$EventSchedule->date = date('m/d/Y',strtotime($request->eventstartdate));
				$EventSchedule->occurs = $request->occurs;
				$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
				$EventSchedule->status = isset($request->status) ? $request->status : "0";
				$EventSchedule->save();
			}
			if(!empty($request->monthdates)){
				$monthlyeditdates =  explode(",", $request->monthdates);
				foreach($monthlyeditdates as $value){
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $events->id;
					$EventSchedule->date = $value;
					$EventSchedule->occurs = $request->occurs;
					$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
					$EventSchedule->status = isset($request->status) ? $request->status : "0";
					$EventSchedule->save();
				}
			}else{
				$EventSchedule = new EventSchedule();
				$EventSchedule->event_id = $events->id;
				$EventSchedule->date = date('m/d/Y',strtotime($request->eventstartdate));
				$EventSchedule->occurs = $request->occurs;
				$EventSchedule->event_code = !empty($events->event_code) ? $events->event_code : NULL;
				$EventSchedule->status = isset($request->status) ? $request->status : "0";
				$EventSchedule->save();
			}
		}
		if($result){
			$message = 'Event updated successfully..';
			$status = true;
			$data = $events;
		}else{
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status,'message' => $message,'data'=>$data]);
	}*/

	public function recurringeventUpdate(Request $request) {

		$events = EventSchedule::find($request->eventscheduleid);

		$events->start_time = !empty($request->starttime) ? $request->starttime : NULL;
		$events->end_time = !empty($request->endtime) ? $request->endtime : NULL;
		$events->event_hours = !empty($request->eventhours) ? $request->eventhours : NULL;
		$result = $events->save(); // save data

		if ($result) {
			$message = 'Event updated successfully..';
			$status = true;
			$data = $events;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message, 'data' => $data]);
	}

	public function eventListSearch(Request $request){
		$filter_date_event = !empty($request->filter_date_event) ? $request->filter_date_event : '';
		$filter_event_type = !empty($request->filter_event_type) ? $request->filter_event_type : '';
		$filter_occurs = !empty($request->filter_occurs) ? $request->filter_occurs : '';
		$html = '';
		//HTML
		$html .= '<table id="search-eventtable" class="table">
					<thead>
						<tr>
							<th>
								<input type="checkbox" name="eventIds[]" class="select-all-event-chkbox" value="all">
							</th>
							<th>' . __('languages.event.Event Name') . '</th>
							<th>' . __('languages.event.Event Type') . '</th>
							<th>' . __('languages.event.Event_code') . '</th>
							<th>' . __('languages.event.Start Date') . '</th>
							<th>' . __('languages.event.End Date') . '</th>
							<th>' . __('languages.event.Start_time') . '</th>
							<th>' . __('languages.event.End_time') . '</th>
							<th>' . __('languages.event.Hours') . '</th>
							<th>' . __('languages.event.no_of_dates') . '</th>';
							if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
								$html .= '<th>' . __('languages.Status') . '</th>';
							}
							$html .= '<th>' . __('languages.Action') . '</th>
						</tr>
					</thead>
				<tbody>';

		$query = Events::with('eventType','eventschedule');

		//Filteration on Event Date
		if(!empty($filter_date_event)){
			$expolde_event_date = array_map('trim',explode('-', $filter_date_event));
			$start_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[0]);
			$end_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[1]);

			$query->whereHas('eventSchedule',function($q) use($start_event_date, $end_event_date){
				$q->whereBetween('date', [$start_event_date, $end_event_date]);
			});
		}
		
		//Filtration on Event Type
		if (isset($request->filter_event_type) && !empty($request->filter_event_type)) {
			if($request->filter_event_type == 'all_service'){
				$query->whereHas('eventType',function($q) use($request){
					$q->where('type_id',3);
				});
			}else{
				$query->where('event_type', $filter_event_type);
			}
		}

		//Filtration on Event Status
		if ($request->event_status != '') {
			$query->where('status', $request->event_status);
		}
		//Filtration on Event Name and Event Code
		if(!empty($request->search_text)){
			$query->where(function($q1) use($request){
				$q1->where('event_name', 'like', '%'.$request->search_text.'%')->orWhere('event_code', 'like', '%'.$request->search_text.'%');
			});
		}

		$events = $query->orderBy('id','DESC')->get()->toArray();
		if (!empty($events)) {
			foreach ($events as $key => $value) {
				$events[$key]['scheduleData'] = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
				$scheduleData = EventSchedule::select('date', 'start_time', 'end_time', 'event_hours')->where('event_id', $value['id'])->groupBy('date')->get()->toArray();
				$totaleventhour = 0;
				$dates = [];
				foreach ($scheduleData as $val) {
					$dates[] = $val['date'];
					if(!empty($val['event_hours']) && $val['event_hours'] != 'NaN'){
						$totaleventhour += $val['event_hours'];
					} 
				}
				$events[$key]['totaleventhour'] = $totaleventhour;
				if(!empty($dates)){
					$events[$key]['event_start_date'] = date('d/m/Y', strtotime($dates[0]));
					$events[$key]['event_end_date'] = date('d/m/Y', strtotime(end($dates)));
				}else{
					$events[$key]['event_start_date'] = '';
					$events[$key]['event_end_date'] = '';
				}
			}
		}
		

		if(!empty($events)){
			$EventType = 'event_type_name_' . app()->getLocale();
			$selected = '';
			foreach ($events as $val) {
				$html .= '<tr>
							<td><input type="checkbox" name="eventIds[]" class="select-event-chkbox" value="'.$val['id'].'"></td>
							<td><a href="' . route('eventManagement.edit', $val['id']) . '">' . $val['event_name'] . '</a></td>
							<td>' . $val['event_type']['event_type_name_en'] . '</td>
							<td>' . $val['event_code'] . '</td>
							<td>' . $val['event_start_date'] . '</td>';
							
							if ($val['event_end_date'] != '') {
								$html .= '<td>' . $val['event_end_date'] . '</td>';
							} else {
								$html .= '<td></td>';
							}
							$html .= '<td>' . $val['start_time'] . '</td>
							<td>' . $val['end_time'] . '</td>
							<td>' . $val['totaleventhour'] . '</td>
							<td>' . $val['no_of_dates'] . '</td>';
							if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
								$html .= '<td>
											<select class="form-control status" id="status" >
												<option value="">' . __('languages.event.Select_status') . '</option>
												<option value="1" data-id="' . $val['id'] . '"';
												if ($val['status'] == 1) {
													$html .= 'selected';
												}
												$html .= '>' . __('languages.event.Published') . '</option>

												<option value="2" data-id="' . $val['id'] . '"';
												if ($val['status'] == 2) {
													$html .= 'selected';
												}
												$html .= '>' . __('languages.event.Ready_to_close') . '</option>

												<option value="3" data-id="' . $val['id'] . '"';
												if ($val['status'] == 3) {
													$html .= 'selected';
												}
												$html .= '>' . __('languages.event.Unpublished') . '</option>

												<option value="4" data-id="' . $val['id'] . '"';
												if ($val['status'] == 4) {
													$html .= 'selected';
												}
												$html .= '>' . __('languages.event.Close_event') . '</option>
											</select>
										</td>';
							}
							$html .= '<td>';
								if ($val['status'] != '1') {
									if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
										$html .= '<a href="' . route('eventManagement.edit', $val['id']) . '"><i class="bx bx-edit-alt"></i></a>';
									}
								} else {
									$url = '/attendanceManagement?event_id=' . Helper::encodekey($val['id']);
									$html .= '<a href="' . $url . '" title="Attendance" target="_blank"><i class="bx bxs-book-open"></i></a>';
								}
								if (in_array('event_management_delete', Helper::module_permission(Session::get('user')['role_id']))) {
									$html .= '<a href="javascript:void(0);" data-id="' . $val['id'] . '" class="deletEvent"><i class="bx bx-trash-alt"></i> </a>';
								}
							'</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
		}
		return $html;
	}
	// public function eventListSearch(Request $request) {
	// 	$filter_date_event = !empty($request->filter_date_event) ? $request->filter_date_event : '';
	// 	$filter_event_type = !empty($request->filter_event_type) ? $request->filter_event_type : '';
	// 	$filter_occurs = !empty($request->filter_occurs) ? $request->filter_occurs : '';
	// 	$ids = array();
	// 	$html = '';

	// 	if(!empty($filter_date_event)){
	// 		$expolde_event_date = array_map('trim',explode('-', $filter_date_event));
	// 		$start_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[0]);
	// 		$end_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[1]);
	// 		//$search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE STR_TO_DATE(date, '%m/%d/%Y') BETWEEN STR_TO_DATE('" . $start_event_date . "', '%m/%d/%Y') AND STR_TO_DATE('" . $end_event_date . "', '%m/%d/%Y')  GROUP BY event_code ORDER BY id DESC"));
	// 		$search_result = EventSchedule::whereBetween('date', array($start_event_date, $end_event_date))
	// 							->groupBy('event_code')->orderBy('ID','DESC')->get()->toArray();
	// 		if (!empty($search_result)) {
	// 			$array = json_decode(json_encode($search_result), true);
	// 			$ids = array_column($array, 'id');
	// 		} else {
	// 			$html .= '<table id="search-eventtable" class="table">
	// 						<thead>
	// 							<tr>
	// 								<th>
	// 									<input type="checkbox" name="eventIds[]" class="select-all-event-chkbox" value="all">
	// 								</th>
	// 								<th>' . __('languages.event.Event Name') . '</th>
	// 								<th>' . __('languages.event.Event Type') . '</th>
	// 								<th>' . __('languages.event.Event_code') . '</th>
	// 								<th>' . __('languages.event.Start Date') . '</th>
	// 								<th>' . __('languages.event.End Date') . '</th>
	// 								<th>' . __('languages.event.Start_time') . '</th>
	// 								<th>' . __('languages.event.End_time') . '</th>
	// 								<th>' . __('languages.event.Hours') . '</th>';
	// 								if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
	// 									$html .= '<th>' . __('languages.Status') . '</th>';
	// 								}
	// 								$html .= '<th>' . __('languages.Action') . '</th>
	// 							</tr>
	// 						</thead>
	// 						<tbody>';
	// 			$html .= '</tbody>
	// 					</table>';
	// 			return $html;exit;
	// 		}
	// 	}
	// 	$Select_db = DB::table('event_schedule')
	// 		->select('event_schedule.*', 'events.*','events.status as event_status', 'event_type.*')
	// 		->join('events', 'events.id', 'event_schedule.event_id')
	// 		->join('event_type', 'event_type.id', 'events.event_type');

	// 	if (isset($request->filter_event_type) && !empty($filter_event_type)) {
	// 		if($request->filter_event_type == 'all_service'){
	// 			$allServiceIds = EventType::where('type_id',3)->get()->pluck('id');
	// 			$Select_db->whereIn('events.event_type', $allServiceIds);
	// 		}else{
	// 			$Select_db->where('events.event_type', $filter_event_type);
	// 		}
	// 	}
	// 	if ($request->event_status != '') {
	// 		$Select_db->where('event_schedule.status', $request->event_status);
	// 	}
	// 	if(!empty($request->search_text)){
	// 		//$Select_db->where('events.event_name', $request->search_text)->orWhere('event_schedule.event_code', $request->search_text);
	// 		$Select_db->where('events.event_name', 'like', '%'.$request->search_text.'%')->orWhere('event_schedule.event_code', 'like', '%'.$request->search_text.'%');
	// 	}
	// 	if (!empty($filter_date_event)) {
	// 		$Select_db->whereIn('event_schedule.id', $ids);
	// 	}
	// 	if (isset($request->filter_occurs) && !empty($filter_occurs)) {
	// 		$Select_db->where('event_schedule.occurs', $filter_occurs);
	// 	}
	// 	if (Session::get('user')['role_id'] != '1') {
	// 		$Select_db->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',events.event_assign_user)');
	// 		$events = $Select_db->orderBy('id','DESC')->groupBy('event_schedule.date')->groupBy('event_schedule.event_code')->get()->toArray();
	// 	} else {
	// 		// $events = $Select_db->orderBy('events.id','DESC')->groupBy('event_schedule.event_code')->get()->toArray();
	// 		//$events = $Select_db->orderBy('events.id','DESC')->get()->toArray();
	// 		$events = $Select_db->orderBy('events.id','DESC')->groupBy('events.event_code')->groupBy('event_schedule.date')->get()->toArray();
	// 	}

	// 	$html .= '<table id="search-eventtable" class="table">
	// 				<thead>
	// 					<tr>
	// 						<th>
	// 							<input type="checkbox" name="eventIds[]" class="select-all-event-chkbox" value="all">
	// 						</th>
	// 						<th>' . __('languages.event.Event Name') . '</th>
	// 						<th>' . __('languages.event.Event Type') . '</th>
	// 						<th>' . __('languages.event.Event_code') . '</th>
	// 						<th>' . __('languages.event.Start Date') . '</th>
	// 						<th>' . __('languages.event.End Date') . '</th>
	// 						<th>' . __('languages.event.Start_time') . '</th>
	// 						<th>' . __('languages.event.End_time') . '</th>
	// 						<th>' . __('languages.event.Hours') . '</th>';
	// 						if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
	// 							$html .= '<th>' . __('languages.Status') . '</th>';
	// 						}
	// 						$html .= '<th>' . __('languages.Action') . '</th>
	// 					</tr>
	// 				</thead>
	// 			<tbody>';
	// 			if (!empty($events)) {
	// 				$EventType = 'event_type_name_' . app()->getLocale();
	// 				$selected = '';
	// 				foreach ($events as $val) {
	// 					$html .= '<tr>
	// 								<td><input type="checkbox" name="eventIds[]" class="select-event-chkbox" value="'.$val->event_id.'"></td>
	// 								<td><a href="' . route('eventManagement.edit', $val->event_id) . '">' . $val->event_name . '</a></td>
	// 								<td>' . $val->$EventType . '</td>
	// 								<td>' . $val->event_code . '</td>
	// 								<td>' . date('d/m/Y', strtotime($val->startdate)) . '</td>';
									
	// 								if ($val->enddate != '') {
	// 									$html .= '<td>' . date('d/m/Y', strtotime($val->enddate)) . '</td>';
	// 								} else {
	// 									$html .= '<td></td>';
	// 								}
	// 								$html .= '<td>' . $val->start_time . '</td>
	// 								<td>' . $val->end_time . '</td>
	// 								<td>' . $val->event_hours . '</td>';
	// 								//$html .='<td>'.$totalhour->totalhour.'</td>
	// 								if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
	// 									$html .= '<td>
	// 												<select class="form-control status" id="status" >
	// 													<option value="">' . __('languages.event.Select_status') . '</option>
	// 													<option value="1" data-id="' . $val->event_id . '"';
	// 													if ($val->event_status == 1) {
	// 														$html .= 'selected';
	// 													}
	// 													$html .= '>' . __('languages.event.Published') . '</option>
	// 													<option value="3" data-id="' . $val->event_id . '"';
	// 													if ($val->event_status == 3) {
	// 														$html .= 'selected';
	// 													}
	// 													$html .= '>' . __('languages.event.Ready_to_close') . '</option>
	// 													<option value="4" data-id="' . $val->event_id . '"';
	// 													if ($val->event_status == 4) {
	// 														$html .= 'selected';
	// 													}
	// 													$html .= '>' . __('languages.event.Close_event') . '</option>
	// 												</select>
	// 											</td>';
	// 								}
	// 								$html .= '<td>';
	// 									if ($val->status != '1') {
	// 										if (in_array('event_management_write', Helper::module_permission(Session::get('user')['role_id']))) {
	// 											$html .= '<a href="' . route('eventManagement.edit', $val->event_id) . '"><i class="bx bx-edit-alt"></i></a>';
	// 										}
	// 									} else {
	// 										$url = '/attendanceManagement?event_id=' . Helper::encodekey($val->event_id);
	// 										$html .= '<a href="' . $url . '" title="Attendance" target="_blank"><i class="bx bxs-book-open"></i></a>';
	// 									}
	// 									if (in_array('event_management_delete', Helper::module_permission(Session::get('user')['role_id']))) {
	// 										$html .= '<a href="javascript:void(0);" data-id="' . $val->event_id . '" class="deletEvent"><i class="bx bx-trash-alt"></i> </a>';
	// 									}
	// 								'</td>';
	// 					$html .= '</tr>';
	// 				}
	// 				$html .= '</tbody></table>';
	// 			}
	// 	return $html;
	// }

	/**
	 ** USE : Generate Event code dynamic based on event type select
	 **/
	public function generateEventCode(Request $request) {
		$eventTypeData = EventType::where('id', $request->eventTypeID)->first();
		if (!empty($eventTypeData)) {
			if ($eventTypeData->type_id == '0') {
				$eventFirstCode = ($eventTypeData->id == '1') ? 'T' : 'E';
				$Event_count = Events::where('event_type', $eventTypeData->id)->count();
				$event_number = (1 + $Event_count);
				$eventCode = $eventFirstCode . '' . date('y') . '' . sprintf("%02d", $event_number);
			} else {
				$eventFirstCode = 'S';
				//$eventTypeData = EventType::where('id', $request->eventTypeID)->first();
				$eventTypeIds = EventType::where('type_id', $request->eventTypeParentId)->pluck('id')->toArray();
				//$Event_count = Events::where('event_type', $eventTypeData->type_id)->count();
				$Event_count = Events::whereIn('event_type', $eventTypeIds)->count();
				$event_number = (1 + $Event_count);
				$eventCode = $eventFirstCode . '' . date('y') . '' . sprintf("%02d", $event_number);
			}
			if ($eventCode) {
				Session::flash('success_msg', 'Assign User successfully.');
				$status = true;
				return response()->json(['eventCode' => $eventCode, 'status' => $status]);
			} else {
				Session::flash('error_msg', "Something went wrong.");
				$status = false;
				return response()->json(['status' => $status]);
			}
		}
	}

	public function editNewEventDates(Request $request) {
		$formData = $request->formData;
		parse_str($formData, $eventArray);
		$allDates = explode(",", $eventArray['event_dates']);

		$events = Events::find($eventArray['event_main_id']);
		/*Audit Log START*/
		$usersdata = new Events;
		$post_value = $request->all();
		Helper::AuditLogfuncation($post_value, $usersdata, 'id', $eventArray['event_main_id'], 'events', 'Event');
		/*Audit Log END*/

		$events->event_name = !empty($eventArray['event_name']) ? $eventArray['event_name'] : NULL;
		$events->event_type = !empty($eventArray['event_type']) ? $eventArray['event_type'] : NULL;
		$events->event_code = !empty($eventArray['event_code']) ? $eventArray['event_code'] : NULL;
		$events->assessment = !empty($eventArray['assessment']) ? $eventArray['assessment'] : NULL;
		$events->assessment_text = !empty($eventArray['assessment_text']) ? $eventArray['assessment_text'] : NULL;

		$oldDates = explode(',',$eventArray['old_event_dates']);
		$resultDate = array_diff($allDates,$oldDates);
		$events->no_of_dates = !empty($resultDate) ? ($events->no_of_dates + count($resultDate)) :  count($oldDates);
		
		
		// $oldDates = explode(',',$request->old_event_dates);
		// $resultDate = array_diff($allDates,$oldDates);
		// $events->no_of_dates = !empty($resultDate) ? ($events->no_of_dates + count($resultDate)) :  count($oldDates);

		/*$events->event_money = !empty($eventArray['event_money']) ? str_replace("HKD","",$eventArray['event_money']) : '0';*/
		//$events->event_money = !empty($eventArray['event_money']) ? $eventArray['event_money'] : '0';
		/*$events->event_token = !empty($eventArray['event_money']) ? (str_replace("HKD","",$eventArray['event_money']) / 10) : NULL;*/
		//$events->event_token = !empty($eventArray['event_token']) ? (str_replace("HKD","",$eventArray['event_token'])) : NULL;
		$events->status = isset($eventArray['status']) ? $eventArray['status'] : "0";
		$result = $events->update(); // save data

		if (!empty($events)) {
			$scheduleData = EventSchedule::where('event_id', $events->id)->get();
			foreach ($scheduleData as $key => $value) {
				$updateSchedule = EventSchedule::where('id', $value->id)->first();
				$updateSchedule->event_code = !empty($eventArray['event_code']) ? $eventArray['event_code'] : NULL;
				$updateSchedule->status = !empty($eventArray['status']) ? $eventArray['status'] : NULL;
				$result = $updateSchedule->save();
			}
		}

		if (!empty($allDates)) {
			foreach ($allDates as $date_key => $date_value) {
				$date_value = Helper::dateFormatMDY('/','-',$date_value);
				if(EventSchedule::where('event_id',$eventArray['event_main_id'])->where('date',$date_value)->exists()){
					$EventSchedule = EventSchedule::where('event_id',$eventArray['event_main_id'])->where('date',$date_value)->first();
					$EventSchedule->event_code = !empty($eventArray['event_code']) ? $eventArray['event_code'] : NULL;
					$EventSchedule->status = !empty($eventArray['status']) ? $eventArray['status'] : NULL;
					$EventSchedule->update();
				}else{
					$EventSchedule = new EventSchedule();
					$EventSchedule->event_id = $eventArray['event_main_id'];
					$EventSchedule->start_time = !empty($eventArray['eventstarttime']) ? $eventArray['eventstarttime'] : NULL;
					$EventSchedule->end_time = !empty($eventArray['endtime']) ? $eventArray['endtime'] : NULL;
					$EventSchedule->event_hours = !empty($eventArray['eventhours']) ? $eventArray['eventhours'] : NULL;
					$EventSchedule->date = $date_value;
					$EventSchedule->occurs = 'Once';
					$EventSchedule->status = $eventArray['status'];
					$EventSchedule->event_code = !empty($eventArray['event_code']) ? $eventArray['event_code'] : NULL;
					$EventSchedule->save();
				}
			}
		}

		if ($EventSchedule) {
			$message = 'Event added successfully..';
			$status = true;
			$data = $EventSchedule;
		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message, 'data' => $data]);

	}

	public static function eventAssignLog($user_id, $log_id, $event_name, $event_type) {
		$assignTo = User::find($user_id)->English_name;
		$assignBy =  Session::get('user')['username'];
		$getData =  ['Event' => $event_name, 'event_type' => $event_type, 'assign_to_user' => $assignTo, 'assign_by_user' => $assignBy];
		Helper::InsertAuditLogfuncation($getData, $log_id, 'EventAssign', 'Event');
	}
}