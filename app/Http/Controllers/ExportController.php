<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Models\EventType;
use App\Http\Models\Events;
use App\Http\Models\User;
use App\Http\Models\EventSchedule;
use App\Http\Models\AssignAwards;
use App\Http\Models\RolePermission;
use Session;
use App\Http\Models\Attendance;
use App\Helpers\Helper;
use App\Http\Models\EventAssignModel;
use App\Http\Models\EventPosttypeModel;
use App\Http\Models\EventTokenManage;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductAssignModel;
use App\Http\Models\MemberUsedToken;
use App\Http\Models\BadgeAssign;
use App\Http\Models\EilteModel;
use App\Http\Models\Subteam;
use App\Http\Controllers\AttendanceController;
use Config;
use Carbon\Carbon;
use App\Http\Models\AssignProductOrder;

class ExportController extends Controller
{
    public function __construct() {
		$this->AttendanceController = new AttendanceController;
	}

    public function events(Request $request){
        $filter_date_event = !empty($request->filter_date_event) ? $request->filter_date_event : '';
		$filter_event_type = !empty($request->filter_event_type) ? $request->filter_event_type : '';
		$filter_occurs = !empty($request->filter_occurs) ? $request->filter_occurs : '';
        $ids = array();
        $rows = [];

        $query = Events::with('eventType','eventschedule');
        //Export Selected Row
        if(!empty($request->eventIds)){
            $query->whereIn('id',$request->eventIds);
        }

		//Filteration on Event Date
		if(!empty($filter_date_event)){
			$expolde_event_date = array_map('trim',explode('-', $filter_date_event));
			$start_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[0]);
			$end_event_date = Helper::dateFormatMDY('/','-',$expolde_event_date[1]);

			$query->whereHas('eventSchedule',function($q) use($start_event_date, $end_event_date){
				$q->whereBetween('date', [$start_event_date, $end_event_date]);
			})->get();
		}
		
		//Filtration on Event Type
		if (isset($request->filter_event_type) && !empty($request->filter_event_type)) {
			if($request->filter_event_type == 'all_service'){
				$query->whereHas('eventType',function($q) use($request){
					$q->where('type_id',3);
				})->get();
			}else{
				$query->where('event_type', $filter_event_type);
			}
		}

