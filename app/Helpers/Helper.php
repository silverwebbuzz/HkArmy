<?php

namespace App\Helpers;

//use Illuminate\Support\Facades\Mail;
use App\Http\Models\AuditLog;
use App\Http\Models\EilteModel;
use App\Http\Models\Events;
use App\Http\Models\EventSchedule;
use App\Http\Models\EventType;
use App\Http\Models\ProductCosttypeModel;
use App\Http\Models\QualificationModel;
use App\Http\Models\Remarks;
use App\Http\Models\Settings;
use App\Http\Models\SubElite;
use App\Http\Models\Subteam;
use App\Http\Models\EventTokenManage;
use App\Http\Models\User;
use App\Http\Models\AwardsBadgesCategories;
use App\Http\Models\EventAssignModel;
use App\Http\Models\EventPosttypeModel;
use App\Http\Models\ProductAssignModel;
use App\Http\Models\Attendance;
use App\Http\Models\AssignProductOrder;
use App\Http\Models\ChildProduct;
use App\Role;
use Auth;
use Mail;
use Session;

class Helper {

	public static function get_transaction_history_child_product($id){
		$ChildProductHtml = '';
		if(isset($id) && !empty($id)){
			$ProductAssignModel = ProductAssignModel::find($id);
			if(isset($ProductAssignModel) && !empty($ProductAssignModel) && !empty($ProductAssignModel->child_product_id)){
				$ChildProductIds = explode(',',$ProductAssignModel->child_product_id);
				if(isset($ChildProductIds) && !empty($ChildProductIds)){
					$ChildProduct = ChildProduct::with('Product')->whereIn('id',$ChildProductIds)->get();
					if(isset($ChildProduct) && !empty($ChildProduct)){
						foreach($ChildProduct as $child_product){
							$ChildProductHtml .='<p>'.$child_product->product->product_name.' : '.$child_product->product_suffix_name.'</br></p>';
						}
					}
				}
			}
		}
		return $ChildProductHtml;
	}

	public static function get_assign_product_order_child_product($id){
		$ChildProductHtml = '';
		if(isset($id) && !empty($id)){
			$AssignProductOrder = AssignProductOrder::find($id);
			if(isset($AssignProductOrder) && !empty($AssignProductOrder) && !empty($AssignProductOrder->child_product_id)){
				$ChildProductIds = explode(',',$AssignProductOrder->child_product_id);
				if(isset($ChildProductIds) && !empty($ChildProductIds)){
					$ChildProduct = ChildProduct::with('Product')->whereIn('id',$ChildProductIds)->get();
					if(isset($ChildProduct) && !empty($ChildProduct)){
						foreach($ChildProduct as $child_product){
							$ChildProductHtml .='<p>'.$child_product->product->product_name.' : '.$child_product->product_suffix_name.'</br></p>';
						}
					}
				}
			}
		}
		return $ChildProductHtml;
	}

	public static function getUserHours($memberid,$hourType){
		$Hours = 0;
		if($memberid){
			$AttendanceData = Attendance::where('user_id',$memberid)->get();
			if(!$AttendanceData->isEmpty()){
				foreach($AttendanceData as $attendance){
					if($hourType == 'training_hour'){
						if($attendance->training_hour != '00:00' && $attendance->training_hour != '0:00'){
							$Hours = ($Hours + $attendance->training_hour);
						}
					}
					if($hourType == 'activity_hour'){
						if($attendance->activity_hour != '00:00' && $attendance->activity_hour != '0:00'){
							$Hours = ($Hours + $attendance->activity_hour);
						}
					}
					if($hourType == 'service_hour'){
						if($attendance->service_hour != '00:00' && $attendance->service_hour != '0:00'){
							$Hours = ($Hours + $attendance->service_hour);
						}
					}

					if($hourType == 'total_hour'){
						if($attendance->training_hour != '00:00' && $attendance->training_hour != '0:00' &&
						$attendance->service_hour != '00:00' && $attendance->service_hour != '0:00' 
						&& $attendance->activity_hour != '00:00' && $attendance->activity_hour != '0:00'){
							$Hours = ($Hours + ($attendance->training_hour + $attendance->activity_hour + $attendance->service_hour));
						}
					}

				}
			}
		}
		return $Hours;
	}

