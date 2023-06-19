<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Models\Attendance;
use App\Http\Models\EventAssignModel;
use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\HourAttendance;
use App\Http\Models\MemberToken;
use App\Http\Models\MemberTokenStatus;
use App\Http\Models\MemberUsedToken;
use App\Http\Models\User;
use App\Http\Models\EventTokenManage;
use App\Http\Models\Settings;
use App\Jobs\SendQRMailJob;
use Mail;
use Config;
use DateTime;
use DB;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Session;
use App\Jobs\SendEmailJob;
use Log;
use Carbon\Carbon;

class AttendanceController extends Controller {

	public function __construct() {
		$this->Attendance = new Attendance;
		date_default_timezone_set(Config::get('constants.timeZone'));
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
	public function index(Request $request) {
		$Attendance = '';
		$eventTypes = new EventType;
		$get_event_type_list = $eventTypes->get_event_type_select_list();
		if (!empty($request->event_id)) {
			$id = Helper::decodekey($request->event_id);
			$get_event_code = Events::where('id', $id)->first();
			$event_code = $get_event_code->event_code;
			if (Session::get('user')['role_id'] != '1') {
				if (!empty($event_code)) {
					$events = EventSchedule::with('events')->whereHas('events', function ($query) {
						$query->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)');
					})
						->where('event_code', $event_code)->where('status', '1')
						->groupBy('occurs')
						->get()
						->toArray();
					//$events = EventSchedule::with('events')->whereHas('events', function ($query) {$query->whereRaw('FIND_IN_SET('.Session::get('user')['user_id'].',event_assign_user)');})->where('event_code',$event_code)->where('date',date('m/d/Y'))->where('status','1')->get()->toArray();
					$Attendance = Attendance::where('user_id', Session::get('user')['user_id'])->where('event_id', $id)->with('users')
						->with('event')
						->with('eventType')
						->where('date',Carbon::now()->format('l,d F,Y'))
						->orderBy('id','desc')
						->get()
						->toArray();
				}
			} else {
				$events = EventSchedule::with('events')->where('event_code', $event_code)->where('status', '1')
					->groupBy('occurs')
					->get()
					->toArray();
				//$events = EventSchedule::with('events')->where('event_code',$event_code)->where('date',date('m/d/Y'))->where('status','1')->get()->toArray();
				$Attendance = Attendance::where('event_id', $id)->with('users')
					->with('event')
					->with('eventType')
					->where('date',Carbon::now()->format('l,d F,Y'))
					->orderBy('id','desc')
					->get()
					->toArray();
			}
		} else {
			if (Session::get('user')['role_id'] != '1') {
				$events = EventSchedule::with('events')->whereHas('events', function ($query) {
					$query->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)');
				})
					->where('date', date('m/d/Y'))
					->where('status', '1')
					->get()
					->toArray();
				$Attendance = Attendance::where('user_id', Session::get('user')['user_id'])->with('users')
					->with('event')
					->with('eventType')
					->where('date',Carbon::now()->format('l,d F,Y'))
					->orderBy('id','desc')
					->get()
					->toArray();
			} else {
				$events = EventSchedule::with('events')->where('date', date('m/d/Y'))
					->where('status', '1')
					->get()
					->toArray();
				$Attendance = Attendance::with('users')->with('event')
					->with('eventType')
					->where('date',Carbon::now()->format('l,d F,Y'))
					->orderBy('id','desc')
					->get()
					->toArray();
			}
		}
		return view('AttendanceManagement.list_attendance', compact('events', 'Attendance', 'get_event_type_list'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$members = User::selectRaw('id,UserName,MemberCode,Chinese_name,English_name')->where('Role_ID', '!=', '1')
			->where('Status', '1')
			->get()
			->toArray();
		$events = Events::selectRaw('id,event_name,event_type,startdate,start_time')->where('status', '1')
			->get()
			->toArray();
		return view('AttendanceManagement.add_attendance', compact('members', 'events'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$user = User::selectRaw('ID,hour_point')->where('ID', $request->members)
			->first()
			->toArray();
		if (!empty($user)) {
			$totalhour = $user['hour_point'];
		}
		$hourAttendance = HourAttendance::where('user_id', $request->members)
			->orderBy('id', 'desc')
			->first();
		if (!empty($hourAttendance)) {
			if ($hourAttendance->use_hour != $totalhour) {
				$id = $hourAttendance->id;
				$hourattend = HourAttendance::find($id);
				$hourattend->user_id = $request->members;
				$hourattend->event_id = $request->eventName;
				if ($hourAttendance->remaining_hour == 0 && $hourattend->Total_hour == 0) {
					$hourattend = new HourAttendance;
					$use_hour = $request->hours;
					$remaining_hour = $totalhour - $use_hour;
					$hourattend->user_id = $request->members;
					$hourattend->event_id = $request->eventName;
					$hourattend->use_hour = $use_hour;
					$hourattend->remaining_hour = $remaining_hour;
					$hourattend->Total_hour = $totalhour;
					$hourattend->save();

					$attendance = new Attendance;
					$attendance->user_id = $request->members;
					$attendance->member_code = $request->memberCode;
					$attendance->event_id = $request->eventName;
					$attendance->event_type = $request->eventType;
					$attendance->in_time = $request->inTime;
					$attendance->out_time = $request->outTime;
					$attendance->hours = $request->hours;
					$result = $attendance->save();

					if ($result) {
						return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
					} else {
						return back()
							->with('error_msg', 'Something went wrong.');
					}
				} else {
					if ($hourAttendance->remaining_hour >= $request->hours) {
						$use_hour = $hourAttendance->use_hour + $request->hours;
						$remaining_hour = $hourAttendance->remaining_hour - $request->hours;
						$hourattend->use_hour = $use_hour;
						$hourattend->remaining_hour = $remaining_hour;
						if ($remaining_hour != '0') {
							$hourattend->Total_hour = $totalhour;
						} else {
							$hourattend->Total_hour = 0;
						}
						$hourattend->save();
						$attendance = new Attendance;
						$attendance->user_id = $request->members;
						$attendance->member_code = $request->memberCode;
						$attendance->event_id = $request->eventName;
						$attendance->event_type = $request->eventType;
						$attendance->in_time = $request->inTime;
						$attendance->out_time = $request->outTime;
						$attendance->hours = $request->hours;
						$result = $attendance->save();

						if ($result) {
							return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
						} else {
							return back()
								->with('error_msg', 'Something went wrong.');
						}
					} else {
						return redirect('attendanceManagement/create')
							->with('error_msg', 'Please update your hour point.');
					}
				}
			} else {
				return redirect('attendanceManagement/create')
					->with('error_msg', 'Please update your hour point.');
			}
		} else {
			if (!empty($totalhour)) {
				if ($totalhour >= $request->hours) {
					$hourattend = new HourAttendance;
					$use_hour = $request->hours;
					$remaining_hour = $totalhour - $use_hour;
					$hourattend->user_id = $request->members;
					$hourattend->event_id = $request->eventName;
					$hourattend->use_hour = $use_hour;
					$hourattend->remaining_hour = $remaining_hour;
					$hourattend->Total_hour = $totalhour;
					$hourattend->save();

					$attendance = new Attendance;
					$attendance->user_id = $request->members;
					$attendance->member_code = $request->memberCode;
					$attendance->event_id = $request->eventName;
					$attendance->event_type = $request->eventType;
					$attendance->in_time = $request->inTime;
					$attendance->out_time = $request->outTime;
					$attendance->hours = $request->hours;
					$result = $attendance->save();

					if ($result) {
						return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
					} else {
						return back()
							->with('error_msg', 'Something went wrong.');
					}
				} else {
					return redirect('attendanceManagement/create')
						->with('error_msg', 'Please update your hour point.');
				}
			} else {
				return redirect('attendanceManagement/create')
					->with('error_msg', 'Please update your hour point.');
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$members = User::selectRaw('id,UserName,MemberCode,Chinese_name,English_name')->where('Role_ID', '!=', '1')
			->where('Status', '1')
			->get()
			->toArray();
		// $events = Events::selectRaw('id,event_name,event_type,startdate,start_time')->where('status', '1')
		// 	->get()
		// 	->toArray();
		$events = Events::selectRaw('id,event_name,event_type,startdate,start_time')->get()->toArray();
		$attendance = Attendance::find($id);
		return view('AttendanceManagement.edit_attendance', compact('members', 'events', 'attendance'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$formData = $request->formData;
		parse_str($formData, $request);
		$attendance = Attendance::find($id);
		$attendance->user_id = $request['members'];
		$attendance->member_code = $request['memberCode'];
		$attendance->event_id = $request['eventName'];
		$attendance->event_type = $request['eventType'];
		
		$explodeInTime = explode(':',$request['inTime']);
		if(count($explodeInTime) == 2){
			$inTime = $request['inTime'].':00';
		}else{
			$inTime = $request['inTime'];
		}
		
		$attendance->in_time = $inTime;
		$EventScheduleData = EventSchedule::find($attendance->event_schedule_id);
		//if(date_create(date('H:i:s',strtotime($EventScheduleData->end_time))) < date_create(date('H:i:s')) || date('d-m-Y',strtotime($EventScheduleData->date)) < date('d-m-Y')){
			if(isset($request['outTime']) && !empty($request['outTime']) && $request['outTime'] != '-'){
				$explodeOutTime = explode(':',$request['outTime']);
				if(count($explodeOutTime) == 2){
					$outTime = $request['outTime'].':00';
				}else{
					$outTime = $request['outTime'];
				}
			}else{
				$outTime = date('H:i:s',strtotime($EventScheduleData->end_time));
			}
			$attendance->out_time = $outTime;

			// Find the calculate hours
			$diff = abs(strtotime($outTime) - strtotime($inTime));
			$mins = $diff / 60;
			
			$diff_hours = intdiv($mins, 60) . ':' . ($mins % 60);
			$userData = User::where('Role_ID', '2')->where('ID', $attendance->user_id)->first();
			if (!empty($userData)){
				$hour_point = $userData->hour_point;
			}
			if (!empty($hour_point)){
				// Find the late intime login into event management
				$attendance->late_min = Helper::getMinutesOfLateInTimeEvent($attendance->event_schedule_id, $inTime);

				/** Logic for deduct hours */
				$totalEventHours = 0;
				$totalEventTimes = Helper::getEventHotalTimes($attendance->event_schedule_id);
				if(!empty($totalEventTimes)){
					$totalEventHours = $totalEventTimes['difference']->h;
					$attendance->total_event_hours = $totalEventHours ?? 0;
				}

				// Find Intime diduct hours
				$InTimeDeductHours = 0;
				$InTimeDeductHours = Helper::deductHours($attendance->late_min);				
				$attendance->in_time_deducted_hour = $InTimeDeductHours ?? 0;

				// Find earlier no of Minutes of leave event
				$EarlierMinutes = 0;
				$EarlierMinutes = Helper::getMinutesOfEarlierLeaveEvent($attendance->event_schedule_id, $outTime);
				$attendance->early_min = $EarlierMinutes ?? 0;

				// Find Outtime deduct hours
				$OutTimeDeductHour = 0;
				$OutTimeDeductHour = Helper::deductHours($EarlierMinutes);
				$attendance->out_time_deducted_hour = $OutTimeDeductHour ?? 0;
				$attendance->total_deducted_hour = ($InTimeDeductHours + $OutTimeDeductHour);
				$deduct_hour = ($InTimeDeductHours + $OutTimeDeductHour);
				$attendance->hours = ($totalEventHours - ($InTimeDeductHours + $OutTimeDeductHour)) ?? '-';
				//$deduct_hour = $deduct_hour . ':00';
				$diff_deduct_hour = $deduct_hour . ':00';
				// $diff_deduct_minit = abs(strtotime($diff_hours) - strtotime($deduct_hour)) / 60;
				// $diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);

				$remaining_hour = 0;
				$remaining_hour = $this->attendanceremainingHour($attendance->user_id, $diff_deduct_hour, $hour_point);
				//$remaining_hour = $this->attendanceremainingHour($attendance->user_id, $diff_hours, $hour_point);
				$event = Events::find($attendance->event_id);
				// Update field value
				// if ($event->event_type == '1') {
				// 	$attendance->training_hour = $diff_deduct_hour;
				// 	$attendance->service_hour = $diff_deduct_hour;
				// 	$attendance->remaining_hour = $remaining_hour;
				// } else if ($event->event_type == '2') {
				// 	$attendance->activity_hour = $diff_deduct_hour;
				// 	$attendance->service_hour = $diff_deduct_hour;
				// 	$attendance->remaining_hour = $remaining_hour;
				// } else {
				// 	$attendance->service_hour = $diff_deduct_hour;
				// 	$attendance->remaining_hour = $remaining_hour;
				// }

				if ($event->event_type == '1') {
					$attendance->training_hour = $attendance->hours;
					$attendance->service_hour = $attendance->hours;
					$attendance->remaining_hour = $remaining_hour;
				} else if ($event->event_type == '2') {
					$attendance->activity_hour = $attendance->hours;
					$attendance->service_hour = $attendance->hours;
					$attendance->remaining_hour = $remaining_hour;
				} else {
					$attendance->service_hour = $attendance->hours;
					$attendance->remaining_hour = $remaining_hour;
				}

				if (!empty($request['date'])) {
					$startdate = !empty($request['date']) ? DateTime::createFromFormat('d F, Y', $request['date']) : '';
					$attendance->date = !empty($request['date']) ? $startdate->format('l,d F,Y') : NULL;
				}
				// Save the attendance details
				$result = $attendance->save();
				if ($result) {
					// Update for Token management generated after by edit attendance
					$eventScheduleId = $attendance->event_schedule_id;
					$attendanceId = $attendance->id;
					$Setting = Settings::first();
					$eventSchedule = EventSchedule::find($eventScheduleId);
					if(!empty($eventSchedule)){
						$attendance = Attendance::find($attendanceId);
						if(!empty($attendance)){
							// Generate new token based on attempted event hours Ex: User can ateempted per 1 Hours = 1 Token, 2 Hours = 2 Token
							//$NoOfGenerateToken = (date('H', strtotime($diff_deduct_hour)));
							$NoOfGenerateToken = $attendance->hours ?? 0;
							if(EventTokenManage::where([
								'user_id' => $attendance->user_id,
								'event_id' => $eventScheduleId
							])->exists()){
								$EventTokenManage = EventTokenManage::where(['user_id' => $attendance->user_id,'event_id' => $eventScheduleId])->first();
								$EventTokenManage->generate_token = $NoOfGenerateToken;
								$EventTokenManage->save();
							}else{
								// Save data into 'event_token_manage' tables
								$AddToken = EventTokenManage::create([
									'user_id' => $attendance->user_id,
									//'event_id' => $attendance->event_id,
									'event_id' => $attendance->event_schedule_id,
									'generate_token' => $NoOfGenerateToken,
									'expire_date' => date('Y-m-d', strtotime('+' . $Setting->token_expire_day . ' days'))
								]);
							}
						}
					}
					/*return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');*/
					$message = 'Attendance updated successfully..';
					$status = true;
				} else {
					$message = 'Please try again';
					$status = false;
				}
			}else{
				$message = 'Hours Point not available....';
				$status = false;
			}
		// }else{
		// 	$result = $attendance->save();
		// 	$message = 'Attendance updated successfully..';
		// 	$status = true;
		// }
		return response()->json(['status' => $status, 'message' => $message]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//

		$Attendance = Attendance::where('id', $id)->first();
		if ($Attendance) {
			/*$memberToken = MemberToken::where('user_id', $Attendance->user_id)->where('event_id', $Attendance->event_id)->where('status', 0)->where('expired', 0)->whereDate('expired_at', '>=', date('Y-m-d'))->first();

				            if($memberToken){
				                $memberTokenStatus = MemberTokenStatus::where('user_id',$Attendance->user_id)->first();
				                $memberTokenStatus->total_token = ($memberTokenStatus->total_token) - ($memberToken->remaining_token);
				                $result = $memberTokenStatus->save();

				                $memberToken->remaining_token = '0';
				                $memberToken->save();

			*/
			$delete = Attendance::where('id', $id)->delete();
			if ($delete) {
				$message = 'Attendance deleted successfully..';
				$status = true;
			} else {
				$message = 'Please try again';
				$status = false;
			}

		} else {
			$message = 'Please try again';
			$status = false;
		}
		return response()->json(['status' => $status, 'message' => $message]);

	}

	public function checkTimeLimitScaning($eventStartTime){
		$isScan = 'false';
		if(strtotime(date('H:i:s')) >= strtotime($eventStartTime)){
			$isScan = 'true';
		}else{
			$eventStartTime = date_create(date('H:i:s',strtotime($eventStartTime)));
			$currentTime = date_create(date('H:i:s')); 
			$difference = date_diff($eventStartTime, $currentTime); 
			$minutes = $difference->days * 24 * 60;
			$minutes += $difference->h * 60;
			$minutes += $difference->i;
			if($difference->days === 0 && $difference->h === 0 && $minutes <= 30){ // Login scanner allowed before 30 minutes
				$isScan = 'true';
			}else{
				$isScan = 'false';
			}
		}
		return $isScan;
	}

	/**
	 ** USE : Add Attender Login Time
	 *
	 */
	public function recordAttendance(Request $request) {
		$userid = base64_decode($request->user_id);
		$userData = User::where('Role_ID', '2')->where('ID', $userid)->first();
		$eventData = Events::where('id', $request->event_id)->first();
		if (!empty($userData)) {
			$hour_point = $userData->hour_point;
		}
		$redirect = '/attendanceManagement';
		if (!empty($userData)) {
			// if($eventData->is_free_event == 0 && empty($hour_point)) {
			// 	return response()->json(array(
			// 		'status' => 0,
			// 		'message' => 'You have not enough hour.',
			// 		'redirecturl' => $redirect,
			// 	));
			// }else{
				$event_sche = EventSchedule::where('id', $request->scheduleID)->first();
				$date_arr = array();
				if (!empty($eventData)) {
					//Login
					$eventStarttime = $event_sche->start_time;
					$diff_event = abs(strtotime($eventStarttime) - strtotime(date('H:i')));
					$tmins_event = $diff_event / 60;
					$hours_event = floor($tmins_event / 60);
					$mins_event = $tmins_event % 60;
					$eventendtime = $event_sche->end_time;
					
					//$checkAccessEvent = EventAssignModel::where('event_id', $request->event_id)->where('user_id', $userid)->first();
					$checkAccessEvent = EventAssignModel::where('event_id', $request->event_id)->where('user_id', $userid)->where('status', 1)->first();
					if (!isset($checkAccessEvent)) {
						return response()->json(array(
							'status' => 0,
							'message' => "You don't have access event",
							'redirecturl' => $redirect,
						));
					}

					if ($request->type == 1) {
						$Alreadylogin = Attendance::where('event_id', $request->event_id)->where('user_id', $userid)->get()->toArray();
						if (empty($Alreadylogin)) {
							$inTime = date('H:i:s');
							$eventStartdate = date("d-m-Y", strtotime($eventData['startdate']));
							if (strtotime(date("d-m-Y")) >= strtotime($eventStartdate) && $this->checkTimeLimitScaning($eventData['start_time']) == 'true') {
								if ($eventendtime > $inTime) {
									$diff_login = strtotime($inTime) - strtotime($eventStarttime);
									$starttimestamp = strtotime($inTime);
									$endtimestamp = strtotime($eventStarttime);
									$mins = abs($endtimestamp - $starttimestamp) / 60;
									$hours = intdiv($mins, 60) . ':' . ($mins % 60);

									// Get Attandance detail for perticular users
									$userattendfirst = Attendance::where('user_id', $userid)->first();

									// Save users attandance
									$attendance = new Attendance;
									$attendance->user_id = $userid;
									$attendance->member_code = $userData->MemberCode;
									$attendance->event_id = $request->event_id;
									$attendance->event_schedule_id = $event_sche->id;
									$attendance->event_type = $eventData->event_type;
									//if ($hours == 0 && $mins < $this->globalmin) {
									if(date_create(date('H:i:s',strtotime($eventStarttime))) > date_create(date('H:i:s'))){
										$attendance->in_time = date('H:i:s',strtotime($eventStarttime));
									} else {
										$attendance->late_min = $mins;
										$attendance->in_time = $inTime;
									}
									$attendance->date = !empty($event_sche->date) ? date("l,d F,Y", strtotime($event_sche->date)) : '';
									if (empty($userattendfirst)) {
										if($eventData->is_free_event==0){
											$attendance->remaining_hour = $hour_point . ':00';
										}
									}
									$result = $attendance->save();
									if ($result) {
										return response()->json(array(
											'status' => 1,
											'message' => 'Attendance Added successfully',
											'redirecturl' => $redirect,
										));
									} else {
										return response()->json(array(
											'status' => 0,
											'message' => 'Something is wrong',
											'redirecturl' => $redirect,
										));
									}
								} else {
									return response()->json(array(
										'status' => 0,
										'message' => 'Today event is closed.',
										'redirecturl' => $redirect,
									));
								}
							} else {
								return response()->json(array(
									'status' => 0,
									'message' => 'Wait for the event start.',
									'redirecturl' => $redirect,
								));
							}
						} else {
							return response()->json(array(
								'status' => 0,
								'message' => 'Member Already Login',
								'redirecturl' => $redirect,
							));
						}
					}

					//Logout
					if ($request->type == 2) {
						$loginDetail = Attendance::where('event_id', $request->event_id)
							->where('user_id', $userid)->first();
						if (!empty($loginDetail)) {
							$attendance = Attendance::find($loginDetail->id);
							if ($attendance->out_time != '-') {
								return response()->json(array(
									'status' => 0,
									'message' => 'Member is Already Logout.',
									'redirecturl' => $redirect,
								));
							} else {
								$inTime = $attendance->in_time;
								$outTime = date('H:i');
								if ($outTime > $eventendtime) {
									$diff = abs(strtotime($outTime) - strtotime($inTime));
									$attendance->out_time = $outTime;
								} else {
									$diff = abs(strtotime($outTime) - strtotime($inTime));
									$attendance->out_time = $eventendtime;
								}
								$mins = $diff / 60;
								$diff_hours = intdiv($mins, 60) . ':' . ($mins % 60);

								/** EARLY LATE MARGIN CALCULATION START **/
								$early_mins = abs(strtotime($eventendtime) - strtotime($outTime)) / 60;
								$early_hours = intdiv($mins, 60) . ':' . ($mins % 60);
								/** EARLY LATE MARGIN CALCULATION END **/
								if ($early_mins > $this->globalmin) {
									$attendance->early_min = $early_mins;
								}
								$remaining_hour = 0;
								$remaining_hour = $this->attendanceremainingHour($userid, $diff_hours, $hour_point);
								$deduct_hour = 0;
								if ($attendance->late_min != NULL && $this->globalmin < $attendance->late_min) {
									$deduct_hour++;
								}
								if ($this->globalmin < $early_mins) {
									$deduct_hour++;
								}
								$deduct_hour = $deduct_hour . ':00';
								$diff_deduct_minit = abs(strtotime($diff_hours) - strtotime($deduct_hour)) / 60;
								$diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);
								$attendance->hours = $diff_deduct_hour;
								if ($eventData->event_type == '1') {
									$attendance->training_hour = $diff_deduct_hour;
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								} else if ($eventData->event_type == '2') {
									$attendance->activity_hour = $diff_deduct_hour;
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								} else {
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								}
								$result = $attendance->save();
								if ($result) {
									return response()->json(array(
										'status' => 1,
										'message' => 'Member Logout successfully',
										'redirecturl' => $redirect,
									));
								} else {
									return response()->json(array(
										'status' => 0,
										'message' => 'Something is wrong',
										'redirecturl' => $redirect,
									));
								}
							}
						} else {
							return response()->json(array(
								'status' => 0,
								'message' => 'Member is not login',
								'redirecturl' => $redirect,
							));
						}
					}
				} else {
					return response()->json(array(
						'status' => 0,
						'message' => 'Event not found',
						'redirecturl' => $redirect,
					));
				}
			//}
		} else {
			return response()->json(array(
				'status' => 0,
				'message' => 'Invalid User',
				'redirecturl' => $redirect,
			));
		}
	}

	public function recordMemberCodeAttendance(Request $request) {
		$MemberCode = $request->MemberCode;
		$explodeMember = explode("C", $MemberCode);
		if (!empty($explodeMember[1])) {
			$userData = User::where('Role_ID', '2')->where('MemberCode', $explodeMember[1])->first();
		}
		if (!empty($userData)) {
			$userid = $userData->ID;
			$hour_point = $userData->hour_point;
		}
		// Redirect url
		$redirect = '/attendanceManagement';
		$eventData = Events::where('id', $request->event_id)->first();
		if (!empty($userData)) {
			// if($eventData->is_free_event == 0 && empty($hour_point)) {
			// 	return response()->json(array(
			// 		'status' => 0,
			// 		'message' => 'You have not enough hour.',
			// 		'redirecturl' => $redirect,
			// 	));
			// }else{
				$event_sche = EventSchedule::where('id', $request->scheduleID)->first();
				$date_arr = array();
				if (!empty($eventData)) {
					//Login
					$eventStarttime = $event_sche->start_time;
					$diff_event = abs(strtotime($eventStarttime) - strtotime(date('H:i')));
					$tmins_event = $diff_event / 60;
					$hours_event = floor($tmins_event / 60);
					$mins_event = $tmins_event % 60;
					$eventendtime = $event_sche->end_time;

					// Check event enrollment order is confirmed or not
					$checkAccessEvent = EventAssignModel::where('event_id', $request->event_id)->where('user_id', $userid)->where('status', 1)->first();
					if (!isset($checkAccessEvent)) {
						return response()->json(array(
							'status' => 0,
							'message' => "You don't have access event",
							'redirecturl' => $redirect,
						));
					}

					if ($request->type == 1) {
						$Alreadylogin = Attendance::where('event_id', $request->event_id)->where('user_id', $userid)->get()->toArray();
						if (empty($Alreadylogin)) {
							$inTime = date('H:i');
							$eventStartdate = date("d-m-Y", strtotime($eventData['startdate']));
							if (strtotime(date("d-m-Y")) >= strtotime($eventStartdate) && $this->checkTimeLimitScaning($eventData['start_time']) == 'true') {								
								if ($eventendtime > $inTime) {
									$diff_login = strtotime($inTime) - strtotime($eventStarttime);

									//if ($diff_login > 0) {
										//check event is paid or not
										// if ($eventData['event_money'] == '0') {
										// 	//free
										// 	$insertData = true;
										// } else {
										// 	//paid
										// 	if ($request->usedCoin == '0') {

										// 		//not using coin
										// 		$message = 'Please use coin for paid event.';
										// 		$insertData = false;
										// 	} else {
										// 		$MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
										// 		if (!empty($MemberTokenStatus)) {
										// 			//using coin
										// 			if ($eventData['event_token'] <= $MemberTokenStatus->total_token) {
										// 				$currentdate = date('Y-m-d');
										// 				//start logic for token used or not for expired token
										// 				$memberToken = MemberToken::where('user_id', $userid)->where('remaining_token', '!=', 0)
										// 					->where('status', 0)
										// 					->whereDate('expired_at', '>=', $currentdate)->where('expired', 0)
										// 					->get()
										// 					->toArray();

										// 				if (!empty($memberToken)) {
										// 					$remainingHour = $eventData['event_token'];
										// 					foreach ($memberToken as $memberToken_key => $memberToken_value) {
										// 						if ($remainingHour == '0') {
										// 							break;
										// 						} else {
										// 							if ($remainingHour < $memberToken_value['remaining_token']) {
										// 								$token = MemberToken::where('id', $memberToken_value['id'])->first();
										// 								$token->remaining_token = $token->remaining_token - $remainingHour;
										// 								$token->status = ($token->remaining_token - $remainingHour == '0') ? '1' : '0';
										// 								$saveToken = $token->save();
										// 								break;
										// 							} else {
										// 								$token = MemberToken::where('id', $memberToken_value['id'])->first();
										// 								$remainingHour = $remainingHour - $token->remaining_token;
										// 								$token->remaining_token = 0;
										// 								$token->status = 1;
										// 								$saveToken = $token->save();
										// 							}
										// 						}

										// 					}
										// 				} else {

										// 				}
										// 				//end logic for token used or not for expired token
										// 				$MemberTokenStatus->total_token = ($MemberTokenStatus->total_token - $eventData['event_token']);
										// 				$MemberTokenStatus->save();

										// 				//Save member used token detail
										// 				$MemberUsedToken = new MemberUsedToken;
										// 				$MemberUsedToken->user_id = !empty($userid) ? $userid : NULL;
										// 				$MemberUsedToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
										// 				$MemberUsedToken->token = !empty($eventData['event_token']) ? $eventData['event_token'] : NULL;
										// 				$saveMemberUsedToken = $MemberUsedToken->save();

										// 				$insertData = true;
										// 			} elseif ($eventData['event_money'] <= $MemberTokenStatus->total_money) {

										// 				$MemberTokenStatus->total_money = ($MemberTokenStatus->total_money - $eventData['event_money']);
										// 				$MemberTokenStatus->save();

										// 				//Save member used token detail
										// 				$MemberUsedToken = new MemberUsedToken;
										// 				$MemberUsedToken->user_id = !empty($userid) ? $userid : NULL;
										// 				$MemberUsedToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
										// 				$MemberUsedToken->money = !empty($eventData['event_money']) ? $eventData['event_money'] : NULL;
										// 				$saveMemberUsedToken = $MemberUsedToken->save();
										// 				$insertData = true;

										// 			} else {
										// 				$message = 'User have not enough coin to attend this event.';
										// 				$insertData = false;
										// 			}
										// 		} else {
										// 			$message = 'User have no coin to attend this event.';
										// 			$insertData = false;
										// 		}
										// 	}
										// }
										//if ($insertData) {
											/* $diff = abs(strtotime($inTime) - strtotime($eventStarttime));
																						$tmins = $diff / 60;
																						$hours = floor($tmins / 60);
											*/
											$starttimestamp = strtotime($inTime);
											$endtimestamp = strtotime($eventStarttime);
											$mins = abs($endtimestamp - $starttimestamp) / 60;
											$hours = intdiv($mins, 60) . ':' . ($mins % 60);

											$userattendfirst = Attendance::where('user_id', $userid)->first();
											$attendance = new Attendance;
											$attendance->user_id = $userid;
											$attendance->member_code = $userData->MemberCode;
											$attendance->event_id = $request->event_id;
											$attendance->event_schedule_id = $event_sche->id;
											$attendance->event_type = $eventData->event_type;

											// if ($hours == 0 && $mins < $this->globalmin) {
											// 	//$attendance->in_time = $eventStarttime;
											// 	$attendance->in_time = date('H:i:s',strtotime($eventStarttime));
											// } else {
											// 	$attendance->late_min = $mins;
											// 	$attendance->in_time = $inTime;
											// }

											if(date_create(date('H:i:s',strtotime($eventStarttime))) > date_create(date('H:i:s'))){
												$attendance->in_time = date('H:i:s',strtotime($eventStarttime));
											} else {
												$attendance->late_min = $mins;
												$attendance->in_time = $inTime;
											}

											$attendance->date = !empty($event_sche->date) ? date("l,d F,Y", strtotime($event_sche->date)) : '';
											if (empty($userattendfirst)) {
												if($eventData->is_free_event==0){
													$attendance->remaining_hour = $hour_point . ':00';
												}
											}
											$result = $attendance->save();
											if ($result) {
												return response()->json(array(
													'status' => 1,
													'message' => 'Attendance Added successfully',
													'redirecturl' => $redirect,
												));
											} else {
												return response()->json(array(
													'status' => 0,
													'message' => 'Something is wrong',
													'redirecturl' => $redirect,
												));
											}
										// } else {
										// 	return response()->json(array(
										// 		'status' => 0,
										// 		'message' => $message,
										// 		'redirecturl' => $redirect,
										// 	));
										// }
									// } else {
									// 	return response()->json(array(
									// 		'status' => 0,
									// 		'message' => 'Wait for the event start.',
									// 		'redirecturl' => $redirect,
									// 	));
									// }
								} else {
									return response()->json(array(
										'status' => 0,
										'message' => 'Today event is closed.',
										'redirecturl' => $redirect,
									));
								}
							} else {
								return response()->json(array(
									'status' => 0,
									'message' => 'Wait for the event start.',
									'redirecturl' => $redirect,
								));
							}
						} else {
							return response()->json(array(
								'status' => 0,
								'message' => 'Member Already Login',
								'redirecturl' => $redirect,
							));
						}
					}
					//Logout
					if ($request->type == 2) {
						$loginDetail = Attendance::where('event_id', $request->event_id)
							->where('user_id', $userid)->first();
						if (!empty($loginDetail)) {
							$attendance = Attendance::find($loginDetail->id);
							if ($attendance->out_time != '-') {
								return response()
									->json(array(
										'status' => 0,
										'message' => 'Member is Already Logout.',
										'redirecturl' => $redirect,
									));
							} else {
								$inTime = $attendance->in_time;
								$outTime = date('H:i');
								/* if ($outTime > $eventendtime)
									                                {
									                                    $diff = abs(strtotime($outTime) - strtotime($inTime));
									                                    $attendance->out_time = $outTime;
									                                }
									                                else
									                                {
									                                    $diff = abs(strtotime($eventendtime) - strtotime($inTime));
									                                    $attendance->out_time = $eventendtime;
								*/

								if ($outTime > $eventendtime) {
									$diff = abs(strtotime($outTime) - strtotime($inTime));
									$attendance->out_time = $outTime;
								} else {
									//$diff = abs(strtotime($eventendtime) - strtotime($inTime));
									$diff = abs(strtotime($outTime) - strtotime($inTime));
									$attendance->out_time = $eventendtime;
								}
								/*$tmins = $diff / 60;
									                                $hours = floor($tmins / 60);
									                                $mins = $tmins % 60;
								*/
								$mins = $diff / 60;
								$diff_hours = intdiv($mins, 60) . ':' . ($mins % 60);

								/** EARLY LATE MARGIN CALCULATION START **/

								$early_mins = abs(strtotime($eventendtime) - strtotime($outTime)) / 60;
								$early_hours = intdiv($mins, 60) . ':' . ($mins % 60);

								/** EARLY LATE MARGIN CALCULATION END **/

								/* $attendance->hours = $diff_hours;*/
								if ($early_mins > $this->globalmin) {
									$attendance->early_min = $early_mins;
								}
								$remaining_hour = 0;
								$remaining_hour = $this->attendanceremainingHour($userid, $diff_hours, $hour_point);

								$deduct_hour = 0;
								if ($attendance->late_min != NULL && $this->globalmin < $attendance->late_min) {
									$deduct_hour++;
								}
								if ($this->globalmin < $early_mins) {
									$deduct_hour++;
								}
								$deduct_hour = $deduct_hour . ':00';

								$diff_deduct_minit = abs(strtotime($diff_hours) - strtotime($deduct_hour)) / 60;
								$diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);

								$attendance->hours = $diff_deduct_hour;

								if ($eventData->event_type == '1') {
									$attendance->training_hour = $diff_deduct_hour;
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								} else if ($eventData->event_type == '2') {
									$attendance->activity_hour = $diff_deduct_hour;
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								} else {
									$attendance->service_hour = $diff_deduct_hour;
									$attendance->remaining_hour = $remaining_hour;
								}

								$result = $attendance->save();
								if ($result) {
									/* $Setting = Settings::first();
									// Add Member tokens 'Per Hours 1 Token Incresed'
									$MemberToken = new MemberToken;
									$MemberToken->user_id = !empty($userid) ? $userid : NULL;
									$MemberToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
									$MemberToken->token = (date('H', strtotime($diff_deduct_hour)));
									$MemberToken->remaining_token = (date('H', strtotime($diff_deduct_hour)));
									$MemberToken->expired_at = date('Y-m-d h:i:s', strtotime('+' . $Setting->token_expire_day . ' days'));
									$saveMemberToken = $MemberToken->save();

									$MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
									if (!empty($MemberTokenStatus)){
										$MemberTokenStatus->total_token = ($MemberTokenStatus->total_token + date('H', strtotime($diff_deduct_hour)));
										$MemberTokenStatus->save();
									}else{
										$MemberTokenStatus = new MemberTokenStatus;
										$MemberTokenStatus->user_id = !empty($userid) ? $userid : NULL;
										$MemberTokenStatus->total_token = (date('H', strtotime($diff_deduct_hour)));
										$saveMemberToken = $MemberTokenStatus->save();
									*/

									// $UserToken = User::where('Role_ID', '2')->where('MemberCode', $explodeMember[1])->first();
									// if (!empty($UserToken))
									// {
									//     $UserToken->total_tokens = ($UserToken->total_tokens + $hours);
									//     $UserToken->save();
									// }
									return response()
										->json(array(
											'status' => 1,
											'message' => 'Member Logout successfully',
											'redirecturl' => $redirect,
										));
								} else {
									return response()->json(array(
										'status' => 0,
										'message' => 'Something is wrong',
										'redirecturl' => $redirect,
									));
								}
							}
						} else {
							return response()->json(array(
								'status' => 0,
								'message' => 'Member is not login',
								'redirecturl' => $redirect,
							));
						}
					}
				} else {
					return response()->json(array(
						'status' => 0,
						'message' => 'Event not found',
						'redirecturl' => $redirect,
					));
				}
			//}
		} else {
			return response()->json(array(
				'status' => 0,
				'message' => 'Invalid User',
				'redirecturl' => $redirect,
			));
		}
	}

	public function attendanceremainingHour($userid, $diff_hours, $hour_point) {
		$AttendanceDetail = Attendance::where('user_id', $userid)->get()->toArray();
		$count_user = count($AttendanceDetail);		
		$last_remaining_data = Attendance::selectRaw('id,user_id,remaining_hour')->where('user_id', $userid)->orderBy('id', 'desc')->first();
		if (!empty($last_remaining_data)) {
			$last_remaining_hour = $last_remaining_data->remaining_hour;
		}
		$remaining_hour = 0;
		if (!empty($AttendanceDetail)) {
			foreach ($AttendanceDetail as $val) {
				if ($count_user == '1') {
					$diff = strtotime($val['remaining_hour']) - strtotime($diff_hours);
					$tmins = $diff / 60;
					$hours = floor($tmins / 60);
					$mins = $tmins % 60;
					$remaining_hour = $hours . ':' . $mins;
				} else {
					if ($val['out_time'] != '-') {
						$diff = strtotime($val['remaining_hour']) - strtotime($diff_hours);
						$tmins = $diff / 60;
						$hours = floor($tmins / 60);
						$mins = $tmins % 60;
						$remaining_hour = $hours . ':' . $mins;
					}
				}
			}
			return $remaining_hour;
		}
	}

	/**
	 ** USE : get event attender list
	 *
	 */
	public function getEventAttenderList(Request $request) {
		$newDate = ($request->date == 'null') ? '' : $request->date;
		$new = date('l,d F,Y', strtotime($newDate));
		if (Session::get('user')['user_id'] != '1') {
			$query = Attendance::query();
			if(isset($request->date) && !empty($request->date)){
				$query->where('date', $new);
			}
			if(isset($request->type) && $request->type != 'null'){
				$query->where('event_type', $request->type);
			}
			if(isset($request->event_id) && !empty($request->event_id)){
				$query->where('event_id', $request->event_id);
			}
			//$query->where('user_id', Session::get('user')['user_id'])
			$query->where('user_id', Session::get('user')['user_id'])
				->whereHas('users', function($userQuery) use($request){
					if($request->search_member_name_code != ''){
						$userQuery->where('UserName', 'like', '%'.$request->search_member_name_code.'%')->orWhere('MemberCode', 'like', '%'.$request->search_member_name_code.'%');
					}
				})
				->whereHas('event', function($eventQuery) use($request){
					if($request->event_status != ''){
						$eventQuery->where('status',$request->event_status);
					}
					if($request->search_text != ''){
						$eventQuery->where('event_name', 'like', '%'.$request->search_text.'%')->orWhere('event_code', 'like', '%'.$request->search_text.'%');
					}
				})
				->with('eventType');
			$attendances = $query->get()->toArray();
		} else {
			$query = Attendance::query();
			if(isset($request->date) && !empty($request->date)){
				$query->where('date', $new);
			}
			if(isset($request->type) && $request->type != 'null'){
				$query->where('event_type', $request->type);
			}
			
			if(isset($request->event_id) && !empty($request->event_id) && $request->event_id != 'null'){
				$query->where('event_id', $request->event_id);
			}

			$query->with('users')->whereHas('users', function($userQuery) use($request){
					if($request->search_member_name_code != ''){
						$userQuery->where('MemberCode', 'like', '%'.$request->search_member_name_code.'%')->orWhere('UserName', 'like', '%'.$request->search_member_name_code.'%');
					}
				})
				->with('event')->whereHas('event', function($eventQuery) use($request){
					if($request->event_status != ''){
						$eventQuery->where('status',$request->event_status);
					}
					if($request->search_text != ''){
						$eventQuery->where('event_name', 'like', '%'.$request->search_text.'%')->orWhere('event_code', 'like', '%'.$request->search_text.'%');
					}
				})
			->with('eventType');
			$attendances = $query->get()->toArray();
		}
		$html = '';
		$EventType = 'event_type_name_' . app()->getLocale();
		$html .= '<table id="search-eventtable" class="table">
					<thead>
						<tr>
							<th>
								<input type="checkbox" name="attendanceIds[]" class="select-all-attendance-chkbox" value="all">
							</th>
							<th>' . __('languages.Attendance.Member_Code') . '</th>
							<th>' . __('languages.member.English_name') . '</th>
							<th>' . __('languages.member.Chinese_name') . '</th>
							<th>' . __('languages.event.Event Type') . '</th>
							<th>' . __('languages.Attendance.Date') . '</th>
							<th>' . __('languages.Attendance.In_Time') . '</th>
							<th>' . __('languages.Attendance.Out_Time') . '</th>
							<th>'.__('languages.Attendance.total_event_hour').'</th>
							<th>'.__('languages.Attendance.in_time_deducted_hour').'</th>
							<th>'.__('languages.Attendance.out_time_deducted_hour').'</th>
							<th>'.__('languages.Attendance.total_deducted_hour').'</th>
							<th>' .__('languages.training_hours').'</th>
							<th>'. __('languages.activity_hours').'</th>
							<th>'.__('languages.service_hours').'</th>
							<th>' . __('languages.Action') . '</th>
						</tr>
					</thead>
					<tbody>';
		
		if (!empty($attendances)) {
			foreach ($attendances as $key => $val) {
				if (!empty($val['users'])) {
					if ($val['users']['UserName']) {
						$name = isset($val['users']['UserName']) ? $val['users']['UserName'] : '';
					} else {
						$Chinese_name = $val['users']['Chinese_name'] ?? '';
						$English_name = $val['users']['English_name'] ?? '';
						$name = $val['users']['Chinese_name'] . '&' . $val['users']['English_name'];
					}
					if ($val['out_time'] == '-' && $val['hours'] == '-') {
						$out_time = '-';
						$hours = '-';
					} else {
						$out_time = date('h:i a', strtotime($val['out_time']));
						$hours = $val['hours'];
					}
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="attendanceIds[]" class="select-attendance-chkbox" value="'.$val["id"].'"></td>';
					if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
						$html .= '<td><a href="users/' . $val['users']['ID'] . '/edit">C' . $val['users']['MemberCode'] . '</a></td>';
					} else {
						$html .= '<td>C' . $val['users']['MemberCode'] . '</td>';
					}
					$event_name = isset($val['event']['event_name']) ? $val['event']['event_name'] : '';
					$html .= '<td>' . $English_name . '</td><td>'. $Chinese_name .'</td><td>' . $event_name . '</td>
					<td>' . $val['event_type'][$EventType] . '</td><td>' . date('d/m/Y', strtotime($val['date'])) . '</td>
					<td>' . date('h:i a', strtotime($val['in_time'])) . '</td><td>' . $out_time . '</td>';

					if($val['total_event_hours']){
						$html .= '<td>'.$val['total_event_hours'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					if($val['in_time_deducted_hour']){
						$html .= '<td>'.$val['in_time_deducted_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					if($val['out_time_deducted_hour']){
						$html .= '<td>'.$val['out_time_deducted_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					if($val['total_deducted_hour']){
						$html .= '<td>'.$val['total_deducted_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					
					if($val['training_hour'] != '00:00' && $val['training_hour'] != '0:00'){
						$html .= '<td>'.$val['training_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					if($val['activity_hour'] != '00:00' && $val['activity_hour'] != '0:00'){
						$html .= '<td>'.$val['activity_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}
					if($val['service_hour'] != '00:00' && $val['service_hour'] != '0:00'){
						$html .= '<td>'.$val['service_hour'].'</td>';
					}else{
						$html .= '<td>---</td>';
					}		

					$html .= '<td><a href="javascript:void(0);" data-id="' . $val['id'] . '" class="editAttendance"><i class="bx bx-edit-alt"></i></a><a href="javascript:void(0);" data-id="' . $val['id'] . '" class="deleteAttendance"><i class="bx bx-trash-alt"></i></a></td></tr>';
				}
			}
			$html .= '</tbody></table>';
			return $html;
		} else {
			return $html;
		}
	}

	/***
	 * USE : GET Events On change Event Type
	 */
	public function getEventTypeList(Request $request){
		// $events = Events::with('eventschedule')->where('event_type',$request->type)->get();
		$events = EventSchedule::with('events')->whereHas('events',function($q) use($request){
			$q->where('event_type',$request->type);
		})->groupBy('event_id')->get();
		
		if(!empty($events)){
			$html = '<select class="form-control" id="filter_event_type1" name="filter_event_type1">';
			$html .= '<option value="">Select</option>';
			foreach($events as $event){
				$html .= '<option value="'.$event['event_id'].'" data-event-schedule="'.$event['id'].'">'.$event['events']['event_name'].'</option>';
			}
			$html .= '</select>';
			echo $html;die;
			return $html;
		}
	}

	/**
	 ** USE : GENERATE QR CODE
	 *
	 */
	public function generateQRCode($id) {
		//$userData = User::where('Role_ID', '2')->where('ID', $id)->first();
		$userData = User::where('Role_ID', '2')->where('ID', $id)->first();
		$Email = '';
		if(!empty($userData->email)){
			$Email = base64_encode($userData->email);
			//$email_add = base64_encode($Email);
		}
		
		if (!empty($userData->UserName)) {
			$UserName = $userData->UserName;
		} else {
			$Chinese_name = $userData->Chinese_name;
			$English_name = $userData->English_name;
			$UserName = $Chinese_name . ' ' . $English_name;
		}
		$user_id = base64_encode($id);
		
		//$userdata = $user_id . "/" . $email_add . "/" . trim($UserName);
		$userdata = $user_id . "/" . $Email . "/" . trim($UserName);

		$public_path = public_path() . '/image';
		$qrCode = new QrCode($userdata);
		$qrCode->setSize(200);
		//$qrimag = trim($UserName) . "-" . time() . ".png";
		$qrimag = trim(str_replace("/","_",$UserName))."-".time().".png";
		$qrcodeimag = $qrCode->writeFile($public_path . '/' . $qrimag);
		$dataUri = $qrCode->writeDataUri();
		// $dataarr = array(
		// 	'name' => $UserName,
		// 	'email' => $userData->email,
		// 	'subject' => "Qrcode",
		// 	'qrcode' => $dataUri,
		// );
		// Mail::send('email.sendCredential',$dataarr, function ($message) use ($dataarr,$qrimag,$public_path,$Email) {
		//  $attchmentImage = $public_path.'/'.$qrimag;
		//  $message->to($Email)
		//  ->subject('Qr Code')
		//  ->attach($attchmentImage, [
		//      'as' => 'qrcode.png',
		//      'mime' => 'image/png'
		//  ]);
		// });

		/** Email Functionality **/
		// $EmailData['subject'] = 'Qr Code';
		// $EmailData['email'] = $userData->email;
		// $EmailData['data'] = $dataarr;
		// $EmailData['emailpage'] = 'email.sendCredential';
		// $EmailData['qrImage'] = $public_path . '/' . $qrimag;

		// Email sent using Queue Job
		//dispatch(new SendQRMailJob($EmailData));
		
		// Full path Store in database
		//$path = Config::get('app.asset_url') . '/image/' . $qrimag;
		$qrImage = User::find($id);
		$qrImage->QrCode = 'image/'.$qrimag;
		$qrImage->save();
		if ($qrImage) {
			$redirecturl = Config::get('app.asset_url') . '/image/' . $qrimag;

			return response()->json(array(
				'status' => 1,
				'url' => $redirecturl,
			));
		} else {
			return response()->json(array(
				'status' => 0,
			));
		}
	}

	public function attendanceReport() {
		if (!empty(Session::get('user')['user_id']) && Session::get('user')['user_id'] == 1) {
			$attendancesreport = Attendance::with('users')->with('event')
				->with('eventType')
				->orderBy('id', 'desc')
				->get()
				->toArray();
			$users = User::where('Role_ID', '!=', '1')->get()
				->toArray();
			$events = Events::where('status', '1')->orderBy('id', 'desc')
				->groupBy('event_code', 'occurs')
				->get()
				->toArray();
		} else {
			$attendancesreport = Attendance::where('user_id', Session::get('user')['user_id'])->with('users')
				->with('event')
				->with('eventType')
				->orderBy('id', 'desc')
				->get()
				->toArray();
			$users = User::where('ID', Session::get('user')['user_id'])->where('Status', '1')
				->where('Role_ID', '!=', '1')
				->get()
				->toArray();
			$events = Events::whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)')->where('status', '1')
				->groupBy('occurs')
				->get()
				->toArray();
		}
		$eventTypes = new EventType;
		$get_event_type_list = $eventTypes->get_event_type_select_list();
		return view('AttendanceManagement.attendance_report', compact('attendancesreport', 'users', 'events', 'get_event_type_list'));
	}

	public function attendancesearchReport(Request $request) {
		$member_name = !empty($request->member_name) ? $request->member_name : '';
		$filter_date = !empty($request->filter_date) ? $request->filter_date : '';
		$event_name = !empty($request->event_name) ? $request->event_name : '';
		$event_type = !empty($request->event_type) ? $request->event_type : '';
		$query = Attendance::with('users')->with('event')->with('eventType');
		$EventType = 'event_type_name_' . app()->getLocale();
		if (isset($member_name) && !empty($member_name)) {
			$query->where('user_id', $member_name);
		}
		if (isset($filter_date) && !empty($filter_date)) {
			$explode_event_date = array_map('trim',explode('-', $filter_date));
			// $start_date = date('m/d/Y',strtotime($expolde_event_date[0]));
			// $end_date = date('m/d/Y',strtotime($expolde_event_date[1]));
			//$from = date('2018-01-01');
			//$to = date('2018-05-02');
			// $search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE STR_TO_DATE(date, '%m/%d/%Y') BETWEEN STR_TO_DATE('".$start_date."', '%m/%d/%Y') AND STR_TO_DATE('".$end_date."', '%m/%d/%Y') AND status = 1 GROUP BY event_code"));
			// if(!empty($search_result)){
			//     $array = json_decode(json_encode($search_result), true);
			//     $ids = array_column($array, 'event_id');
			//     $query->whereIn('event_id', $ids);
			// }else{
			//     $query->where('event_id', 0);
			// }
			$start_date = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explode_event_date[0])));
			$end_date = date('Y-m-d', strtotime(Helper::DateConvert('/','-',$explode_event_date[1])));
			$query->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
			
			// $start_date = date('Y-m-d',strtotime($expolde_event_date[0]));
			// $end_date = date('Y-m-d',strtotime($expolde_event_date[1]));
			// $new_date = date('Y-m-d', strtotime('Wednesday,10 February,2021'));
			// $paydate_raw = DB::raw("STR_TO_DATE(`date`, '%Y-%m-%d')");
			// $query->whereBetween($paydate_raw, [$start_date, $new_date]);

		}
		if (isset($event_name) && !empty($event_name)) {
			$query->where('event_id', $event_name);
		}
		if (isset($event_type) && !empty($event_type)) {
			$query->where('event_type', $event_type);
		}
		if (!empty(Session::get('user')['role_id']) && Session::get('user')['role_id'] != '1') {
			$event_serach_data = $query->where('user_id', Session::get('user')['user_id'])
				->orderBY('id','DESC')
				->get()
				->toArray();
		} else {
			$event_serach_data = $query->orderBY('id','DESC')->get()
				->toArray();
		}

		$html = '';
		$html .= '<table id="attendanceSerachReporttable" class="table event-reportserach-cls">
					<thead>
						<tr>
							<th>
								<input type="checkbox" name="attendanceIds[]" class="select-all-attendance-chkbox" value="all">
							</th>
							<th>' . __('languages.event.Event Name') . '</th>
							<th>' . __('languages.event.Event Type') . '</th>
							<th>' . __('languages.Attendance.Event Date') . '</th>';
							// <th>' . __('languages.Attendance.Member_Name') . '</th>
							$html .='<th>' . __('languages.member.English_name') . '</th>
							<th>' . __('languages.member.Chinese_name') . '</th>
							<th>' .__('languages.training_hours').'</th>
							<th>'. __('languages.activity_hours').'</th>
							<th>'.__('languages.service_hours').'</th>
							<th>'. __('languages.Action') . '</th>
						</tr>
					</thead>
				<tbody>';
		if (!empty($event_serach_data)) {
			foreach ($event_serach_data as $val) {
				if (!empty($val['users'])) {
					if (!empty($val['event']['event_name'])) {
					$html .= '<tr>
								<td>
									<input type="checkbox" name="attendanceIds[]" class="select-attendance-chkbox" value="'.$val['id'].'">
								</td>
								<td>' . $val['event']['event_name'] . '</td>
								<td>' . $val['event_type'][$EventType] . '</td>
								<td>' . date('d/m/Y',strtotime(Helper::DateConvert(',','',$val['event']['startdate']))) . '</td>';
								$html .= '<td>' . $val['users']['English_name'] ?? ''. '</td>';
								$html .= '<td>' . $val['users']['Chinese_name'] ?? ''. '</td>';
								// if ($val['users']['UserName']) {
								// 	$html .= '<td>' . $val['users']['UserName'] . '</td>';
								// } else {
								// 	$html .= '<td>' . $val['users']['Chinese_name'] . ' & ' . $val['users']['English_name'] . '</td>';
								// }
								// $html .= '<td>' . $val['hours'] . '</td>
								// 	<td>' . $val['remaining_hour'] . '</td>
								// 	<td>' . $val['users']['hour_point'] . '</td>';

								if($val['training_hour'] != '00:00' && $val['training_hour'] != '0:00'){
									$html .= '<td>'.$val['training_hour'].'</td>';
								}else{
									$html .= '<td>---</td>';
								}
								if($val['activity_hour'] != '00:00' && $val['activity_hour'] != '0:00'){
									$html .= '<td>'.$val['activity_hour'].'</td>';
								}else{
									$html .= '<td>---</td>';
								}
								if($val['service_hour'] != '00:00' && $val['service_hour'] != '0:00'){
									$html .= '<td>'.$val['service_hour'].'</td>';
								}else{
									$html .= '<td>---</td>';
								}								
								$html .= '<td>
											<a href="' . url('attendance-report-detail', $val['id']) . '"><i class="bx bx-show-alt"></i></a>
										</td>
							</tr>';
					}
				}
			}
		}
		$html .= '</tbody></table>';
		echo $html;
	}

	public function attendancereportdetail($id) {
		$attendancesreportdetalis = Attendance::with('users')->with('event')
			->with('eventType')
			->find($id)->toArray();
		return view('AttendanceManagement.attendance_report_detail', compact('attendancesreportdetalis'));
	}

	public function attendanceEventListSearch(Request $request) {
		$filter_date_attendance_event = !empty($request->filter_date_attendance_event) ? Helper::dateFormatMDY('/','-',$request->filter_date_attendance_event) : '';		
		$html = '';
		if (!empty($filter_date_attendance_event)) {
			/* $expolde_event_date = explode('-', $filter_date_attendance_event);
				            $start_event_date = date('m/d/Y', strtotime($expolde_event_date[0]));
				            $end_event_date = date('m/d/Y', strtotime($expolde_event_date[1]));
			*/

			$search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE `date` = '" . $filter_date_attendance_event . "' AND status = 1 GROUP BY event_code"));

			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'id');
			} else {
				$html .= '<label for="users-list-role">' . __('languages.Attendance.Select_Event') . '</label>
                <fieldset class="form-group">
                <select class="form-control" id="event_id" name="event_id">
                <option value="">' . __('languages.Attendance.Select_Event_Name') . '</option>';
				$html .= '</select></fieldset>';
				return $html;
				exit;
			}
		}

		/* $Select_db = DB::table('event_schedule')->select('event_schedule.*', 'events.*', 'event_type.*')*/
		$Select_db = DB::table('event_schedule')->select('event_schedule.id as scheduleID', 'event_schedule.event_id', 'event_schedule.start_time as scheduleStartTime', 'event_schedule.end_time as scheduleEndTime', 'event_schedule.event_hours as scheduleEventHours', 'events.*', 'event_type.*')
			->join('events', 'events.id', 'event_schedule.event_id')
			->join('event_type', 'event_type.id', 'events.event_type')
			->where('event_schedule.status', 1);
		if (!empty($request->filter_date_attendance_event)) {
			$Select_db->whereIn('event_schedule.id', $ids);
		}

		if (Session::get('user')['user_id'] === 1 || Session::get('user')['role_id'] === 10) {
			$result = $Select_db->groupBy('event_schedule.event_code')
				->get()
				->toArray();	
		} else {
			$Select_db->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',events.event_assign_user)');
			$result = $Select_db->groupBy('event_schedule.event_code')
				->get()
				->toArray();
		}

		$html .= '<label for="users-list-role">' . __('languages.Attendance.Select_Event') . '</label>
        <fieldset class="form-group">
        <select class="form-control attendance_event_id" id="event_id" name="event_id">
        <option value="">' . __('languages.Attendance.Select_Event_Name') . '</option>';
		if (!empty($result)) {
			$EventType = 'event_type_name_' . app()->getLocale();
			foreach ($result as $val) {
				$html .= '<option value="' . $val->event_id . '" data-event-schedule="' . $val->scheduleID . '" data-event-type="' . $val->$EventType . '">' . $val->event_name . '</option>';
			}
		}
		$html .= '</select></fieldset>';
		echo $html;
	}

	public function attendanceEventListSearchDate(Request $request) {
		$filter_date_attendance_event = Helper::dateFormatMDY('/','-',$request->filter_date_attendance_event);		
		//$filter_date_attendance_event = date('m/d/Y',strtotime($request->filter_date_attendance_event));
		$filter_date_attendance_event = Helper::dateFormatMDY('-','-',$filter_date_attendance_event);
		//$filter_date_attendance_event = !empty($request->filter_date_attendance_event) ? Helper::dateConvertDDMMYYY('/','-',$request->filter_date_attendance_event) : '';
		$newDate = Helper::FulldateFormat('-','-',$filter_date_attendance_event);
		if (Session::get('user')['user_id'] != '1') {
			$query = Attendance::query();
			if ($newDate != 'null') {
				$query->where('date', $newDate);
			}
			if (!empty($request->type)) {
				$query->where('event_type', $request->type);
			}
			if(isset($request->event_id) && !empty($request->event_id)) {
				$query->where('event_id', $request->event_id);
			}
			$query->where('user_id', Session::get('user')['user_id'])
				->with('users')
				->with('event')
				->with('eventType');

			$attendances = $query->orderBY('id','DESC')->get()->toArray();
		} else {
			$query = Attendance::query();
			if ($newDate != 'null') {
				$query->where('date', $newDate);
			}
			if (!empty($request->type)) {
				$query->where('event_type', $request->type);
			}
			if(isset($request->event_id) && !empty($request->event_id)) {
				$query->where('event_id', $request->event_id);
			}
			$query->with('users')->with('event')->with('eventType');

			$attendances = $query->orderBY('id','DESC')->get()->toArray();
		}

		$html1 = '';
		$EventType = 'event_type_name_' . app()->getLocale();

		$html1 .= '<table id="search-eventtable" class="table">
   <thead>
   <tr>
   <th>
   		<input type="checkbox" name="attendanceIds[]" class="select-all-attendance-chkbox" value="all">
	</th>
   <th>' . __('languages.Attendance.Member_Code') . '</th>
   <th>' . __('languages.Attendance.Member_Name') . '</th>
   <th>' . __('languages.event.Event Name') . '</th>
   <th>' . __('languages.event.Event Type') . '</th>
   <th>' . __('languages.Attendance.Date') . '</th>
   <th>' . __('languages.Attendance.In_Time') . '</th>
   <th>' . __('languages.Attendance.Out_Time') . '</th>
   <th>'.__('languages.Attendance.total_event_hour').'</th>
   <th>'.__('languages.Attendance.in_time_deducted_hour').'</th>
   <th>'.__('languages.Attendance.out_time_deducted_hour').'</th>
   <th>'.__('languages.Attendance.total_deducted_hour').'</th>
   <th>' .__('languages.training_hours').'</th>
	<th>'. __('languages.activity_hours').'</th>
	<th>'.__('languages.service_hours').'</th>
   <th>' . __('languages.Action') . '</th>
   </tr>
   </thead>
   <tbody>';
		if (!empty($attendances)) {
			foreach ($attendances as $key => $val) {
				if (!empty($val['users'])) {
					if (!empty($val['users']['UserName'])) {
						$name = $val['users']['UserName'];
					} else {
						$name = $val['users']['Chinese_name'] . '&' . $val['users']['English_name'];
						/*   $name = !empty($val['users']['English_name']) ? $val['users']['English_name'] :'';*/
					}
					if ($val['out_time'] == '-' && $val['hours'] == '-') {
						$out_time = '-';
						$hours = '-';
					} else {
						$out_time = date('H:i:s a', strtotime($val['out_time']));
						$hours = $val['hours'];
					}
					$html1 .= '<tr>';
					$html1 .= '<td>'.'<input type="checkbox" name="attendanceIds[]" class="select-attendance-chkbox" value="'.$val['id'].'"></td>';
					if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
						$html1 .= '<td><a href="users/' . $val['users']['ID'] . '/edit">C' . $val['users']['MemberCode'] . '</a></td>';
					} else {
						$html1 .= '<td>C' . $val['users']['MemberCode'] . '</td>';
					}
					$html1 .= 
					'<td>' . $name . '</td><td>' . $val['event']['event_name'] . '</td>
					<td>' . $val['event_type'][$EventType] . '</td>
					<td>' .  Helper::dateConvertDDMMYYY(',','',$val['date']) . '</td>
					<td>' . date('H:i:s a', strtotime($val['in_time'])) . '</td>
					<td>' . $out_time . '</td>';
					if($val['total_event_hours']){
						$html1 .= '<td>'.$val['total_event_hours'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					if($val['in_time_deducted_hour']){
						$html1 .= '<td>'.$val['in_time_deducted_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					if($val['out_time_deducted_hour']){
						$html1 .= '<td>'.$val['out_time_deducted_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					if($val['total_deducted_hour']){
						$html1 .= '<td>'.$val['total_deducted_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					// if($hours){
					// 	$html1 .= '<td>'.$hours.'</td>';
					// }else{
					// 	$html1 .= '<td>---</td>';
					// }

					if($val['training_hour'] != '00:00' && $val['training_hour'] != '0:00'){
						$html1 .= '<td>'.$val['training_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					if($val['activity_hour'] != '00:00' && $val['activity_hour'] != '0:00'){
						$html1 .= '<td>'.$val['activity_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}
					if($val['service_hour'] != '00:00' && $val['service_hour'] != '0:00'){
						$html1 .= '<td>'.$val['service_hour'].'</td>';
					}else{
						$html1 .= '<td>---</td>';
					}		
					
					$html1 .= '<td>
						<a href="javascript:void(0);" data-id="' . $val['id'] . '" class="editAttendance">
							<i class="bx bx-edit-alt"></i>
						</a>
						<a href="javascript:void(0);" data-id="' . $val['id'] . '" class="deleteAttendance">
							<i class="bx bx-trash-alt"></i>
						</a>
					</td>
				</tr>';
				}

			}
			$html1 .= '</tbody></table>';
		}
		return $html1;
	}

	/**
	 ** USE : EXPIRED STATUS TOKEN
	 *
	 */
	public function expiredToken() {
		$currentdate = date('Y-m-d');
		$allRecord = MemberToken::where('status', 0)->where('expired', 0)
			->where('expired_at', '<', $currentdate . '23:59:59')->get()
			->toArray();
		if (!empty($allRecord)) {
			foreach ($allRecord as $key => $value) {
				$MemberTokenStatus = MemberTokenStatus::where('user_id', $value['user_id'])->first();
				$MemberTokenStatus->total_token = ($MemberTokenStatus->total_token - $value['remaining_token']);
				$saveMemberTokenStatus = $MemberTokenStatus->save();

				$token = MemberToken::where('id', $value['id'])->first();
				$token->expired = '1';
				$saveToken = $token->save();
			}
			echo 'success';
		} else {
			echo 'No record found.';
		}
	}

	/**
	 **  USE : Token Report
	 *
	 */
	public function transactionHistory() {

		if (Session::get('user')['role_id'] == '1') {
			$transactionHistory = MemberUsedToken::with('users')->with('event')
				->orderBy('id','desc')
				->get()
				->toArray();
			return view('AttendanceManagement.transaction_history', compact('transactionHistory'));
		}
	}

	/**
	 ** USE : MEMBER'S TOKEN ADJUSTMENT LIST
	 *
	 */
	public function tokenManagement() {
		// if (Session::get('user')['role_id'] == '1') {
		// 	$tokenList = MemberToken::with('users')->with('event')
		// 		->where('status', 0)
		// 		->where('expired', 0)
		// 		->whereDate('expired_at', '>=', date('Y-m-d'))
		// 		->get()
		// 		->toArray();
		// 	return view('TokenManagement.token_management', compact('tokenList'));
		// }
		if (Session::get('user')['role_id'] == '1') {
			$tokenList = EventTokenManage::with('users')->with('EventSchedule','EventSchedule.events')->orderBy('id','DESC')->get()->toArray();
			return view('TokenManagement.token_management', compact('tokenList'));
		}
	}

	/**
	 ** USE : MEMBER TOKEN EDIT PAGE VIEW
	 *
	 */
	public function editToken($id) {
		if (Session::get('user')['role_id'] == '1') {
			$tokenData = EventTokenManage::with('users')->with('EventSchedule','EventSchedule.events')
				->where('id', $id)->first();				
			return view('TokenManagement.edit_token_management', compact('tokenData'));
		}
	}

	/**
	 ** USE : MEMBER TOKEN UPDATE CODE
	 *
	 */
	public function updateToken($id, Request $request) {
		$date = DateTime::createFromFormat('m/d/Y', $request->expired_at);
		$currentDate = Carbon::now()->format('Y-m-d'); 
		$expired_at = $date->format('Y-m-d');
		$status = ($currentDate <= $expired_at) ? 'active' : 'expried';
		$remaining_token = $request->token - $request->used_token;
		$tokenData = EventTokenManage::find($id);
		 $rules = array(
            'token' => 'required|gt:0',
            'used_token' => 'required|lt:token|gt:-1',
			'expired_at' => 'required',
		);	
		$messages = array(
            'token.required' => 'Please Enter Token',
			'used_token.required' => 'Please Enter Used Token',
			'expired_at.required' => 'Please Enter Expired Date',
			'used_token.lt' => 'Used token shoud be less then generated token',
			'token.gt' => 'Generate token value cannot be negative',
			'used_token.gt' => 'Used token value cannot be negative',
		);
		if ($this->validate($request, $rules, $messages) === FALSE) {
			return redirect()->back()->withInput();
		}	
		$postData = [
			'generate_token' => $request->token,
			'used_token' => $request->used_token,
			'remaining_token' => $remaining_token,
			'expire_date' => $expired_at,
			'status' => $status,
		];

		Helper::AuditLogfuncation($postData, new EventTokenManage, 'id', $id, 'event_token_manage', 'Attendance');

		$result = $tokenData->update($postData);

		if ($result) {
			return redirect('token-management')->with('success_msg', 'Token updated successfully.');
		} else {
			return back()->with('error_msg', 'Problem was error accured.. Please try again..');
		}
	}

	/**   USE : CRON JOB URL FOR EVENT CLOSE LOGOUT IN ONE HOUR  **/
	public function closeEventLogout() {
		Log::info('Closing Event Cron-Job Start : '.date('Y-m-d h:i:s'));
		$eventIds = [];
		$currentTime = date('H:i');
		$todayDate = date('m/d/Y');

		//$currentTime = '15:00';
		//$todayDate = date('m/d/Y',strtotime('29-12-2022'));
		$events = EventSchedule::with('events')->where('date','>',$todayDate)->orderBY('id','DESC')->get()->toArray();
		if(!empty($events)){
			$eventIds = array_column($events,'id');
			foreach ($events as $key => $event) {
				//echo '<pre>';print_r($event);die;
				$event_time = strtotime($event['end_time']);
				$diff = $event_time - strtotime($currentTime);
				//echo $diff;die;
				if ($diff < 0 || $diff == 0) {

					EventSchedule::where('event_id',$event['event_id'])->where('date',$todayDate)->update(['status' => 4]);
					if(EventSchedule::where('event_id',$event['event_id'])->where('status',1)->doesntExist()){
						Events::where('id',$event['event_id'])->Update(['status' => 4]);
					}
					// Events::where('id',$event['event_id'])->Update(['status' => 4]);
					$allAttendance = Attendance::where('event_id', $event['event_id'])
									->where('out_time', '=', '-')
									->where('hours', '=', '-')
									->orWhere('hours','=','NaN')
									->get();					
					if (!$allAttendance->isEmpty()) {						
						foreach ($allAttendance as $key => $attendanceVal) {
							$attendance = Attendance::find($attendanceVal->id);
							$userData = User::where('Role_ID', '2')->where('ID', $attendance->user_id)->first();
							if (!empty($userData)) {
								$hour_point = $userData->hour_point;
							}
							//if (!empty($hour_point)) {
								$inTime = $attendance->in_time;
								$outTime = $event['end_time'];

								/** Logic for deduct hours */
								$totalEventHours = 0;
								$totalEventTimes = Helper::getEventHotalTimes($attendance->event_schedule_id);
								if(!empty($totalEventTimes)){
									$totalEventHours = $totalEventTimes['difference']->h;
									$attendance->total_event_hours = $totalEventHours ?? 0;
								}

								// Find Intime diduct hours
								$InTimeDeductHours = 0;
								$InTimeDeductHours = Helper::deductHours($attendance->late_min);
								$attendance->in_time_deducted_hour = $InTimeDeductHours ?? 0;

								// Find earlier no of Minutes of leave event
								$EarlierMinutes = 0;
								$EarlierMinutes = Helper::getMinutesOfEarlierLeaveEvent($attendance->event_schedule_id, $outTime);
								$attendance->early_min = $EarlierMinutes ?? 0;

								// Find Outtime deduct hours
								$OutTimeDeductHour = 0;
								$OutTimeDeductHour = Helper::deductHours($EarlierMinutes);
								$attendance->out_time_deducted_hour = $OutTimeDeductHour ?? 0;
								$attendance->total_deducted_hour = ($InTimeDeductHours + $OutTimeDeductHour);
								$deduct_hour = ($InTimeDeductHours + $OutTimeDeductHour);
								$attendance->hours = ($totalEventHours - ($InTimeDeductHours + $OutTimeDeductHour)) ?? '-';
								$attendance->out_time = $outTime;
								$diff_deduct_hour = $deduct_hour . ':00';

								$remaining_hour = 0;
								$remaining_hour = $this->attendanceremainingHour($attendance->user_id, $diff_deduct_hour, $hour_point);

								// Find the calculate hours
								// $diff = abs(strtotime($outTime) - strtotime($inTime));
								// $mins = $diff / 60;
								// $diff_hours = intdiv($mins, 60) . ':' . ($mins % 60);
								// $remaining_hour = 0;
								// $remaining_hour = $this->attendanceremainingHour($attendance->user_id, $diff_hours, $hour_point);

								// $deduct_hour = 0;
								// if ($attendance->late_min != NULL && $this->globalmin < $attendance->late_min) {
								// 	$deduct_hour++;
								// }
								//$deduct_hour = $deduct_hour . ':00';

								// $diff_deduct_minit = abs(strtotime($diff_hours) - strtotime($deduct_hour)) / 60;
								// $diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);
								
								// Update field value
								//$attendance->hours = $diff_deduct_hour;
								// if ($event['events']['event_type'] == '1') {
								// 	$attendance->training_hour = $diff_deduct_hour;
								// 	$attendance->service_hour = $diff_deduct_hour;
								// 	$attendance->remaining_hour = $remaining_hour;
								// } else if ($event['events']['event_type'] == '2') {
								// 	$attendance->activity_hour = $diff_deduct_hour;
								// 	$attendance->service_hour = $diff_deduct_hour;
								// 	$attendance->remaining_hour = $remaining_hour;
								// } else {
								// 	$attendance->service_hour = $diff_deduct_hour;
								// 	$attendance->remaining_hour = $remaining_hour;
								// }

								if ($event['events']['event_type'] == '1') {
									$attendance->training_hour = $attendance->hours;
									$attendance->service_hour = $attendance->hours;
									$attendance->remaining_hour = $remaining_hour;
								} else if ($event['events']['event_type'] == '2') {
									$attendance->activity_hour = $attendance->hours;
									$attendance->service_hour = $attendance->hours;
									$attendance->remaining_hour = $remaining_hour;
								} else {
									$attendance->service_hour = $attendance->hours;
									$attendance->remaining_hour = $remaining_hour;
								}

								$result = $attendance->save();
								/** Update after event closed add token into user profile */
								$this->generateEventUserToken($event['id'], $attendance->id, $diff_deduct_hour);
								/** End Update after event closed add token into user profile */
							//}
						}
					}
				}
			}
			// After sucessfull run cron-job send email to admin email-notification
			$sendMail = Mail::send(['html' => 'email.close_event_cron_job_notification'], [], function ($message){
				$message->to(Config::get('mail.cron_job_send_email_address'),'Admin' ?? '');
				$message->subject('Closing Events Cron-Job Run Successfully');
			});
			Log::info('Closing Event Cron-Job Run Successfully : '.date('Y-m-d h:i:s').' => ( Closed Event ids : ['.implode(',',$eventIds).'])');
			echo 'Event closing cron job run successfully';exit;
		} else {
			Log::info('Closing Event Cron-Job Run Successfully : '.date('Y-m-d h:i:s').' [No any event closing today]');
			echo 'Event closing cron job run successfully';exit;
		}
	}

	// public function getDiffrenceOfDiductHours($OutTime, $InTime){
	// 	$hours = 0;
	// 	$Diffrence = round((strtotime($OutTime) - strtotime($InTime))/3600, 1);
	// 	if(!empty($Diffrence)){
	// 		$ExplodeTime = explode('.',$Diffrence);
	// 		$hours = $ExplodeTime[0];
	// 	}
	// 	return $hours;
	// }

	/**
	 * USE : Update User Token after closing events using cron job
	 */
	public function generateEventUserToken($eventScheduleId, $attendanceId, $diff_deduct_hour = 0){
		$Setting = Settings::first();
		$eventSchedule = EventSchedule::find($eventScheduleId);
		if(!empty($eventSchedule)){
			$attendance = Attendance::find($attendanceId);
			if(!empty($attendance)){
				// Generate new token based on attempted event hours Ex: User can ateempted per 1 Hours = 1 Token, 2 Hours = 2 Token
				//$NoOfGenerateToken = (date('H', strtotime($diff_deduct_hour))) ?? 0;
				$NoOfGenerateToken = $attendance->hours ?? 0;
				//if(!empty($NoOfGenerateToken)){
					if(EventTokenManage::where([
						'user_id' => $attendance->user_id,
						'event_id' => $eventScheduleId
					])->exists()){
						$EventTokenManage = EventTokenManage::where(['user_id' => $attendance->user_id,'event_id' => $eventScheduleId])->first();
						$EventTokenManage->generate_token = $NoOfGenerateToken;
						$EventTokenManage->save();
					}else{
						// Save data into 'event_token_manage' tables
						$AddToken = EventTokenManage::create([
							'user_id' => $attendance->user_id,
							//'event_id' => $attendance->event_id,
							'event_id' => $attendance->event_schedule_id,
							'generate_token' => $NoOfGenerateToken,
							'expire_date' => date('Y-m-d', strtotime('+' . $Setting->token_expire_day . ' days'))
						]);
					}
				//}
			}
		}
	}

	public function attendanceEventList(Request $request) {

		$Attendance = Attendance::with('users')->with('event')
			->with('eventType')
			->get()
			->toArray();
		$html = '';
		$EventType = 'event_type_name_' . app()->getLocale();

		$html .= '<table id="search-eventtable" class="table">
        <thead>
        <tr>
        <th>' . __('languages.Attendance.Member_Code') . '</th>
        <th>' . __('languages.Attendance.Member_Name') . '</th>
        <th>' . __('languages.event.Event Name') . '</th>
        <th>' . __('languages.event.Event Type') . '</th>
        <th>' . __('languages.Attendance.Date') . '</th>
        <th>' . __('languages.Attendance.In_Time') . '</th>
        <th>' . __('languages.Attendance.Out_Time') . '</th>
        <th>' . __('languages.Attendance.Hours') . '</th>
        <th>' . __('languages.Action') . '</th>
        </tr>
        </thead>
        <tbody>';
		if (!empty($Attendance)) {
			foreach ($Attendance as $key => $val) {
				if ($val['users']['UserName']) {
					$name = $val['users']['UserName'];
				} else {
					$name = $val['users']['Chinese_name'] . '&' . $val['users']['English_name'];
				}
				if ($val['out_time'] == '-' && $val['hours'] == '-') {
					$out_time = '-';
					$hours = '-';
				} else {
					$out_time = date('h:i a', strtotime($val['out_time']));
					$hours = $val['hours'];
				}
				$html .= '<tr>';
				if (in_array('members_write', Helper::module_permission(Session::get('user')['role_id']))) {
					$html .= '<td><a href="users/' . $val['users']['ID'] . '/edit">C' . $val['users']['MemberCode'] . '</a></td>';
				} else {
					$html .= '<td>C' . $val['users']['MemberCode'] . '</td>';
				}
				$html .= '<td>' . $name . '</td><td>' . $val['event']['event_name'] . '</td>
                <td>' . $val['event_type'][$EventType] . '</td><td>' . date('d/m/Y', strtotime($val['date'])) . '</td>
                <td>' . date('h:i a', strtotime($val['in_time'])) . '</td><td>' . $out_time . '</td>
                <td>' . $hours . '</td><td><a href="javascript:void(0);" data-id="' . $val['id'] . '" class="editAttendance"><i class="bx bx-edit-alt"></i></a><a href="javascript:void(0);" data-id="' . $val['id'] . '" class="deleteAttendance"><i class="bx bx-trash-alt"></i></a></td></tr>';
			}
			$html .= '</tbody></table>';
			return $html;
		}
	}

	public function checkQR() {

		$Attendance = '';
		if (!empty($request->event_id)) {
			$id = Helper::decodekey($request->event_id);
			$get_event_code = Events::where('id', $id)->first();
			$event_code = $get_event_code->event_code;
			if (Session::get('user')['role_id'] != '1') {
				if (!empty($event_code)) {
					$events = EventSchedule::with('events')->whereHas('events', function ($query) {
						$query->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)');
					})
						->where('event_code', $event_code)->where('status', '1')
						->groupBy('occurs')
						->get()
						->toArray();
					//$events = EventSchedule::with('events')->whereHas('events', function ($query) {$query->whereRaw('FIND_IN_SET('.Session::get('user')['user_id'].',event_assign_user)');})->where('event_code',$event_code)->where('date',date('m/d/Y'))->where('status','1')->get()->toArray();
					$Attendance = Attendance::where('user_id', Session::get('user')['user_id'])->where('event_id', $id)->with('users')
						->with('event')
						->with('eventType')
						->get()
						->toArray();
				}
			} else {
				$events = EventSchedule::with('events')->where('event_code', $event_code)->where('status', '1')
					->groupBy('occurs')
					->get()
					->toArray();
				//$events = EventSchedule::with('events')->where('event_code',$event_code)->where('date',date('m/d/Y'))->where('status','1')->get()->toArray();
				$Attendance = Attendance::where('event_id', $id)->with('users')
					->with('event')
					->with('eventType')
					->get()
					->toArray();
			}
		} else {
			if (Session::get('user')['role_id'] != '1') {
				$events = EventSchedule::with('events')->whereHas('events', function ($query) {
					$query->whereRaw('FIND_IN_SET(' . Session::get('user')['user_id'] . ',event_assign_user)');
				})
					->where('date', date('m/d/Y'))
					->where('status', '1')
					->get()
					->toArray();
				$Attendance = Attendance::where('user_id', Session::get('user')['user_id'])->with('users')
					->with('event')
					->with('eventType')
					->get()
					->toArray();
			} else {
				$events = EventSchedule::with('events')->where('date', date('m/d/Y'))
					->where('status', '1')
					->get()
					->toArray();
				$Attendance = Attendance::with('users')->with('event')
					->with('eventType')
					->get()
					->toArray();
			}
		}
		return view('checkQR', compact('events', 'Attendance'));
	}
}