		//Filtration on Event Status
		if ($request->event_status != '') {
            $query->where('status',$request->event_status);            
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
       
        if (!empty($events)) {
            $i =0;
            foreach ($events as $eventKey => $value) {              
                // $rows[$eventKey]['event_name'] = ($value['event_name']) ? $value['event_name'] : '';
                // $rows[$eventKey]['event_type'] = ($value['event_type']['event_type_name_en']) ? $value['event_type']['event_type_name_en'] : '';
                // $rows[$eventKey]['event_code'] = ($value['event_code']) ? $value['event_code'] : '';
                // $rows[$eventKey]['startdate'] = ($value['event_start_date']) ? $value['event_start_date'] : '';
                // $rows[$eventKey]['enddate'] = ($value['event_end_date']) ? $value['event_end_date'] : '';
                // $rows[$eventKey]['start_time'] = ($value['start_time']) ? $value['start_time'] : '';
                // $rows[$eventKey]['end_time'] = ($value['end_time']) ? $value['end_time'] : '';
                // $rows[$eventKey]['event_hours'] = ($value['totaleventhour']) ? $value['totaleventhour'] : '';
                // $rows[$eventKey]['no_of_dates'] = ($value['no_of_dates']) ? $value['no_of_dates'] : 0;

                $rows[$i]['event_name'] = ($value['event_name']) ? $value['event_name'] : '';
                $rows[$i]['event_type'] = ($value['event_type']['event_type_name_en']) ? $value['event_type']['event_type_name_en'] : '';
                $rows[$i]['event_code'] = ($value['event_code']) ? $value['event_code'] : '';
                $rows[$i]['startdate'] = ($value['event_start_date']) ? $value['event_start_date'] : '';
                $rows[$i]['enddate'] = ($value['event_end_date']) ? $value['event_end_date'] : '';
                $rows[$i]['start_time'] = ($value['start_time']) ? $value['start_time'] : '';
                $rows[$i]['end_time'] = ($value['end_time']) ? $value['end_time'] : '';
                $rows[$i]['event_hours'] = ($value['totaleventhour']) ? $value['totaleventhour'] : '';
                $rows[$i]['no_of_dates'] = ($value['no_of_dates']) ? $value['no_of_dates'] : 0;
                
                foreach($value['eventschedule'] as $eventscheduleKey => $data){
                    $i += 1;
                    $rows[$i]['event_name'] = '';
                    $rows[$i]['event_type'] = '';
                    $rows[$i]['event_code'] = '';

                    $rows[$i]['startdate'] = ($data['date']) ? date('d/m/Y',strtotime($data['date'])) : '';
                    $rows[$i]['enddate'] = ($data['date']) ? date('d/m/Y',strtotime($data['date'])) : '';
                    $rows[$i]['start_time'] = ($data['start_time']) ? $data['start_time'] : '';
                    $rows[$i]['end_time'] = ($data['end_time']) ? $data['end_time'] : '';
                    $rows[$i]['event_hours'] =  $data['event_hours'];
                    $rows[$i]['no_of_dates'] =  '';
                }
                $i += 1;

            }
            
            $defaultColumnName = array(
                'event_name' => __('languages.export_fields.events.event_name'),
                'event_type' => __('languages.export_fields.events.event_type'),
                'event_code' => __('languages.export_fields.events.event_code'),
                'startdate' => __('languages.export_fields.events.event_start_date'),
                'enddate' => __('languages.export_fields.events.event_end_date'),
                'start_time' => __('languages.export_fields.events.event_start_time'),
                'end_time' => __('languages.export_fields.events.event_end_time'),
                'event_hours' => __('languages.export_fields.events.event_hours'),
                'no_of_dates' => __('languages.export_fields.events.no_of_dates')
            );
            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];
            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'events.csv';
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

    /**
     * USE : Export attendance-list
     */
    public function attendanceList(Request $request){
        if(isset($request->date) && $request->date != 'null'){
            $new = date('l,d F,Y', strtotime($request->date));
        }

        if (Session::get('user')['user_id'] != '1') {
			$query = Attendance::query();
            if(isset($request->AttendanceIds) && !empty($request->AttendanceIds)){
                $query->whereIn('id', $request->AttendanceIds);
            }
			if(isset($request->date) && $request->date != 'null'){
				$query->where('date', $new);
			}
			if(isset($request->type) && $request->type != 'null'){
				$query->where('event_type', $request->type);
			}
			if(isset($request->event_id) && !empty($request->event_id)){
				$query->where('event_id', $request->event_id);
			}
			//$query->where('user_id', Session::get('user')['user_id'])
            $query->whereHas('users', function($userQuery) use($request){
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
			$attendances = $query->orderBy('id','DESC')->get()->toArray();
		} else {
			$query = Attendance::query();
            if(isset($request->AttendanceIds) && !empty($request->AttendanceIds)){
                $query->whereIn('id', $request->AttendanceIds);
            }
			if(isset($request->date) && $request->date != 'null'){
				$query->where('date', $new);
			}
			if(isset($request->event_type) && $request->type != 'null'){
				$query->where('event_type', $request->type);
			}
			if(isset($request->event_id) && !empty($request->event_id)){
				$query->where('event_id', $request->event_id);
			}

			$query->with('users')->whereHas('users', function($userQuery) use($request){
					if($request->search_member_name_code != ''){
						$userQuery->where('MemberCode', 'like', '%'.$request->search_member_name_code.'%')
                        ->orWhere('UserName', 'like', '%'.$request->search_member_name_code.'%');
					}
				})
				->with('event')->whereHas('event', function($eventQuery) use($request){
					if($request->event_status != ''){
						$eventQuery->where('status',$request->event_status);
					}
					if($request->search_text != ''){
						$eventQuery->where('event_name', 'like', '%'.$request->search_text.'%')
                        ->orWhere('event_code', 'like', '%'.$request->search_text.'%');
					}
				})
			->with('eventType');
			$attendances = $query->orderBy('id','DESC')->get()->toArray();
		}

        if(isset($attendances) && !empty($attendances)){
            $rows = [];
            foreach ($attendances as $key => $value) {
                // echo "<pre>";print_r($value['in_time_deducted_hour']);die;
                $rows[$key]['member_code'] = ($value['member_code']) ? $value['member_code'] : '';
                // $rows[$key]['member_name'] = ($value['users']['English_name']) ? $value['users']['English_name'] : '';
                $rows[$key]['english_name'] = ($value['users']['English_name']) ? $value['users']['English_name'] : '';
                $rows[$key]['chinese_name'] = ($value['users']['Chinese_name']) ? $value['users']['Chinese_name'] : '';
                $rows[$key]['event_name'] = ($value['event']['event_name']) ? $value['event']['event_name'] : '';
                $rows[$key]['event_type'] = ($value['event_type']['event_type_name_en']) ? $value['event_type']['event_type_name_en'] : '';
                $rows[$key]['date'] = ($value['date']) ? date('d/m/Y',strtotime($value['date'])) : '';
                $rows[$key]['intime'] = ($value['in_time']) ? $value['in_time'] : '-';
                $rows[$key]['outtime'] = ($value['out_time']) ? $value['out_time'] : '-';
                $rows[$key]['total_event_hours'] = (!empty($value['total_event_hours']) && $value['total_event_hours'] != 0) ? $value['total_event_hours'] : '---';
                $rows[$key]['in_time_deducted_hour'] = (!empty($value['in_time_deducted_hour']) && $value['in_time_deducted_hour'] != 0) ? $value['in_time_deducted_hour'] : '---';
                $rows[$key]['out_time_deducted_hour'] = (!empty($value['out_time_deducted_hour']) && $value['out_time_deducted_hour'] != 0) ? $value['out_time_deducted_hour'] : '---';
                $rows[$key]['total_deducted_hour'] = (!empty($value['total_deducted_hour']) && $value['total_deducted_hour'] != 0) ? $value['total_deducted_hour'] : '---';
                $rows[$key]['hours'] = ($value['hours']) ? $value['hours'] : '';
            }

            $defaultColumnName = array(
                'member_code' => __('languages.Attendance.Member_Code'),
                // 'member_name' => __('languages.Attendance.Member_Name'),
                'english_name' => __('languages.UserManagement.english_name'),
                'chinese_name' => __('languages.UserManagement.chinese_name'),
                'event_name' => __('languages.Attendance.Event_Name'),
                'event_type' => __('languages.Attendance.Event_Type'),
                'date' => __('languages.Attendance.Date'),
                'intime' => __('languages.Attendance.In_Time'),
                'outtime' => __('languages.Attendance.Out_Time'),
                'total_event_hours' => __('languages.Attendance.total_event_hour'),
                'in_time_deducted_hour' => __('languages.Attendance.in_time_deducted_hour'),
                'out_time_deducted_hour' => __('languages.Attendance.out_time_deducted_hour'),
                'total_deducted_hour' => __('languages.Attendance.total_deducted_hour'),
                'hours' => __('languages.Attendance.Hours')
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];
            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AttendanceList.csv';
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

    public function awardAssignMember(Request $request){
        $filter_date_event = !empty($request->filter_date_event) ? $request->filter_date_event : '';
		$award_categories_id = !empty($request->award_categories) ? $request->award_categories : '';
		$award_member_id = !empty($request->member_id) ? $request->member_id : '';
        $award_reference_number = !empty($request->reference_number) ? $request->reference_number : '';
        $Model = AssignAwards::with('user')->with('award');
        $ids = array();
        if(!empty($request->awardAssignIds)){
            $Model->whereIn('id',$request->awardAssignIds);
        }
        if(!empty($filter_date_event)){
			$expolde_award_issue_date = array_map('trim',explode('-', $filter_date_event));
            $start_award_issue_date = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$expolde_award_issue_date[0])));
            $end_award_issue_date = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$expolde_award_issue_date[1])));
            $Model->whereBetween('issue_date', [$start_award_issue_date, $end_award_issue_date]);
		}