	public static function getEventCostTypeHtml($eventid){
		$costTypehtml = '';
		$cost_type_id = EventAssignModel::where('event_id',$eventid)->get()->pluck('cost_type_id');
		if($cost_type_id){
			$EventCosttype = EventPosttypeModel::whereIn('id',$cost_type_id)->get()->toArray();
			if(isset($EventCosttype) && !empty($EventCosttype)){
				foreach($EventCosttype as $eventCostType){
					if($eventCostType['post_type'] == 1){
						$costTypehtml .= '<li>'.__('languages.member.Money').' : '.$eventCostType['post_value'].'</li>';
					}
					if($eventCostType['post_type'] == 2){
						$costTypehtml .= '<li>'.__('languages.member.Tokens').' : '.$eventCostType['post_value'].'</li>';
					}
					if($eventCostType['post_type'] == 3){
						$explodeEventCostType = explode("+",$eventCostType['post_value']);
						$costTypehtml .= '<li>'.__('languages.member.Money').' : '.$explodeEventCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeEventCostType[1].'</li>';
					}
				}
			}
		}

		return $costTypehtml;
	}

	public static function countUsersExistingTokens($user_id){
		$currentDate = date('Y-m-d');
		$ExpireDate = date('Y-m-d', strtotime('-30days'));
		// Get the count og user available tokens
		$CountEventTotalToken = EventTokenManage::where('user_id',$user_id)->where('status','active')->sum('remaining_token');		
		return $CountEventTotalToken;
	}

	public static function DateConvert($fromseprator,$toseprator,$date){
		$date = str_replace($fromseprator,$toseprator,$date);
		return $date;
	}
	/**
	 *USE : Date Formate D/M/Y Format 
	 **/
	public static function dateConvertDDMMYYY($fromseprator,$toseprator,$date){
		$date = str_replace($fromseprator,$toseprator,$date);
		return date('d/m/Y',strtotime($date));
	}

	/**
	 *USE : Date Formate M/D/Y Format 
	 **/
	public static function dateFormatMDY($fromseprator,$toseprator,$date){
		$date = str_replace($fromseprator,$toseprator,$date);
		return date('m/d/Y',strtotime($date));
	}

	public static function FulldateFormat($fromseprator,$toseprator,$date){
		//l,d F,Y
		$date = str_replace($fromseprator,$toseprator,$date);
		return date('l,d F,Y',strtotime($date));
	}

	/**
	 *USE : Get event type name
	 **/
	public static function getEventTypeName($id) {
		$event = EventType::select('event_type_name_' . app()->getLocale())->where('id', $id)->first();
		if (!empty($event)) {
			$name = 'event_type_name_' . app()->getLocale();
			return $event->$name;
		} else {
			return null;
		}
	}

	/**
	 * USE : Get User Permissions
	 */
	public static function getPermissions($roleid) {
		$permission = array();
		$data = Role::where('id', $roleid)->first()->toArray();
		if ($data) {
			$permission['role_id'] = $data['id'];
			$permission['role_name'] = $data['role_name'];
			$permission['permission'] = json_decode($data['permission']);
			return $permission;
		} else {
			return false;
		}
	}

	/**
	 * USE : Send email notification
	 */
	public static function sendMail($data, $html) {
		$data = ['data' => $data];
		$sendMail = Mail::send(['html' => $html], $data, function ($message) use ($data) {
			$message->to($data['data']['email'], $data['data']['name'] ?? '');
			$message->subject($data['data']['subject']);
		});
	}

