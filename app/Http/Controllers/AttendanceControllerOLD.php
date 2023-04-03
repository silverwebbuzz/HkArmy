<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Http\Models\Attendance;
use App\Http\Models\User;
use App\Http\Models\Events;
use App\Http\Models\HourAttendance;
use App\Http\Models\ServiceHourPackage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use App\Http\Models\EventType;
use App\Http\Models\EventSchedule;
//use Mail;
use Carbon\Carbon;
use Config;
use Session;
use DB;
use App\Jobs\SendQRMailJob;
use App\Http\Models\MemberToken;
use App\Http\Models\MemberUsedToken;
use App\Http\Models\MemberTokenStatus;
use App\Http\Models\Settings;
use DateTime;

class AttendanceController extends Controller
{

    public function __construct()
    {
        $this->Attendance = new Attendance;
        date_default_timezone_set(Config::get('constants.timeZone'));
        $sitesettings = Helper::getsitesettings();
        if (!empty($sitesettings->min_hour))
        {
            $this->globalmin = $sitesettings->min_hour;
        }
        else
        {
            $this->globalmin = 30;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Attendance = '';
        if (!empty($request->event_id))
        {
            $id = Helper::decodekey($request->event_id);
            $get_event_code = Events::where('id', $id)->first();
            $event_code = $get_event_code->event_code;
            if (Session::get('user') ['role_id'] != '1')
            {
                if (!empty($event_code))
                {
                    $events = EventSchedule::with('events')->whereHas('events', function ($query)
                    {
                        $query->whereRaw('FIND_IN_SET(' . Session::get('user') ['user_id'] . ',event_assign_user)');
                    })
                        ->where('event_code', $event_code)->where('status', '1')
                        ->groupBy('occurs')
                        ->get()
                        ->toArray();
                    //$events = EventSchedule::with('events')->whereHas('events', function ($query) {$query->whereRaw('FIND_IN_SET('.Session::get('user')['user_id'].',event_assign_user)');})->where('event_code',$event_code)->where('date',date('m/d/Y'))->where('status','1')->get()->toArray();
                    $Attendance = Attendance::where('user_id', Session::get('user') ['user_id'])->where('event_id', $id)->with('users')
                        ->with('event')
                        ->with('eventType')
                        ->get()
                        ->toArray();
                }
            }
            else
            {
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
        }
        else
        {
            if (Session::get('user') ['role_id'] != '1')
            {
                $events = EventSchedule::with('events')->whereHas('events', function ($query)
                {
                    $query->whereRaw('FIND_IN_SET(' . Session::get('user') ['user_id'] . ',event_assign_user)');
                })
                    ->where('date', date('m/d/Y'))
                    ->where('status', '1')
                    ->get()
                    ->toArray();
                $Attendance = Attendance::where('user_id', Session::get('user') ['user_id'])->with('users')
                    ->with('event')
                    ->with('eventType')
                    ->get()
                    ->toArray();
            }
            else
            {
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
        return view('AttendanceManagement.list_attendance', compact('events', 'Attendance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
    public function store(Request $request)
    {
        $user = User::selectRaw('ID,hour_point')->where('ID', $request->members)
            ->first()
            ->toArray();
        if (!empty($user))
        {
            $totalhour = $user['hour_point'];
        }
        $hourAttendance = HourAttendance::where('user_id', $request->members)
            ->orderBy('id', 'desc')
            ->first();
        if (!empty($hourAttendance))
        {
            if ($hourAttendance->use_hour != $totalhour)
            {
                $id = $hourAttendance->id;
                $hourattend = HourAttendance::find($id);
                $hourattend->user_id = $request->members;
                $hourattend->event_id = $request->eventName;
                if ($hourAttendance->remaining_hour == 0 && $hourattend->Total_hour == 0)
                {
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

                    if ($result)
                    {
                        return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
                    }
                    else
                    {
                        return back()
                            ->with('error_msg', 'Something went wrong.');
                    }
                }
                else
                {
                    if ($hourAttendance->remaining_hour >= $request->hours)
                    {
                        $use_hour = $hourAttendance->use_hour + $request->hours;
                        $remaining_hour = $hourAttendance->remaining_hour - $request->hours;
                        $hourattend->use_hour = $use_hour;
                        $hourattend->remaining_hour = $remaining_hour;
                        if ($remaining_hour != '0')
                        {
                            $hourattend->Total_hour = $totalhour;
                        }
                        else
                        {
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

                        if ($result)
                        {
                            return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
                        }
                        else
                        {
                            return back()
                                ->with('error_msg', 'Something went wrong.');
                        }
                    }
                    else
                    {
                        return redirect('attendanceManagement/create')
                            ->with('error_msg', 'Please update your hour point.');
                    }
                }
            }
            else
            {
                return redirect('attendanceManagement/create')
                    ->with('error_msg', 'Please update your hour point.');
            }
        }
        else
        {
            if (!empty($totalhour))
            {
                if ($totalhour >= $request->hours)
                {
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

                    if ($result)
                    {
                        return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
                    }
                    else
                    {
                        return back()
                            ->with('error_msg', 'Something went wrong.');
                    }
                }
                else
                {
                    return redirect('attendanceManagement/create')
                        ->with('error_msg', 'Please update your hour point.');
                }
            }
            else
            {
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
        $members = User::selectRaw('id,UserName,MemberCode,Chinese_name,English_name')->where('Role_ID', '!=', '1')
            ->where('Status', '1')
            ->get()
            ->toArray();
        $events = Events::selectRaw('id,event_name,event_type,startdate,start_time')->where('status', '1')
            ->get()
            ->toArray();
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
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        $attendance->user_id = $request->members;
        $attendance->member_code = $request->memberCode;
        $attendance->event_id = $request->eventName;
        $attendance->event_type = $request->eventType;
        $attendance->in_time = $request->inTime;
        $attendance->out_time = $request->outTime;
        $attendance->hours = $request->hours;
        if(!empty($request->date)){
            $startdate = !empty($request->date) ? DateTime::createFromFormat('d F, Y', $request->date) : '';
            $attendance->date = !empty($request->date) ? $startdate->format('l,d F,Y') : NULL;
        }
        $result = $attendance->save();
        if ($result)
        {
            return redirect('attendanceManagement')->with('success_msg', 'Attendance add successfully.');
        }
        else
        {
            return back()
                ->with('error_msg', 'Something went wrong.');
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
        //
        
    }

    /**
     ** USE : Add Attender Login Time
     *
     */
    public function recordAttendance(Request $request)
    {
        $userid = base64_decode($request->user_id);
        $userData = User::where('Role_ID', '2')->where('ID', $userid)->first();
        if (!empty($userData))
        {
            $hour_point = $userData->hour_point;
        }
        $redirect = '/attendanceManagement';
        if (!empty($userData))
        {
            if (!empty($hour_point))
            {
                $eventData = Events::where('id', $request->event_id)
                    ->first();
                $event_sche = EventSchedule::where('event_id', $request->event_id)
                    ->get()
                    ->toArray();
                $date_arr = array();
                if (!empty($event_sche))
                {
                    foreach ($event_sche as $row)
                    {
                        $date_arr[] = $row['date'];
                    }
                }
                if (!empty($eventData))
                {
                    //Login
                    $eventStarttime = $eventData->start_time;
                    $diff_event = abs(strtotime($eventStarttime) - strtotime(date('H:i')));
                    $tmins_event = $diff_event / 60;
                    $hours_event = floor($tmins_event / 60);
                    $mins_event = $tmins_event % 60;
                    $eventendtime = $eventData->end_time;
                    if ($request->type == 1)
                    {
                        if (in_array(date('m/d/Y') , $date_arr))
                        {
                            $Alreadylogin = Attendance::where('event_id', $request->event_id)
                                ->where('user_id', $userid)->get()
                                ->toArray();
                            if (empty($Alreadylogin))
                            {
                                // $eventlogin = Attendance::where('user_id',$userid)->orderBy('id','desc')->first();
                                // if(!empty($eventlogin) && $eventlogin->out_time != '-'){
                                $inTime = date('H:i');
                                if ($eventendtime > $inTime)
                                {
                                    if ($hours_event < 1)
                                    {

                                        //check event is paid or not
                                        if ($eventData['event_money'] == '0')
                                        {
                                            //free
                                            $insertData = true;
                                        }
                                        else
                                        {
                                            //paid
                                            if ($request->usedCoin == '0')
                                            {

                                                //not using coin
                                                $message = 'Please use coin for paid event.';
                                                $insertData = false;
                                            }
                                            else
                                            {
                                                $MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
                                                if (!empty($MemberTokenStatus))
                                                {
                                                    //using coin
                                                    if ($eventData['event_token'] <= $MemberTokenStatus->total_token)
                                                    {

                                                        //$UserToken = User::where('Role_ID', '2')->where('MemberCode', $userid)->first();
                                                        $UserToken = User::where('Role_ID', '2')->where('ID', $userid)->first();
                                                        if (!empty($UserToken))
                                                        {

                                                            //start logic for token used or not for expired token
                                                            $memberToken = MemberToken::where('user_id', $userid)->where('remaining_token', '!=', 0)
                                                                ->where('status', 0)
                                                                ->where('expired', 0)
                                                                ->get()
                                                                ->toArray();
                                                            if (!empty($memberToken))
                                                            {
                                                                $remainingHour = $eventData['event_token'];
                                                                foreach ($memberToken as $memberToken_key => $memberToken_value)
                                                                {
                                                                    if ($remainingHour == '0')
                                                                    {
                                                                        break;
                                                                    }
                                                                    else
                                                                    {
                                                                        if ($remainingHour < $memberToken_value['remaining_token'])
                                                                        {
                                                                            $token = MemberToken::where('id', $memberToken_value['id'])->first();
                                                                            // echo $remainingHour;
                                                                            // $remainingHour =  $token->remaining_token - $remainingHour;
                                                                            $token->remaining_token = $token->remaining_token - $remainingHour;
                                                                            $token->status = ($token->remaining_token - $remainingHour == '0') ? '1' : '0';
                                                                            $saveToken = $token->save();
                                                                            break;
                                                                        }
                                                                        else
                                                                        {
                                                                            $token = MemberToken::where('id', $memberToken_value['id'])->first();
                                                                            $remainingHour = $remainingHour - $token->remaining_token;
                                                                            $token->remaining_token = 0;
                                                                            $token->status = 1;
                                                                            $saveToken = $token->save();
                                                                        }
                                                                    }

                                                                }
                                                            }
                                                            else
                                                            {

                                                            }
                                                            //end logic for token used or not for expired token
                                                            $MemberTokenStatus->total_token = ($MemberTokenStatus->total_token - $eventData['event_token']);
                                                            $MemberTokenStatus->save();

                                                            //Save member used token detail
                                                            $MemberUsedToken = new MemberUsedToken;
                                                            $MemberUsedToken->user_id = !empty($userid) ? $userid : NULL;
                                                            $MemberUsedToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
                                                            $MemberUsedToken->token = !empty($eventData['event_token']) ? $eventData['event_token'] : NULL;
                                                            $saveMemberUsedToken = $MemberUsedToken->save();

                                                            $insertData = true;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $message = 'User have not enough coin to attend this event.';
                                                        $insertData = false;
                                                    }
                                                }
                                                else
                                                {
                                                    $message = 'User have no coin to attend this event.';
                                                    $insertData = false;
                                                }
                                            }
                                        }
                                        if ($insertData)
                                        {
                                            $diff = abs(strtotime($inTime) - strtotime($eventStarttime));
                                            $tmins = $diff / 60;
                                            $hours = floor($tmins / 60);
                                            $mins = $tmins % 60;
                                            $userattendfirst = Attendance::where('user_id', $userid)->first();
                                            $attendance = new Attendance;
                                            $attendance->user_id = $userid;
                                            $attendance->member_code = $userData->MemberCode;
                                            $attendance->event_id = $request->event_id;
                                            $attendance->event_type = $eventData->event_type;
                                            if ($hours == 0 && $mins < $this->globalmin)
                                            {
                                                $attendance->in_time = $eventStarttime;
                                            }
                                            else
                                            {
                                                $attendance->in_time = $inTime;
                                            }
                                            $attendance->date = $eventData->startdate;
                                            if (empty($userattendfirst))
                                            {
                                                $attendance->remaining_hour = $hour_point . ':00';
                                            }
                                            $result = $attendance->save();
                                            if ($result)
                                            {

                                                // Add Member tokens 'Per Hours 1 Token Incresed'
                                                $UserToken = User::where('Role_ID', '2')->where('ID', $userid)->first();
                                                if (!empty($UserToken))
                                                {
                                                    $UserToken->total_tokens = ($UserToken->total_tokens + $hours);
                                                    $UserToken->save();
                                                }
                                                return response()
                                                    ->json(array(
                                                    'status' => 1,
                                                    'message' => 'Attendance Added successfully',
                                                    'redirecturl' => $redirect
                                                ));
                                            }
                                            else
                                            {
                                                return response()->json(array(
                                                    'status' => 0,
                                                    'message' => 'Something is wrong',
                                                    'redirecturl' => $redirect
                                                ));
                                            }

                                        }
                                        else
                                        {
                                            return response()->json(array(
                                                'status' => 0,
                                                'message' => $message,
                                                'redirecturl' => $redirect
                                            ));
                                        }
                                    }
                                    else
                                    {
                                        return response()->json(array(
                                            'status' => 0,
                                            'message' => 'Wait for the event start.',
                                            'redirecturl' => $redirect
                                        ));
                                    }
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => 'Today event is closed.',
                                        'redirecturl' => $redirect
                                    ));
                                }
                                // }else{
                                //  return response()->json(array('status' => 0,'message'=>'You Already login another event.','redirecturl' => $redirect));
                                // }
                                
                            }
                            else
                            {
                                return response()->json(array(
                                    'status' => 0,
                                    'message' => 'Member Already Login',
                                    'redirecturl' => $redirect
                                ));
                            }
                        }
                        else
                        {
                            return response()->json(array(
                                'status' => 0,
                                'message' => 'Wait for the event start.',
                                'redirecturl' => $redirect
                            ));
                        }
                    }
                    //Logout
                    if ($request->type == 2)
                    {
                        $loginDetail = Attendance::where('event_id', $request->event_id)
                            ->where('user_id', $userid)->first();
                        if (!empty($loginDetail))
                        {
                            $attendance = Attendance::find($loginDetail->id);
                            if ($attendance->out_time != '-')
                            {
                                return response()
                                    ->json(array(
                                    'status' => 0,
                                    'message' => 'Member is Already Logout.',
                                    'redirecturl' => $redirect
                                ));
                            }
                            else
                            {
                                $inTime = $attendance->in_time;
                                $outTime = date('H:i');
                                if ($outTime > $eventendtime)
                                {
                                    $diff = abs(strtotime($outTime) - strtotime($inTime));
                                    $attendance->out_time = $outTime;
                                }
                                else
                                {
                                    $diff = abs(strtotime($eventendtime) - strtotime($inTime));
                                    $attendance->out_time = $eventendtime;
                                }
                                $tmins = $diff / 60;
                                $hours = floor($tmins / 60);
                                $mins = $tmins % 60;
                                //$diff_hours =  $hours.':'.$mins;
                                $diff_hours = $hours . ':' . $mins;
                                $attendance->hours = $diff_hours;
                                $remaining_hour = 0;
                                $remaining_hour = $this->attendanceremainingHour($userid, $diff_hours, $hour_point);
                                if ($eventData->event_type == '1')
                                {
                                    $attendance->training_hour = $diff_hours;
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else if ($eventData->event_type == '2')
                                {
                                    $attendance->activity_hour = $diff_hours;
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else
                                {
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                $result = $attendance->save();
                                if ($result)
                                {
                                    $Setting = Settings::first();
                                    // Add Member tokens 'Per Hours 1 Token Incresed'
                                    $MemberToken = new MemberToken;
                                    $MemberToken->user_id = !empty($userid) ? $userid : NULL;
                                    $MemberToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
                                    $MemberToken->token = ($hours);
                                    $MemberToken->remaining_token = ($hours);
                                    $MemberToken->expired_at = date('Y-m-d h:i:s', strtotime('+'.$Setting->token_expire_day.' days'));
                                    $saveMemberToken = $MemberToken->save();

                                    $MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
                                    if (!empty($MemberTokenStatus))
                                    {
                                        $MemberTokenStatus->total_token = ($MemberTokenStatus->total_token + $hours);
                                        $MemberTokenStatus->save();
                                    }
                                    else
                                    {
                                        $MemberTokenStatus = new MemberTokenStatus;
                                        $MemberTokenStatus->user_id = !empty($userid) ? $userid : NULL;
                                        $MemberTokenStatus->total_token = ($hours);
                                        $saveMemberToken = $MemberTokenStatus->save();
                                    }

                                    return response()
                                        ->json(array(
                                        'status' => 1,
                                        'message' => 'Member Logout successfully',
                                        'redirecturl' => $redirect
                                    ));
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => 'Something is wrong',
                                        'redirecturl' => $redirect
                                    ));
                                }
                            }
                        }
                        else
                        {
                            return response()->json(array(
                                'status' => 0,
                                'message' => 'Member is not login',
                                'redirecturl' => $redirect
                            ));
                        }
                    }
                }
                else
                {
                    return response()->json(array(
                        'status' => 0,
                        'message' => 'Event not found',
                        'redirecturl' => $redirect
                    ));
                }
            }
            else
            {
                return response()->json(array(
                    'status' => 0,
                    'message' => 'You have not enough hour.',
                    'redirecturl' => $redirect
                ));
            }
        }
        else
        {
            return response()->json(array(
                'status' => 0,
                'message' => 'Invalid User',
                'redirecturl' => $redirect
            ));
        }
    }

    public function recordMemberCodeAttendance(Request $request)
    {
        $MemberCode = $request->MemberCode;
        $explodeMember = explode("C", $MemberCode);
        if (!empty($explodeMember[1]))
        {
            $userData = User::where('Role_ID', '2')->where('MemberCode', $explodeMember[1])->first();
        }
        if (!empty($userData))
        {
            $userid = $userData->ID;
            $hour_point = $userData->hour_point;
        }

        $redirect = '/attendanceManagement';
        if (!empty($userData))
        {
            if (!empty($hour_point))
            {
                $eventData = Events::where('id', $request->event_id)
                    ->first();
                $event_sche = EventSchedule::where('event_id', $request->event_id)
                    ->get()
                    ->toArray();
                $date_arr = array();
                if (!empty($event_sche))
                {
                    foreach ($event_sche as $row)
                    {
                        $date_arr[] = $row['date'];
                    }
                }
                if (!empty($eventData))
                {
                    //Login
                    $eventStarttime = $eventData->start_time;
                    $diff_event = abs(strtotime($eventStarttime) - strtotime(date('H:i')));
                    $tmins_event = $diff_event / 60;
                    $hours_event = floor($tmins_event / 60);
                    $mins_event = $tmins_event % 60;
                    $eventendtime = $eventData->end_time;
                    if ($request->type == 1)
                    {
                        if (in_array(date('m/d/Y') , $date_arr))
                        {
                            $Alreadylogin = Attendance::where('event_id', $request->event_id)
                                ->where('user_id', $userid)->get()
                                ->toArray();
                            if (empty($Alreadylogin))
                            {
                                // $eventlogin = Attendance::where('user_id',$userid)->orderBy('id','desc')->first();
                                // if(!empty($eventlogin) && $eventlogin->out_time != '-'){
                                $inTime = date('H:i');
                                if ($eventendtime > $inTime)
                                {
                                    if ($hours_event < 1)
                                    {
                                //check event is paid or not
                                if ($eventData['event_money'] == '0')
                                {
                                    //free
                                    $insertData = true;
                                }
                                else
                                {
                                    //paid
                                    if ($request->usedCoin == '0')
                                    {

                                        //not using coin
                                        $message = 'Please use coin for paid event.';
                                        $insertData = false;
                                    }
                                    else
                                    {
                                        $MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
                                        if (!empty($MemberTokenStatus))
                                        {
                                            //using coin
                                            if ($eventData['event_token'] <= $MemberTokenStatus->total_token)
                                            {

                                                $UserToken = User::where('Role_ID', '2')->where('MemberCode', $explodeMember[1])->first();
                                                if (!empty($UserToken))
                                                {

                                                    //start logic for token used or not for expired token
                                                    $memberToken = MemberToken::where('user_id', $userid)->where('remaining_token', '!=', 0)
                                                        ->where('status', 0)
                                                        ->where('expired', 0)
                                                        ->get()
                                                        ->toArray();
                                                    if (!empty($memberToken))
                                                    {
                                                        $remainingHour = $eventData['event_token'];
                                                        foreach ($memberToken as $memberToken_key => $memberToken_value)
                                                        {
                                                            if ($remainingHour == '0')
                                                            {
                                                                break;
                                                            }
                                                            else
                                                            {
                                                                if ($remainingHour < $memberToken_value['remaining_token'])
                                                                {
                                                                    $token = MemberToken::where('id', $memberToken_value['id'])->first();
                                                                    // echo $remainingHour;
                                                                    // $remainingHour =  $token->remaining_token - $remainingHour;
                                                                    $token->remaining_token = $token->remaining_token - $remainingHour;
                                                                    $token->status = ($token->remaining_token - $remainingHour == '0') ? '1' : '0';
                                                                    $saveToken = $token->save();
                                                                    break;
                                                                }
                                                                else
                                                                {
                                                                    $token = MemberToken::where('id', $memberToken_value['id'])->first();
                                                                    $remainingHour = $remainingHour - $token->remaining_token;
                                                                    $token->remaining_token = 0;
                                                                    $token->status = 1;
                                                                    $saveToken = $token->save();
                                                                }
                                                            }

                                                        }
                                                    }
                                                    else
                                                    {

                                                    }
                                                    //end logic for token used or not for expired token
                                                    $MemberTokenStatus->total_token = ($MemberTokenStatus->total_token - $eventData['event_token']);
                                                    $MemberTokenStatus->save();

                                                    //Save member used token detail
                                                    $MemberUsedToken = new MemberUsedToken;
                                                    $MemberUsedToken->user_id = !empty($userid) ? $userid : NULL;
                                                    $MemberUsedToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
                                                    $MemberUsedToken->token = !empty($eventData['event_token']) ? $eventData['event_token'] : NULL;
                                                    $saveMemberUsedToken = $MemberUsedToken->save();

                                                    $insertData = true;
                                                }
                                            }
                                            else
                                            {
                                                $message = 'User have not enough coin to attend this event.';
                                                $insertData = false;
                                            }
                                        }
                                        else
                                        {
                                            $message = 'User have no coin to attend this event.';
                                            $insertData = false;
                                        }
                                    }
                                }
                                if ($insertData)
                                {
                                    $diff = abs(strtotime($inTime) - strtotime($eventStarttime));
                                    $tmins = $diff / 60;
                                    $hours = floor($tmins / 60);
                                    $mins = $tmins % 60;
                                    $userattendfirst = Attendance::where('user_id', $userid)->first();
                                    $attendance = new Attendance;
                                    $attendance->user_id = $userid;
                                    $attendance->member_code = $userData->MemberCode;
                                    $attendance->event_id = $request->event_id;
                                    $attendance->event_type = $eventData->event_type;
                                    if ($hours == 0 && $mins < $this->globalmin)
                                    {
                                        $attendance->in_time = $eventStarttime;
                                    }
                                    else
                                    {
                                        $attendance->in_time = $inTime;
                                    }
                                    $attendance->date = $eventData->startdate;
                                    if (empty($userattendfirst))
                                    {
                                        $attendance->remaining_hour = $hour_point . ':00';
                                    }
                                    $result = $attendance->save();
                                    if ($result)
                                    {
                                        return response()->json(array(
                                            'status' => 1,
                                            'message' => 'Attendance Added successfully',
                                            'redirecturl' => $redirect
                                        ));
                                    }
                                    else
                                    {
                                        return response()->json(array(
                                            'status' => 0,
                                            'message' => 'Something is wrong',
                                            'redirecturl' => $redirect
                                        ));
                                    }
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => $message,
                                        'redirecturl' => $redirect
                                    ));
                                }
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => 'Wait for the event start.',
                                        'redirecturl' => $redirect
                                    ));
                                }
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => 'Today event is closed.',
                                        'redirecturl' => $redirect
                                    ));
                                }
                                // }else{
                                //  return response()->json(array('status' => 0,'message'=>'You Already login another event.','redirecturl' => $redirect));
                                // }
                                
                            }
                            else
                            {
                                return response()->json(array(
                                    'status' => 0,
                                    'message' => 'Member Already Login',
                                    'redirecturl' => $redirect
                                ));
                            }
                        }
                        else
                        {
                            return response()->json(array(
                                'status' => 0,
                                'message' => 'Wait for the event start.',
                                'redirecturl' => $redirect
                            ));
                        }
                    }
                    //Logout
                    if ($request->type == 2)
                    {
                        $loginDetail = Attendance::where('event_id', $request->event_id)
                            ->where('user_id', $userid)->first();
                        if (!empty($loginDetail))
                        {
                            $attendance = Attendance::find($loginDetail->id);
                            if ($attendance->out_time != '-')
                            {
                                return response()
                                    ->json(array(
                                    'status' => 0,
                                    'message' => 'Member is Already Logout.',
                                    'redirecturl' => $redirect
                                ));
                            }
                            else
                            {
                                $inTime = $attendance->in_time;
                                $outTime = date('H:i');
                                if ($outTime > $eventendtime)
                                {
                                    $diff = abs(strtotime($outTime) - strtotime($inTime));
                                    $attendance->out_time = $outTime;
                                }
                                else
                                {
                                    $diff = abs(strtotime($eventendtime) - strtotime($inTime));
                                    $attendance->out_time = $eventendtime;
                                }
                                $tmins = $diff / 60;
                                $hours = floor($tmins / 60);
                                $mins = $tmins % 60;
                                $diff_hours = $hours . ':' . $mins;
                                $attendance->hours = $diff_hours;
                                $remaining_hour = 0;
                                $remaining_hour = $this->attendanceremainingHour($userid, $diff_hours, $hour_point);
                                if ($eventData->event_type == '1')
                                {
                                    $attendance->training_hour = $diff_hours;
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else if ($eventData->event_type == '2')
                                {
                                    $attendance->activity_hour = $diff_hours;
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else
                                {
                                    $attendance->service_hour = $diff_hours;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                $result = $attendance->save();
                                if ($result)
                                {
                                    $Setting = Settings::first();
                                    // Add Member tokens 'Per Hours 1 Token Incresed'
                                    $MemberToken = new MemberToken;
                                    $MemberToken->user_id = !empty($userid) ? $userid : NULL;
                                    $MemberToken->event_id = !empty($request->event_id) ? $request->event_id : NULL;
                                    $MemberToken->token = ($hours);
                                    $MemberToken->remaining_token = ($hours);
                                    $MemberToken->expired_at = date('Y-m-d h:i:s', strtotime('+'.$Setting->token_expire_day.' days'));
                                    $saveMemberToken = $MemberToken->save();

                                    $MemberTokenStatus = MemberTokenStatus::where('user_id', $userid)->first();
                                    if (!empty($MemberTokenStatus))
                                    {
                                        $MemberTokenStatus->total_token = ($MemberTokenStatus->total_token + $hours);
                                        $MemberTokenStatus->save();
                                    }
                                    else
                                    {
                                        $MemberTokenStatus = new MemberTokenStatus;
                                        $MemberTokenStatus->user_id = !empty($userid) ? $userid : NULL;
                                        $MemberTokenStatus->total_token = ($hours);
                                        $saveMemberToken = $MemberTokenStatus->save();
                                    }

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
                                        'redirecturl' => $redirect
                                    ));
                                }
                                else
                                {
                                    return response()->json(array(
                                        'status' => 0,
                                        'message' => 'Something is wrong',
                                        'redirecturl' => $redirect
                                    ));
                                }
                            }
                        }
                        else
                        {
                            return response()->json(array(
                                'status' => 0,
                                'message' => 'Member is not login',
                                'redirecturl' => $redirect
                            ));
                        }
                    }
                }
                else
                {
                    return response()->json(array(
                        'status' => 0,
                        'message' => 'Event not found',
                        'redirecturl' => $redirect
                    ));
                }
            }
            else
            {
                return response()->json(array(
                    'status' => 0,
                    'message' => 'You have not enough hour.',
                    'redirecturl' => $redirect
                ));
            }
        }
        else
        {
            return response()->json(array(
                'status' => 0,
                'message' => 'Invalid User',
                'redirecturl' => $redirect
            ));
        }
    }

    public function attendanceremainingHour($userid, $diff_hours, $hour_point)
    {

        $AttendanceDetail = Attendance::where('user_id', $userid)->get()
            ->toArray();
        $count_user = count($AttendanceDetail);
        $last_remaining_data = Attendance::selectRaw('id,user_id,remaining_hour')->where('user_id', $userid)->orderBy('id', 'desc')
            ->first();
        if (!empty($last_remaining_data))
        {
            $last_remaining_hour = $last_remaining_data->remaining_hour;
        }
        $remaining_hour = 0;
        if (!empty($AttendanceDetail))
        {
            foreach ($AttendanceDetail as $val)
            {
                if ($count_user == '1')
                {
                    $diff = strtotime($val['remaining_hour']) - strtotime($diff_hours);
                    $tmins = $diff / 60;
                    $hours = floor($tmins / 60);
                    $mins = $tmins % 60;
                    $remaining_hour = $hours . ':' . $mins;
                }
                else
                {
                    if ($val['out_time'] != '-')
                    {
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
    public function getEventAttenderList($event_id)
    {
        if (Session::get('user') ['user_id'] != '1')
        {
            $attendances = Attendance::where('user_id', Session::get('user') ['user_id'])->where('event_id', $event_id)->where('date', date('l,d F,Y'))
                ->with('users')
                ->with('event')
                ->with('eventType')
                ->get()
                ->toArray();
        }
        else
        {
            $attendances = Attendance::where('event_id', $event_id)->where('date', date('l,d F,Y'))
                ->with('users')
                ->with('event')
                ->with('eventType')
                ->get()
                ->toArray();
        }
        if (!empty($attendances))
        {
            $html = '';
            $EventType = 'event_type_name_' . app()->getLocale();
            foreach ($attendances as $key => $val)
            {
                if ($val['users']['UserName'])
                {
                    $name = $val['users']['UserName'];
                }
                else
                {
                    $name = $val['users']['Chinese_name'] . '&' . $val['users']['English_name'];
                }
                if ($val['out_time'] == '-' && $val['hours'] == '-')
                {
                    $out_time = '-';
                    $hours = '-';
                }
                else
                {
                    $out_time = date('h:i a', strtotime($val['out_time']));
                    $hours = $val['hours'];
                }
                $html .= '<tr>';
                if (in_array('members_write', Helper::module_permission(Session::get('user') ['role_id'])))
                {
                    $html .= '<td><a href="users/' . $val['users']['ID'] . '/edit">' . $val['users']['MemberCode'] . '</a></td>';
                }
                else
                {
                    $html .= '<td>' . $val['users']['MemberCode'] . '</td>';
                }
                $html .= '<td>' . $name . '</td><td>' . $val['event']['event_name'] . '</td>
<td>' . $val['event_type'][$EventType] . '</td><td>' . date('d/m/Y', strtotime($val['date'])) . '</td>
<td>' . date('h:i a', strtotime($val['in_time'])) . '</td><td>' . $out_time . '</td>
<td>' . $hours . '</td></tr>';
            }
            return response()->json(array(
                'status' => 1,
                'list' => $html
            ));
        }
        else
        {
            return response()->json(array(
                'status' => 0
            ));
        }
    }

    /**
     ** USE : GENERATE QR CODE
     *
     */
    public function generateQRCode($id)
    {
        $userData = User::where('Role_ID', '2')->where('ID', $id)->first();
        $Email = $userData->email;
        if (!empty($userData->UserName))
        {
            $UserName = $userData->UserName;
        }
        else
        {
            $Chinese_name = $userData->Chinese_name;
            $English_name = $userData->English_name;
            $UserName = $Chinese_name . ' ' . $English_name;
        }
        $user_id = base64_encode($id);
        $email_add = base64_encode($Email);
        $userdata = $user_id . "/" . $email_add . "/" . trim($UserName);
        $public_path = public_path() . '/image';
        $qrCode = new QrCode($userdata);
        $qrCode->setSize(200);
        $qrimag = trim($UserName) . "-" . time() . ".png";
        $qrcodeimag = $qrCode->writeFile($public_path . '/' . $qrimag);
        $dataUri = $qrCode->writeDataUri();
        $dataarr = array(
            'name' => $UserName,
            'email' => $userData->email,
            'subject' => "Qrcode",
            'qrcode' => $dataUri
        );
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
        $EmailData['subject'] = 'Qr Code';
        $EmailData['email'] = $userData->email;
        $EmailData['data'] = $dataarr;
        $EmailData['emailpage'] = 'email.sendCredential';
        $EmailData['qrImage'] = $public_path . '/' . $qrimag;

        // Email sent using Queue Job
        dispatch(new SendQRMailJob($EmailData));

        $qrImage = User::find($id);
        $qrImage->QrCode = $qrimag;
        $qrImage->save();
        if ($qrImage)
        {
            $redirecturl = env('ASSET_URL') . '/image/' . $qrimag;
            return response()->json(array(
                'status' => 1,
                'url' => $redirecturl
            ));
        }
        else
        {
            return response()->json(array(
                'status' => 0
            ));
        }
    }

    public function attendanceReport()
    {
        if (!empty(Session::get('user') ['user_id']) && Session::get('user') ['user_id'] == 1)
        {
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
        }
        else
        {
            $attendancesreport = Attendance::where('user_id', Session::get('user') ['user_id'])->with('users')
                ->with('event')
                ->with('eventType')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
            $users = User::where('ID', Session::get('user') ['user_id'])->where('Status', '1')
                ->where('Role_ID', '!=', '1')
                ->get()
                ->toArray();
            $events = Events::whereRaw('FIND_IN_SET(' . Session::get('user') ['user_id'] . ',event_assign_user)')->where('status', '1')
                ->groupBy('occurs')
                ->get()
                ->toArray();
        }
        $eventTypes = new EventType;
        $get_event_type_list = $eventTypes->get_event_type_select_list();
        return view('AttendanceManagement.attendance_report', compact('attendancesreport', 'users', 'events', 'get_event_type_list'));
    }

    public function attendancesearchReport(Request $request)
    {
        $member_name = !empty($request->member_name) ? $request->member_name : '';
        $filter_date = !empty($request->filter_date) ? $request->filter_date : '';
        $event_name = !empty($request->event_name) ? $request->event_name : '';
        $event_type = !empty($request->event_type) ? $request->event_type : '';
        $query = Attendance::with('users')->with('event')
            ->with('eventType');
        $EventType = 'event_type_name_' . app()->getLocale();
        if (isset($member_name) && !empty($member_name))
        {
            $query->where('user_id', $member_name);
        }
         if (isset($filter_date) && !empty($filter_date))
        {
            $expolde_event_date = explode('-', $filter_date);
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
            
            $start_date = date('Y-m-d',strtotime($expolde_event_date[0]));
            $end_date = date('Y-m-d',strtotime($expolde_event_date[1]));
            $query->whereBetween('created_at',[$start_date.' 00:00:00', $end_date.' 23:59:59']);

            // $start_date = date('Y-m-d',strtotime($expolde_event_date[0]));
            // $end_date = date('Y-m-d',strtotime($expolde_event_date[1]));
            // $new_date = date('Y-m-d', strtotime('Wednesday,10 February,2021'));
            // $paydate_raw = DB::raw("STR_TO_DATE(`date`, '%Y-%m-%d')");
            // $query->whereBetween($paydate_raw, [$start_date, $new_date]);

           
        }
        if (isset($event_name) && !empty($event_name))
        {
            $query->where('event_id', $event_name);
        }
        if (isset($event_type) && !empty($event_type))
        {
            $query->where('event_type', $event_type);
        }
        if (!empty(Session::get('user') ['role_id']) && Session::get('user') ['role_id'] != '1')
        {
            $event_serach_data = $query->where('user_id', Session::get('user') ['user_id'])
                ->get()
                ->toArray();
        }
        else
        {
            $event_serach_data = $query->get()->toArray();
        }
       
        $html = '';
        $html .= '<table id="attendanceSerachReporttable" class="table event-reportserach-cls">
<thead>
<tr>
<th>' . __('languages.event.Event Name') . '</th>
<th>' . __('languages.event.Event Type') . '</th>
<th>' . __('languages.Attendance.Event Date') . '</th>
<th>' . __('languages.Attendance.Member_Name') . '</th>
<th>' . __('languages.Attendance.Used Hour') . '</th>
<th>' . __('languages.Attendance.Remaining Hours') . '</th>
<th>' . __('languages.Attendance.Total Hour') . '</th>
<th>' . __('languages.Action') . '</th>
</tr>
</thead>
<tbody>';
        if (!empty($event_serach_data))
        {
            foreach ($event_serach_data as $val)
            {
                if (!empty($val['users']))
                {
                    if (!empty($val['event']['event_name']))
                    {
                        $html .= '<tr>
<td>' . $val['event']['event_name'] . '</td>    
<td>' . $val['event_type'][$EventType] . '</td>
<td>' . date('d/m/Y', strtotime($val['event']['startdate'])) . '</td>';
                        if ($val['users']['UserName'])
                        {
                            $html .= '<td>' . $val['users']['UserName'] . '</td>';
                        }
                        else
                        {
                            $html .= '<td>' . $val['users']['Chinese_name'] . ' & ' . $val['users']['English_name'] . '</td>';
                        }
                        $html .= '<td>' . $val['hours'] . '</td>
<td>' . $val['remaining_hour'] . '</td>
<td>' . $val['users']['hour_point'] . '</td>
<td>
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

    public function attendancereportdetail($id)
    {
        $attendancesreportdetalis = Attendance::with('users')->with('event')
            ->with('eventType')
            ->find($id)->toArray();
        return view('AttendanceManagement.attendance_report_detail', compact('attendancesreportdetalis'));
    }

    public function attendanceEventListSearch(Request $request)
    {
        $filter_date_attendance_event = !empty($request->filter_date_attendance_event) ? $request->filter_date_attendance_event : '';
        $html = '';
        if (!empty($filter_date_attendance_event))
        {
            $expolde_event_date = explode('-', $filter_date_attendance_event);
            $start_event_date = date('m/d/Y', strtotime($expolde_event_date[0]));
            $end_event_date = date('m/d/Y', strtotime($expolde_event_date[1]));
            $search_result = DB::select(DB::raw("SELECT * FROM `event_schedule` WHERE STR_TO_DATE(date, '%m/%d/%Y') BETWEEN STR_TO_DATE('" . $start_event_date . "', '%m/%d/%Y') AND STR_TO_DATE('" . $end_event_date . "', '%m/%d/%Y') AND status = 1 GROUP BY event_code"));
            if (!empty($search_result))
            {
                $array = json_decode(json_encode($search_result) , true);
                $ids = array_column($array, 'id');
            }
            else
            {
                $html .= '<label for="users-list-role">' . __('languages.Attendance.Select_Event') . '</label>
<fieldset class="form-group">
<select class="form-control" id="event_id" name="event_id">
<option value="">' . __('languages.Attendance.Select_Event_Name') . '</option>';
                $html .= '</select></fieldset>';
                return $html;
                exit;
            }
        }

        $Select_db = DB::table('event_schedule')->select('event_schedule.*', 'events.*', 'event_type.*')
            ->join('events', 'events.id', 'event_schedule.event_id')
            ->join('event_type', 'event_type.id', 'events.event_type')
            ->where('event_schedule.status', 1);
        if (!empty($request->filter_date_attendance_event))
        {
            $Select_db->whereIn('event_schedule.id', $ids);
        }
        if (Session::get('user') ['user_id'] != '1')
        {
            $Select_db->whereRaw('FIND_IN_SET(' . Session::get('user') ['user_id'] . ',events.event_assign_user)');
            $result = $Select_db->groupBy('event_schedule.event_code')
                ->get()
                ->toArray();
        }
        else
        {
            $result = $Select_db->groupBy('event_schedule.event_code')
                ->get()
                ->toArray();
        }
        $html .= '<label for="users-list-role">' . __('languages.Attendance.Select_Event') . '</label>
<fieldset class="form-group">
<select class="form-control" id="event_id" name="event_id">
<option value="">' . __('languages.Attendance.Select_Event_Name') . '</option>';
        if (!empty($result))
        {
            $EventType = 'event_type_name_' . app()->getLocale();
            foreach ($result as $val)
            {
                $html .= '<option value="' . $val->event_id . '" data-event-type="' . $val->$EventType . '">' . $val->event_name . ' - ' . $val->occurs . '</option>';
            }
        }
        $html .= '</select></fieldset>';
        echo $html;
    }

    /**
     ** USE : EXPIRED STATUS TOKEN
     *
     */
    public function expiredToken()
    {
        $currentdate = date('Y-m-d');
        $allRecord = MemberToken::where('status', 0)->where('expired', 0)
            ->where('expired_at', '<', $currentdate . '23:59:59')->get()
            ->toArray();
        if (!empty($allRecord))
        {
            foreach ($allRecord as $key => $value)
            {
                $MemberTokenStatus = MemberTokenStatus::where('user_id', $value['user_id'])->first();
                $MemberTokenStatus->total_token = ($MemberTokenStatus->total_token - $value['remaining_token']);
                $saveMemberTokenStatus = $MemberTokenStatus->save();

                $token = MemberToken::where('id', $value['id'])->first();
                $token->expired = '1';
                $saveToken = $token->save();
            }
            echo 'success';
        }
        else
        {
            echo 'No record found.';
        }
    }

    /**
    **  USE : Token Report
    **/
    public function transactionHistory(){

       if (Session::get('user') ['role_id'] == '1')
        {
            $transactionHistory = MemberUsedToken::with('users')->with('event')->get()->toArray();
            return view('AttendanceManagement.transaction_history', compact('transactionHistory'));
        }
    }

}