        // Search by award categories
        if(isset($award_categories_id) && !empty($award_categories_id)){
            $Model->where('award_id',$award_categories_id);
        }
        // Search by reference number
        if(isset($request->reference_number) && !empty($request->reference_number)){
            $Model->where('reference_number','like','%'.$request->reference_number.'%');
        }
        // Filter by member
        if(isset($request->member_id) && !empty($request->member_id) && $request->member_id != 'all'){
            $Model->where('user_id',$request->member_id);
        }
        $AwardsMemberList = $Model->orderBy('id','desc')->get();
        if (!empty($AwardsMemberList)) {
            $rows = [];
            foreach ($AwardsMemberList as $awardMemberKey => $value) {
                $rows[$awardMemberKey]['award_name'] = ($value->award->name_en) ? $value->award->name_en : '';
                // $rows[$awardMemberKey]['member_name'] = ($value->user->English_name) ? $value->user->English_name : '';
                $rows[$awardMemberKey]['english_name'] = $value->user->English_name ?? '';
                $rows[$awardMemberKey]['chinese_name'] = $value->user->Chinese_name ?? '';
                $rows[$awardMemberKey]['reference_number'] = ($value->reference_number) ? $value->reference_number : '';
                $rows[$awardMemberKey]['issue_date'] = ($value->issue_date) ? $value->issue_date : '';
                $rows[$awardMemberKey]['status'] = ($value->status=="active") ? 'Active' : 'Inactive';
            }

            $defaultColumnName = array(
                'award_name' => __('languages.award_member_list.award_name'),
                // 'member_name' => 'Member Name',
                'english_name'  => __('languages.UserManagement.english_name'),
                'chinese_name'  => __('languages.UserManagement.chinese_name'),
                'reference_number' => __('languages.award_member_list.reference_number'),
                'issue_date' => __('languages.award_member_list.issue_date'),
                'status' => __('languages.UserManagement.status')
            );

            $exportColumn = $request->columnList;

            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AwardAssignMember.csv';
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

    public function assignUserReport(Request $request){
        $filter_date_attendance = !empty($request->filter_date_attendance) ? Helper::dateFormatMDY('/','-',$request->filter_date_attendance) : '';
        $filter_event_type = !empty($request->filter_event_type) ? $request->filter_event_type : '';
		$filter_event = !empty($request->filter_event) ? $request->filter_event : '';

        $eventTypes = new EventType;
		if (empty($request->filter_event_type)) {
			$get_event_type_list = $eventTypes->get_event_type_select_list();
		}
		$query = Events::query()->with('eventCostType');
        if(!empty($request->assignUserReportIds)){
            $query->whereIn('id',$request->assignUserReportIds);
        }
        if ($request->filter_event_id) {
			$query->where('id', $request->filter_event_id);
		}

		if ($request->filter_event_type) {
			$get_event_type_list = $eventTypes->get_event_type_select_list($request->filter_event_type);
			$query->where('event_type', $request->filter_event_type);
		}
		if (!empty($filter_date_attendance)) {
			$search_result = EventSchedule::where('date', $filter_date_attendance)->where('status', '1')->get();
			$ids = [];
			if (!empty($search_result)) {
				$array = json_decode(json_encode($search_result), true);
				$ids = array_column($array, 'event_id');
				$query->whereIn('id', $ids);
			} else {
				$query->whereIn('id', $ids);
			}
		}
		$EventList = $query->where('status', '1')->orderBy('id','DESC')->get()->toArray();
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
        if (!empty($EventList)) {
            $rows = [];
            foreach ($EventList as $eventKey => $value) {
                //echo "<pre>";print_r($value);die;
                $rows[$eventKey]['event_code'] = ($value['event_code']) ? $value['event_code'] : '';
                $rows[$eventKey]['event_name'] = ($value['event_name']) ?$value['event_name'] : '';
                $rows[$eventKey]['date'] = ($value['event_start_date']) ? $value['event_start_date'] : '';

                $eventCostValue = '';
                if(isset($value['event_cost_type']) && !empty($value['event_cost_type'])){
                    foreach($value['event_cost_type'] as $eventCostType){
                        if($eventCostType['post_type'] == 1){
                            $eventCostValue .= __('languages.member.Money').' : '.$eventCostType['post_value'].' | ';
                        }
                        if($eventCostType['post_type'] == 2){
                            $eventCostValue .= __('languages.member.Tokens').' : '.$eventCostType['post_value'].' | ';
                        }
                        if($eventCostType['post_type'] == 3){
                            $explodeEventCostType = explode("+",$eventCostType['post_value']);
                            $eventCostValue .= __('languages.member.Money').' : '.$explodeEventCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeEventCostType[1].' | ';
                        }
                    }
                }
                $rows[$eventKey]['cost_method'] = ($eventCostValue) ? $eventCostValue : '';
            }

            // echo '<pre>';print_r($rows);die;

            $defaultColumnName = array(
                'event_code' => __('languages.event.Event_code'),
                'event_name' => __('languages.event.Event Name'),
                'cost_method' => __('languages.cost_method'),
                'date' => __('languages.event.Date'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AssignUserReport.csv';
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

    public function assignedEventsMemberList(Request $request){
        if(isset($request->recordIds) && !empty($request->recordIds)){
            $EventList = EventAssignModel::where('event_id', $request->mainEventId)->whereIn('id',$request->recordIds)->get()->toArray();
        }else{
            $EventList = EventAssignModel::where('event_id', $request->mainEventId)->get()->toArray();
        }
        
        if (!empty($EventList)) {
            $user = array_column($EventList, "user_id");
            if(isset($request->recordIds) && !empty($request->recordIds)){
                $memberList = EventAssignModel::with('users')->whereIn('id',$request->recordIds)->where('event_id', $request->mainEventId)->orderBy('id','DESC')->get()->toArray();
            }else{
                $memberList = EventAssignModel::with('users')->where('event_id', $request->mainEventId)->orderBy('id','DESC')->get()->toArray();
            }
            if(!empty($memberList)){
                foreach ($memberList as $memberKey => $member) {
                    $rows[$memberKey]['member_number'] = ($member['users']['MemberCode']) ? 'C'.$member['users']['MemberCode'] : '';
                    // $rows[$memberKey]['member_name'] = ($member['users']['Chinese_name']) ? $member['users']['Chinese_name'] : '';
                    $rows[$memberKey]['english_name'] = ($member['users']['English_name']) ? $member['users']['English_name'] : '';
                    $rows[$memberKey]['chinese_name'] = ($member['users']['Chinese_name']) ? $member['users']['Chinese_name'] : '';
                    $rows[$memberKey]['remarks'] = ($member['remark']) ? $member['remark'] : '';
                    $rows[$memberKey]['status'] = ($member['status'] == 1) ? 'Confirm' : 'Not-Confirm';
                    $eventCostValue = '';
                    $costType = EventAssignModel::where('event_id', $member['event_id'])->where('user_id', $member['user_id'])->get()->toArray();
					foreach ($costType as $key => $costtype) {
						$costValue = EventPosttypeModel::where('event_id', $costtype['event_id'])->where('post_type', $costtype['cost_type'])->get()->first();
						if ($costtype['cost_type'] == 1) {
							$eventCostValue .= '' . __('languages.event.Money') . ' - ' . $costValue->post_value;
						} else if ($costtype['cost_type'] == 2) {
							$eventCostValue .= '' . __('languages.event.Tokens') . ' - ' . $costValue->post_value;
						} else if ($costtype['cost_type'] == 3) {
							$costValue = explode("+", $costValue->post_value);
							$eventCostValue .= '' . __('languages.event.Money') . ' - ' . $costValue[0] . " + " . '' . __('languages.event.Tokens') . ' - ' . $costValue[1];
						}
					}

                    $rows[$memberKey]['cost_method'] = ($eventCostValue) ? $eventCostValue : '';
                }
            }

            $defaultColumnName = array(
                'member_number' => __('languages.export_member.member_number'),
                // 'member_name' => 'Member Name',
                'english_name' => __('languages.export_member.english_name'),
                'chinese_name' => __('languages.export_member.chinese_name'),
                'cost_method' => __('languages.cost_method'),
                'remarks' => __('languages.export_member.remark'),
                'status' => __('languages.export_member.status'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AssignEventsViewMemberReport.csv';
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

    public function roles(Request $request){
        $query = RolePermission::orderBy('id','desc');
        if(!empty($request->roleIds)){
            $query = $query->whereIn('id',$request->roleIds);
        }
        $roles = $query->orderBy('id','DESC')->get()->toArray();
        if (!empty($roles)) {
            $rows = [];
            foreach ($roles as $roleKey => $value) {
                $rows[$roleKey]['role_name'] = ($value['role_name']) ? $value['role_name'] : '';
                $rows[$roleKey]['status'] = ($value['status']==1) ? 'ACTIVE' : 'INACTIVE';
            }
            
            $defaultColumnName = array(
                'role_name' => __('languages.RoleManagement.Role_name'),
                'status' => __('languages.RoleManagement.Status'),
            );

            $exportColumn = $request->columnList;

            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'Roles.csv';
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

    public function Tokens(Request $request){
        $tokenIds = !empty($request->tokenIds) ? $request->tokenIds : '';

        $query = EventTokenManage::with('users')->with('EventSchedule','EventSchedule.events');
        if(!empty($request->tokenIds)){
            $query->whereIn('id',$tokenIds);
        }
        $tokenList = $query->orderBy('id','DESC')->get()->toArray();
        if (!empty($tokenList)) {
            $rows = [];
            foreach ($tokenList as $tokenKey => $value) {
                // $rows[$tokenKey]['user_name'] = !empty($value['users']['English_name']) ? $value['users']['English_name'] : '';
                $rows[$tokenKey]['english_name'] = $value['users']['English_name'] ?? '';
                $rows[$tokenKey]['chinese_name'] = $value['users']['Chinese_name'] ?? '';
                $rows[$tokenKey]['event_name'] = !empty($value['event_schedule']['events']['event_name']) ? $value['event_schedule']['events']['event_name'] : '-';
                $rows[$tokenKey]['generate_token'] = !empty($value['generate_token']) ? $value['generate_token'] : '-';
                $rows[$tokenKey]['used_token'] = !empty($value['used_token']) ? $value['used_token'] : '-';
                $rows[$tokenKey]['remaining_token'] = !empty($value['remaining_token']) ? $value['remaining_token'] : '-';
                $rows[$tokenKey]['expire_date'] = !empty($value['expire_date']) ? Helper::dateConvertDDMMYYY('/','/',$value['expire_date']) : '-';
                $rows[$tokenKey]['status'] = !empty($value['status'] && $value['status']=="active") ? 'Active' : 'Inactive';
            }
            
            
            $defaultColumnName = array(
                // 'user_name' => 'User Name',
                'english_name'  => __('languages.member.English_name'),
                'chinese_name'  =>  __('languages.member.Chinese_name'),
                'event_name' => __('languages.Event name'),
                'generate_token' => __('languages.generate_token'),
                'used_token'    => __('languages.used_token'),
                'remaining_token' => __('languages.Token Management.Remaining Token'),
                'expire_date'   =>  __('languages.Token Management.Expire Date'),
                'status'    => __('languages.Status'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'TokenManagement.csv';
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

    public function Product(Request $request){
        $productIds = !empty($request->productIds) ? $request->productIds : '';
        $query = ProductModel::with('productCostType');
        if(!empty($productIds)){
            $query->whereIn('id',$productIds);
        }
        $productList = $query->orderBy('id','DESC')->get()->toArray();
        if (!empty($productList)) {
            $rows = [];
            foreach ($productList as $productKey => $value) {
                $rows[$productKey]['product_image'] = !empty($value['product_image']) ? $value['product_image'] : '';
                $rows[$productKey]['product_name'] = !empty($value['product_name']) ? $value['product_name'] : '';
                $rows[$productKey]['product_sku'] = !empty($value['product_sku']) ? $value['product_sku'] : '';
                $productCost = '';
                if(isset($value['product_cost_type']) && !empty($value['product_cost_type'])){
                    foreach($value['product_cost_type'] as $productCostType){
                        if($productCostType['cost_type'] == 1){
                            $productCost .= __('languages.member.Money').' : '.$productCostType['cost_value'].' | ';
                        }
                        if($productCostType['cost_type'] == 2){
                            $productCost .= __('languages.member.Tokens').' : '.$productCostType['cost_value'].' | ';;
                        }
                        if($productCostType['cost_type'] == 3){
                            $explodeProductCostType = explode("+",$productCostType['cost_value']);
                            $productCost .= __('languages.member.Money').' : '.$explodeProductCostType[0].' + '.__('languages.member.Tokens').' : '.$explodeProductCostType[1].' | ';
                        }
                    }
                }
                $rows[$productKey]['product_cost_method'] = $productCost;
                $rows[$productKey]['amount'] = !empty($value['amount']) ? $value['amount'] : '';
                $rows[$productKey]['product_date'] = !empty($value['date']) ? Helper::dateConvertDDMMYYY('-','/',$value['date']) : '';
                $rows[$productKey]['status'] = !empty($value['status'] && $value['status']=="1") ? 'Active' : 'Inactive';
            }
            
            $defaultColumnName = array(
                'product_image' => __('languages.Product.product_image'),
                'product_name' => __('languages.Product.Product_name'),
                'product_sku' => __('languages.Product.Product_name'),
                'product_cost_method' => __('languages.Product.Product_name'),
                'amount'    => __('languages.Product.Amount'),
                'product_date' => __('languages.Product.product_date'),
                'status'    => __('languages.Status'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'Product.csv';
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

    public function EnrollmentProduct(Request $request){
        $EnrollmentOrderIds = !empty($request->EnrollmentOrderIds) ? $request->EnrollmentOrderIds : '';
        $Query = AssignProductOrder::with('ProductCostType')
								->with('product','product.childProducts')
								->with('ProductAssignMembers','ProductAssignMembers.users');
        if(isset($EnrollmentOrderIds) && !empty($EnrollmentOrderIds)){
            $Query->whereIn('id',$EnrollmentOrderIds);
        }
        $EnrollmentOrder = $Query->orderBy('id','desc')->get();
        if (!empty($EnrollmentOrder)) {
            $rows = [];
            foreach ($EnrollmentOrder as $orderKey => $value) {
                $rows[$orderKey]['order_id'] = !empty($value['order_id']) ? $value['order_id'] : '';
                $rows[$orderKey]['product_code'] = !empty($value['product']['product_sku']) ? $value['product']['product_sku'] : '';
                $rows[$orderKey]['product_name'] = !empty($value['product']['product_name']) ? $value['product']['product_name'] : '';
                if(!empty($value['product']['childProducts'])){
                    foreach($value['product']['childProducts'] as $childorderKey => $childProduct){
                        $rows[$orderKey]['option_code_and_option_name'] = !empty($childProduct['product_suffix'] && $childProduct['product_suffix_name']) ? $childProduct['product_suffix']. '+' .$childProduct['product_suffix_name'] : '';
                    }
                }else{
                    $rows[$orderKey]['option_code_and_option_name'] = '';
                } 
                $costType = ''; 
                if(!empty($value['ProductCostType'])){                 
                    if($value['ProductCostType']['cost_type']==1){
                        $costType .= !empty($value['ProductCostType']['cost_type']) ? __('languages.member.Money').' : '.$value['ProductCostType']['cost_value'] : '';
                    }
                    if($value['ProductCostType']['cost_type']==2){
                        $costType .= !empty($value['ProductCostType']['cost_type']) ? __('languages.member.Tokens').' : '.$value['ProductCostType']['cost_value'] : '';
                    }
                    if($value['ProductCostType']['cost_type'] == 3){
                        $explodeProductCostType = explode("+",$value['ProductCostType']['cost_value']);
                        $costType .= !empty($value['ProductCostType']['cost_type']) ? __('languages.member.Money').' : '.$explodeProductCostType[0].' + '.__('languages.member.Tokens'). ':' .$explodeProductCostType[1] : '';
                    }
                }else{
                    $rows[$orderKey]['cost_method'] = '';
                } 
                
                $rows[$orderKey]['cost_method'] = $costType;
                $rows[$orderKey]['no_of_member'] = count($value['ProductAssignMembers']) ?? 0;
                $rows[$orderKey]['order_date'] = !empty($value['date']) ? Helper::dateConvertDDMMYYY('-','/',$value['order_date']) : '';
            }
            
            $defaultColumnName = array(
                'order_id' => __('languages.order_id'),
                'product_code' => __('languages.Product.product_code'),
                'product_name' => __('languages.Product.Product_name'),
                'option_code_and_option_name' => __('languages.option_code_option_name'),
                'cost_method'    => __('languages.cost_method'),
                'no_of_member'   => __('languages.no_of_members'),
                'order_date' => __('languages.order_date'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'EnrollmentProduct.csv';
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

    public function AttendanceTransaction(Request $request){
        $transactionIds  = !empty($request->transactionIds) ? $request->transactionIds : '';
        $query = MemberUsedToken::with('users')->with('event');
        if(!empty($transactionIds)){
            $query->whereIn('id',$transactionIds);
        }
        $TransactionList = $query->orderBy('id','desc')->get()->toArray();
        if (!empty($TransactionList)) {
            $rows = [];
            foreach ($TransactionList as $transactionKey => $value) {
                // $rows[$transactionKey]['member_name'] = ($value['users']['English_name']) ? $value['users']['English_name'] : '';
                $rows[$transactionKey]['english_name'] = (isset($value['users']['English_name']) && !empty($value['users']['English_name'])) ? $value['users']['English_name'] : '';
                $rows[$transactionKey]['chinese_name'] = (isset($value['users']['Chinese_name']) && !empty($value['users']['Chinese_name'])) ? $value['users']['Chinese_name'] : '';
                $rows[$transactionKey]['event_name'] = (isset($value['event']['event_name']) && !empty($value['event']['event_name'])) ? $value['event']['event_name'] : '';
                $rows[$transactionKey]['tokens'] = ($value['token']) ? $value['token'] : '-';
                $rows[$transactionKey]['money'] =($value['money']) ? $value['money'] : '-';
                $rows[$transactionKey]['date'] = ($value['created_at']!='') ? Helper::dateConvertDDMMYYY('-','/',$value['created_at']) : '';
            }
            
            $defaultColumnName = array(
                // 'member_name' => 'Member Name',
                'english_name'  => __('languages.member.English_name'),
                'chinese_name' =>  __('languages.member.Chinese_name'),
                'event_name' =>  __('languages.Event name'),
                'tokens'    =>  __('languages.member.Tokens'),
                'money'     =>  __('languages.member.Money'),
                'date'      =>  __('languages.event.Date')
            );

            $exportColumn = $request->columnList;

            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AttendanceTranscation.csv';
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

    public function AttendanceEventMember(Request $request){
        $attendaceMemberIds = !empty($request->attendaceMemberIds) ? $request->attendaceMemberIds : '';
        $member_name = !empty($request->member_name) ? $request->member_name : '';
		$filter_date = !empty($request->filter_date) ? $request->filter_date : '';
		$event_name = !empty($request->event_name) ? $request->event_name : '';
		$event_type = !empty($request->event_type) ? $request->event_type : '';
        $query = Attendance::with('users')->with('event')->with('eventType');
        if(!empty($attendaceMemberIds)){
            $query->whereIn('id',$attendaceMemberIds);
        }
    
		//$EventType = 'event_type_name_' . app()->getLocale();
		if (isset($member_name) && !empty($member_name)) {
			$query->where('user_id', $member_name);
		}
		if (isset($filter_date) && !empty($filter_date)) {
			$explode_event_date = array_map('trim',explode('-', $filter_date));
			$start_date = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explode_event_date[0])));
			$end_date = date('Y-m-d', strtotime(Helper::DateConvert('/','-',$explode_event_date[1])));
			$query->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date);
		}
		if (isset($event_name) && !empty($event_name)) {
			$query->where('event_id', $event_name);
		}
		if (isset($event_type) && !empty($event_type)) {
			$query->where('event_type', $event_type);
        }
		$attendanceMemberList = $query->orderBY('id','DESC')->get()->toArray();

        if(!empty($attendanceMemberList)){
            $rows = [];
            foreach ($attendanceMemberList as $attendanceMemberKey => $value) {
                if(!empty($value['users']) && !empty($value['event'])){
                    $rows[$attendanceMemberKey]['event_name'] = (!empty($value['event']) && ($value['event']['event_name'])) ? $value['event']['event_name'] : '';
                    $rows[$attendanceMemberKey]['event_type'] = ($value['event_type']['event_type_name_en']) ? $value['event_type']['event_type_name_en'] : '';
                    $rows[$attendanceMemberKey]['event_date'] = (!empty($value['event']) && $value['event']['startdate']) ? $value['event']['startdate'] : '';
                    // $rows[$attendanceMemberKey]['member_name'] = !empty($value['users']['UserName']) ? $value['users']['UserName'] :  $value['users']['Chinese_name'].' & '.$value['users']['English_name'] ;
                    $rows[$attendanceMemberKey]['english_name'] = !empty($value['users']['English_name']) ? $value['users']['English_name'] : '---';
                    $rows[$attendanceMemberKey]['chinese_name'] = !empty($value['users']['Chinese_name']) ? $value['users']['Chinese_name'] : '---';
                    if($value['training_hour'] != '00:00' && $value['training_hour'] != '0:00'){
                        $rows[$attendanceMemberKey]['training_hour'] = ($value['training_hour']) ? $value['training_hour'] : '---';
                    }else{
                        $rows[$attendanceMemberKey]['training_hour'] = '';
                    }
                    if($value['activity_hour'] != '00:00' && $value['activity_hour'] != '0:00'){
                        $rows[$attendanceMemberKey]['activity_hour'] = ($value['activity_hour']) ? $value['activity_hour'] : '---';
                    }else{
                        $rows[$attendanceMemberKey]['activity_hour'] = '';
                    }
                    if($value['service_hour'] != '00:00' && $value['service_hour'] != '0:00'){
                        $rows[$attendanceMemberKey]['service_hour'] = ($value['service_hour']) ? $value['service_hour'] : '---';
                    }else{
                        $rows[$attendanceMemberKey]['service_hour'] = '';
                    }
                    // $rows[$attendanceMemberKey]['used_hour'] = ($value['hours']) ? $value['hours'] : '';
                    // $rows[$attendanceMemberKey]['remaining_hour'] = ($value['remaining_hour']) ? $value['remaining_hour'] : '';
                    // $rows[$attendanceMemberKey]['total_hour'] = ($value['users']['hour_point']) ? $value['users']['hour_point'] : '';
                }
            }
            $defaultColumnName = array(
                'event_name' => __('languages.Attendance.Event_Name'),
                'event_type' => __('languages.Attendance.Event_Type'),
                'event_date'    => __('languages.Attendance.Event Date'),
                // 'member_name'     => 'Memeber Name',
                'english_name' => __('languages.UserManagement.english_name'),
                'chinese_name' => __('languages.UserManagement.chinese_name'),
                'training_hour' => __('languages.training_hours'),
                'activity_hour' => __('languages.activity_hours'),
                'service_hour' => __('languages.service_hours'),
                // 'used_hour'      => 'Used Hour',
                // 'remaining_hour' => 'Remaining Hour',
                // 'total_hour'    => 'Total Hour',
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'AttendanceTranscation.csv';
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

    public function productHistory(Request $request){
        $productHistoryIds = !empty($request->productTransactionIds) ? $request->productTransactionIds : '';
        $query = ProductAssignModel::with('childProducts')->with('users')->with('product');
        if(!empty($productHistoryIds)){
            $query->whereIn('id',$productHistoryIds);
        }
        $productHistoryList = $query->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
        if(!empty($productHistoryList)){
            $rows = [];
            foreach ($productHistoryList as $productHistoryKey => $value) {
                $rows[$productHistoryKey]['member_number'] = ($value['users']['MemberCode']) ? 'C'.$value['users']['MemberCode'] : '';
                // $rows[$productHistoryKey]['member_name'] = ($value['users']['English_name']) ? $value['users']['English_name'] : '';
                $rows[$productHistoryKey]['english_name'] = $value['users']['English_name'] ?? '';
                $rows[$productHistoryKey]['chinese_name'] = $value['users']['Chinese_name'] ?? '';
                $rows[$productHistoryKey]['product_name'] = ($value['product']['product_name']) ? $value['product']['product_name'] : '';
                $rows[$productHistoryKey]['product_suffix_code_and_name'] = !empty($value['child_products']) ? $value['child_products']['product_suffix'].' + '.$value['child_products']['product_suffix_name'] :  '' ;
                $rows[$productHistoryKey]['date'] = ($value['created_at']) ? Helper::dateConvertDDMMYYY('-','/',$value['created_at']) : '';
                $rows[$productHistoryKey]['remarks'] = ($value['remark']) ? $value['remark'] : '';
                $rows[$productHistoryKey]['tokens'] = ($value['token']) ? $value['token'] : '';
                $rows[$productHistoryKey]['money'] = ($value['money']) ? $value['money'] : '';
            }
            $defaultColumnName = array(
                'member_number' => __('languages.export_member.member_number'),
                // 'member_name' => 'Member Name',
                'english_name'  => __('languages.export_member.english_name'),
                'chinese_name'  => __('languages.export_member.chinese_name'),
                'product_name' => __('languages.Product.Product_name'),
                'product_suffix_code_and_name' => __('languages.product_suffix_code_and_name'),
                'date' => __('languages.event.Date'),
                'remarks' => __('languages.Remarks.Remarks'),
                'tokens' => __('languages.export_member.Tokens'),
                'money' => __('languages.member.Money')
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'ProductHistory.csv';
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

    public function badgeAssignMember(Request $request){
        $query = BadgeAssign::with('user')->with('badge');

        // Filter by issue date
        if(isset($request->badges_issue_date) && !empty($request->badges_issue_date)){
            $explodeDate = array_map('trim',explode('-',$request->badges_issue_date));
            if(!empty($explodeDate[0]) && !empty($explodeDate[1])){
                $fromDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[0])));
                $toDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[1])));
                $query->whereBetween('issue_date', [$fromDate, $toDate]);
            }
        }

        // Search by award categories
        if(isset($request->badges_categories) && !empty($request->badges_categories)){
            $query->where('badge_id',$request->badges_categories);
        }
        // Search by reference number
        if(isset($request->search_text) && !empty($request->search_text)){
            $query->where('reference_number','like','%'.$request->search_text.'%');
        }
        // Filter by member
        if(isset($request->member_id) && !empty($request->member_id) && $request->member_id != 'all'){
            $query->where('user_id',$request->member_id);
        }
        $badgeMemberList = $query->orderBy('id','desc')->get()->toArray();
        if(!empty($badgeMemberList)){
            $rows = [];
            foreach ($badgeMemberList as $badgeMemberKey => $value) {
                $rows[$badgeMemberKey]['badge_name'] = ($value['badge']['name_en']) ? $value['badge']['name_en'] : '';
                // $rows[$badgeMemberKey]['member_name'] = ($value['user']['English_name']) ? $value['user']['English_name'] : '';
                $rows[$badgeMemberKey]['english_name'] = $value['user']['English_name'] ?? '';
                $rows[$badgeMemberKey]['chinese_name'] = $value['user']['Chinese_name'] ?? '';
                $rows[$badgeMemberKey]['reference_name'] = ($value['reference_number']) ? $value['reference_number'] : '';
                $rows[$badgeMemberKey]['issue_date'] = ($value['issue_date']) ? Helper::dateConvertDDMMYYY('-','/',$value['issue_date']) :  '' ;
                $rows[$badgeMemberKey]['assigned_date'] = ($value['assigned_date']) ? Helper::dateConvertDDMMYYY('-','/',$value['assigned_date']) : '';
                $rows[$badgeMemberKey]['status'] = ($value['status']=="active") ? 'Active' : 'Inactive';
            }
            $defaultColumnName = array(
                'badge_name' => __('languages.awards.badge_name'),
                // 'member_name' => 'Member Name',
                'english_name'  => __('languages.member.English_name'),
                'chinese_name'  => __('languages.member.Chinese_name'),
                'reference_name' => __('languages.reference_name'),
                'issue_date' => __('languages.issue_date'),
                'assigned_date' => __('languages.assigned_date'),
                'status' => __('languages.Status'),
            );

            $exportColumn = $request->columnList;
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'BadgeAssignMember.csv';
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

    /**
     * USE : Export member QR-Code Url
     */
    public function exportMemberQrCodeUrls(Request $request){
        $Query = User::select('ID','MemberCode','email','Chinese_name','English_name','team','elite_team');
        if(isset($request->userIds) && !empty($request->userIds)){
            $Query->whereIn('ID',$request->userIds);
        }
        $UserData = $Query->orderBy('ID','DESC')->get();
        if(!$UserData->isEmpty()){
            $rows = [];
            // File Path
            $count = 1;
            
            foreach($UserData as $memberKey => $member) {
                $rows[$memberKey]['sr_no'] = $count++;
                $rows[$memberKey]['ChineseName'] = ($member['Chinese_name']) ? $member['Chinese_name'] : $member['English_name'];
                $rows[$memberKey]['Team'] = ($member['team']) ? EilteModel::where('id',$member['team'])->pluck('elite_ch')->first() : '';
                $rows[$memberKey]['SubTeam'] = ($member['elite_team']) ? Subteam::where('id',$member['elite_team'])->pluck('subteam_ch')->first() : '';
                $rows[$memberKey]['MemberCode'] = ($member['MemberCode']) ? $member['MemberCode'] : '';

                $Email = $member->email;
                if (!empty($member->UserName)) {
                    $UserName = $member->UserName;
                } else {
                    $Chinese_name = $member->Chinese_name;
                    $English_name = $member->English_name;
                    $UserName = $Chinese_name . ' ' . $English_name;
                }
                $user_id = base64_encode($member->ID);
                $email_add = base64_encode($Email);
                $QRString = $user_id . "/" . $email_add . "/" . trim($UserName);
                $rows[$memberKey]['QrCode'] = $QRString ?? '';
            }
            $defaultColumnName = array(
                'sr_no' => '#SR.NO',
                'MemberCode' => __('languages.Attendance.Member_Code'),
                'ChineseName' => __('languages.member.Chinese_name'),
                'Team' => __('languages.member.team'),
                'SubTeam' => __('languages.member.SubTeam'),
                'QrCode' => __('languages.member.qr_code')
            );
            $exportColumn = ['sr_no','MemberCode','ChineseName','Team','SubTeam','QrCode'];
            foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
            }

            $i = 0;
            $final_array = [];

            foreach ($rows as $key => $value) {
                $newArr = [];
                foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
                    $newArr[] = $value[$exportColumnValue];
                }
                $final_array[$i] = $newArr;
                $i++;
            }

            $fileName = 'MemberQrCode.csv';
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
    
    // /**
    //  * USE : Export member QR-Code Url
    //  */
    // public function exportMemberQrCodeUrls(Request $request){
    //     $Query = User::select('ID','MemberCode','QrCode');
    //     if(isset($request->userIds) && !empty($request->userIds)){
    //         $Query->whereIn('ID',$request->userIds);
    //     }
    //     $UserData = $Query->orderBy('ID','DESC')->get();
    //     if(!$UserData->isEmpty()){
    //         $rows = [];
    //         // File Path
    //         $count = 1;
    //         foreach($UserData as $memberKey => $value) {
    //             $rows[$memberKey]['sr_no'] = $count++;
    //             if(!empty($value['QrCode'])){
    //                 $rows[$memberKey]['MemberCode'] = ($value['MemberCode']) ? $value['MemberCode'] : '';
    //                 $rows[$memberKey]['QrCode'] = asset($value['QrCode']);
    //             }else{
    //                 $this->AttendanceController->generateQRCode($value['ID']);
    //                 $userDetail = User::find($value['ID']);
    //                 $rows[$memberKey]['MemberCode'] = ($userDetail->MemberCode) ? $userDetail->MemberCode : '';
    //                 $rows[$memberKey]['QrCode'] = asset($userDetail->QrCode);
    //             }
    //         }
    //         $defaultColumnName = array(
    //             'sr_no' => '#SR.NO',
    //             'MemberCode' => 'Member Code',
    //             'QrCode' => 'QrCode',
    //         );

    //         $exportColumn = ['sr_no','MemberCode','QrCode'];

    //         foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
    //             $export_col_name[$exportColumnValue] = $defaultColumnName[$exportColumnValue];
    //         }

    //         $i = 0;
    //         $final_array = [];

    //         foreach ($rows as $key => $value) {
    //             $newArr = [];
    //             foreach ($exportColumn as $exportColumnKey => $exportColumnValue) {
    //                 $newArr[] = $value[$exportColumnValue];
    //             }
    //             $final_array[$i] = $newArr;
    //             $i++;
    //         }

    //         $fileName = 'MemberQrCode.csv';
    //         $headers = array(
    //             "Content-type" => "text/csv",
    //             "Content-Disposition" => "attachment; filename=$fileName",
    //             "Pragma" => "no-cache",
    //             "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
    //             "Expires" => "0",
    //         );
    //         $columns = $export_col_name;
    //         $callback = function () use ($final_array, $columns) {
    //             $file = fopen('php://output', 'w');
    //             fputcsv($file, $columns);
    //             foreach ($final_array as $task) {
    //                 fputcsv($file, $task);
    //             }
    //             fclose($file);
    //         };
    //         return response()->stream($callback, 200, $headers);
    //     }
    // }
    public function exportHistory(Request $request) {
        $location = 'uploads/export_files';
        $currentDate = Carbon::now()->format('Y-m-d');
        $userName = Session::get('user')['username'];
        $name = $request->table . '_' . $userName . '_' . $currentDate . '.csv';
        file_put_contents(public_path($location . '/' . $name . '.csv'), $request->data);
        $postData = [
            'user' => $userName,
            'filename' => $name,
            'link' => $request->url,
        ];
        Helper::exportAuditLogfuncation($postData, $request->table, 'Export');
    }
}