	public static function getLastRefrenceNumber() {
		$unique_id = User::latest('MemberCode')->first();
		if ($unique_id) {
			return $unique_id->MemberCode;
		} else {
			$id = 01001;
			return $id;
		}
	}

	public static function getremarksData($id) {
		$remraks = Remarks::where('id', $id)->first();
		$languages = 'remarks_' . app()->getLocale();
		if (!empty($remraks)) {
			return $remraks->$languages;
		}
	}
	public static function geteliteData($id) {
		$elite = EilteModel::where('id', $id)->first();
		$languages = 'elite_' . app()->getLocale();
		if (!empty($elite)) {
			return $elite->$languages;
		}
	}
	public static function getSubeliteData($id) {
		$subelite = SubElite::where('id', $id)->first();
		$languages = 'subelite_' . app()->getLocale();
		if (!empty($subelite)) {
			return $subelite->$languages;
		}
	}

	public static function getSubteamData($id) {
		$Subteam = Subteam::where('id', $id)->first();
		$languages = 'subteam_' . app()->getLocale();
		if (!empty($Subteam)) {
			return $Subteam->$languages;
		}
	}

	public static function getqualificationData($id) {
		$Qualification = QualificationModel::where('id', $id)->first();
		$languages = 'qualification_' . app()->getLocale();
		if (!empty($Qualification)) {
			return $Qualification->$languages;
		}
	}

	public static function getLastEventNumber() {
		$unique_id = Events::latest('event_code')->first();
		if ($unique_id) {
			return $unique_id->event_code;
		} else {
			$id = 1000;
			return $id;
		}
	}

	public static function AuditLogfuncation($value, $table, $columname, $id, $tablename, $pagename) {
		$existingValue = $table::where($columname, $id)->first()->toArray();
		$postvalue = $value;
		$compare_arr = array();
		foreach ($existingValue as $key1 => $value1) {
			foreach ($postvalue as $key2 => $value2) {
				if ($key1 === $key2) {
					switch (true) {
					case ($value1 != $value2):
						$compare_arr[] = array(
							'old_value_' . $key1 => $value1,
							'new_value_' . $key2 => $value2,
						);
					default:
						//return false;
						break;
					}
				}
			}
		}
		if (!empty($compare_arr)) {
			$AuditLog = new AuditLog;
			$AuditLog->Log = json_encode($compare_arr);
			$AuditLog->Log_id = $id;
			$AuditLog->table_name = $tablename;
			$AuditLog->page = $pagename;
			$AuditLog->date = date('Y-m-d');
			if(Session::get('user')['user_id']){
				$AuditLog->user_id = Session::get('user')['user_id'];
			}
			$result = $AuditLog->save();
		}
	}

	public static function getsitesettings() {
		$settings = Settings::first();
		return $settings;
	}

	public static function module_permission($id) {
		$permissions = [];
		$role_id = $id;
		if ($role_id) {
			$module_permission = Helper::getPermissions($role_id);
			if ($module_permission && !empty($module_permission['permission'])) {
				return $permissions = $module_permission['permission'];
			}
		} else {
			return $permissions = [];
		}
	}

	public static function totalhourEvent($event_code) {
		$totalHour = Events::where('event_code', $event_code)->get()->toArray();

		$data = array();
		$stardate = array();
		$enddate = array();
		$totaleventhour = 0;
		if (!empty($totalHour)) {
			foreach ($totalHour as $val) {
				$stardate[] = $val['startdate'];
				$enddate[] = $val['enddate'];
				$totaleventhour += $val['event_hours'];
			}

			usort($stardate, function ($a, $b) {
				$dateTimestamp1 = strtotime($a);
				$dateTimestamp2 = strtotime($b);

				return $dateTimestamp1 < $dateTimestamp2 ? -1 : 1;
			});

			usort($enddate, function ($a, $b) {
				$dateTimestamp1 = strtotime($a);
				$dateTimestamp2 = strtotime($b);

				return $dateTimestamp1 > $dateTimestamp2 ? -1 : 1;
			});

			$data['minstartDate'] = $stardate[0];
			$data['maxendDate'] = $enddate[0];
			$data['totalhour'] = $totaleventhour;
		}
		return $data;
	}

	public static function encodekey($id) {
		$key = base64_encode($id);
		return str_replace(array('+', '/', '='), array('-', '_', '~'), $key);
	}

	public static function decodekey($id = 0) {
		$key = $key = base64_decode($id);
		$key = str_replace(array('-', '_', '~'), array('+', '/', '='), $key);
		return $key;
	}

	/**
	 *USE : Get Product Cost Type Token
	 **/
	public static function getProductToken($pid) {
		$tokens = ProductCosttypeModel::where('product_id', $pid)->where('cost_type', 2)->first();
		if (!empty($tokens)) {
			return $tokens->cost_value;
		} else {
			return null;
		}
	}

	public static function cleanString($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
	}

	public static function CheckCategoriesIsOther($catid){
		$style='';
		$result = AwardsBadgesCategories::find($catid);
		if(isset($result) && !empty($result)){
			$catName = $result->{'name_'.app()->getLocale()};
			if($catName == 'Others' || $catName == 'others' || $catName == '其他'){
				return 'true';
			}else{
				return 'false';
			}
		}
		return 'false';
	}

	public static function  getEventHotalTimes($eventScheduleId){
		$eventSchedule = EventSchedule::find($eventScheduleId);	
		$eventStartTime = date_create(date('H:i:s',strtotime($eventSchedule['start_time'])));
		$eventEndTime = date_create(date('H:i:s',strtotime($eventSchedule['end_time'])));
		$difference = date_diff($eventStartTime, $eventEndTime);
		$minutes = $difference->days * 24 * 60;
		$minutes += $difference->h * 60;
		$minutes += $difference->i;

		return [
			'difference' => $difference,
			'minutes' => $minutes
		];
	}

	public static function getMinutesOfLateInTimeEvent($eventScheduleId,$inTime){
		$minutes = 0;
		$eventSchedule = EventSchedule::find($eventScheduleId);
		$eventStartTime = date_create(date('H:i:s',strtotime($eventSchedule['start_time'])));
		$eventEndTime = date_create(date('H:i:s',strtotime($inTime)));
		$difference = date_diff($eventStartTime, $eventEndTime);
		$minutes = $difference->days * 24 * 60;
		$minutes += $difference->h * 60;
		$minutes += $difference->i;
		return $minutes;
	}

	public static function  getMinutesOfEarlierLeaveEvent($eventScheduleId,$outTime){
		$minutes = 0;
		$eventSchedule = EventSchedule::find($eventScheduleId);	
		$eventStartTime = date_create(date('H:i:s',strtotime($outTime)));
		$eventEndTime = date_create(date('H:i:s',strtotime($eventSchedule['end_time'])));
		$difference = date_diff($eventStartTime, $eventEndTime);
		$minutes = $difference->days * 24 * 60;
		$minutes += $difference->h * 60;
		$minutes += $difference->i;
		return $minutes;
	}

	public static function deductHours($minutes){
		$deductHours = 0;
		if(!empty($minutes)){
			if($minutes < 30){
				$deductHours = 0;
			}else if($minutes >= 30 && $minutes < 90){
				$deductHours = 1;
			}else if($minutes >= 90 && $minutes < 150){
				$deductHours = 2;
			}else if($minutes >= 150 && $minutes < 210){
				$deductHours = 3;
			}else if($minutes >= 210 && $minutes < 270){
				$deductHours = 4;
			}else if($minutes >= 270 && $minutes < 330){
				$deductHours = 5;
			}else if($minutes >= 330 && $minutes < 390){
				$deductHours = 6;
			}else if($minutes >= 390 && $minutes < 450){
				$deductHours = 7;
			}else if($minutes >= 450 && $minutes < 510){
				$deductHours = 8;
			}else if($minutes >= 510 && $minutes < 570){
				$deductHours = 9;
			}else if($minutes >= 570 && $minutes < 630){
				$deductHours = 10;
			}else if($minutes >= 630 && $minutes < 690){
				$deductHours = 11;
			}else if($minutes >= 690 && $minutes < 750){
				$deductHours = 12;
			}else if($minutes >= 750 && $minutes < 810){
				$deductHours = 13;
			}else if($minutes >= 810 && $minutes < 870){
				$deductHours = 14;
			}else if($minutes >= 870 && $minutes < 930){
				$deductHours = 15;
			}else if($minutes >= 930 && $minutes < 990){
				$deductHours = 16;
			}else if($minutes >= 990 && $minutes < 1050){
				$deductHours = 17;
			}else if($minutes >= 1050 && $minutes < 1110){
				$deductHours = 18;
			}else if($minutes >= 1110 && $minutes < 1170){
				$deductHours = 19;
			}else if($minutes >= 1170 && $minutes < 1230){
				$deductHours = 20;
			}else if($minutes >= 1230 && $minutes < 1290){
				$deductHours = 21;
			}else if($minutes >= 1290 && $minutes < 1350){
				$deductHours = 22;
			}else if($minutes >= 1350 && $minutes < 1410){
				$deductHours = 23;
			}else if($minutes >= 1410 && $minutes < 1440){
				$deductHours = 24;
			}else{
				$deductHours = 0;
			}
		}

		return $deductHours;
	}
	public static function getMemberHours($memberid){
		$hours['activityHour'] = 0;
		$hours['trainingHour'] = 0;
		$hours['serviceHour'] = 0;
		$hours['totalHour'] = 0;
		$hours['totalMinute'] = 0;
		$attendaceData = Attendance::where('user_id',$memberid)->get();
		if(!empty($attendaceData)){
			foreach($attendaceData as $attendance){
				if(!empty($attendance->activity_hour)){
					$activityValue = explode(':',$attendance->activity_hour);
					$hour_to_minute = (60 * intval($activityValue[0] ?? 0));
					$total_minute = ($hour_to_minute + intval($activityValue[1] ?? 0));
					$hours['activityHour'] += intval($total_minute);
				}
				if(!empty($attendance->service_hour)){
					$serviceValue = explode(':',$attendance->service_hour);
					$hour_to_minute = (60 * intval($serviceValue[0] ?? 0));
					$total_minute = ($hour_to_minute + intval($serviceValue[1] ?? 0));
					$hours['serviceHour'] += intval($total_minute);
				}
				if(!empty($attendance->training_hour)){
					$trainingValue = explode(':',$attendance->training_hour);
					$hour_to_minute = (60 * intval($trainingValue[0] ?? 0));
					$total_minute = ($hour_to_minute + intval($trainingValue[1] ?? 0));
					$hours['trainingHour'] += intval($total_minute);
				}
				$hours['totalMinute'] = (intval($hours['serviceHour']) + intval($hours['trainingHour']) + intval($hours['activityHour']));
			}
			
			$hours['totalHour'] = ($hours['totalMinute'] != 0) ? \App\Helpers\Helper::hoursandmins($hours['totalMinute']) : '00:00';
			$hours['activityHour'] = ($hours['activityHour'] != 0) ? \App\Helpers\Helper::hoursandmins($hours['activityHour']) : '00:00';
			$hours['trainingHour'] = ($hours['trainingHour'] != 0) ? \App\Helpers\Helper::hoursandmins($hours['trainingHour']) : '00:00';
			$hours['serviceHour'] = ($hours['serviceHour'] != 0) ? \App\Helpers\Helper::hoursandmins($hours['serviceHour']) : '00:00';
		}

		return $hours;
	}

	public static function hoursandmins($time, $format = '%02d:%02d')
	{
		if ($time < 1) {
			return;
		}
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		return sprintf($format, $hours, $minutes);
	}
}