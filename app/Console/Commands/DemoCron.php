<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\Http\Models\Attendance;
use App\Http\Models\User;
use App\Http\Models\Events;
use App\Http\Models\EventType;
use App\Http\Models\EventSchedule;
use App\Http\Models\HourAttendance;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Cron Job Started');


        $currentTime = date('H:i');
        $todayDate = date('m/d/Y');
        $events = EventSchedule::with('events')->where('date', $todayDate)
        ->where('status', '1')
        ->get()
        ->toArray();

        if(!empty($events)){
            foreach($events as $key => $event){
                $event_time = strtotime($event['end_time']);
                $diff = $event_time - strtotime($currentTime);
                if($diff < 0){
                    $allAttendance = Attendance::where('event_id',$event['event_id'])->where('out_time','=','-')->where('hours','=','-')->get();
                    
                    if(!$allAttendance->isEmpty()){

                        foreach ($allAttendance as $key => $attendanceVal) {

                            $attendance = Attendance::find($attendanceVal->id);

                            $userData = User::where('Role_ID', '2')->where('ID', $attendance->user_id)->first();
                            if (!empty($userData))
                            {
                                $hour_point = $userData->hour_point;
                            }
                            if(!empty($hour_point)){
                                $inTime = $attendance->in_time;
                                $outTime = $event['end_time'];

                                $attendance->out_time = $outTime;

                                $diff = abs(strtotime($outTime) - strtotime($inTime));

                                $mins = $diff / 60;
                                $diff_hours = intdiv($mins, 60) . ':' . ($mins % 60);


                                $remaining_hour = 0;
                                $remaining_hour = $this->attendanceremainingHour($attendance->user_id, $diff_hours, $hour_point);
                                $deduct_hour = 0;
                                if ($attendance->late_min != NULL && $this->globalmin < $attendance->late_min)
                                {
                                    $deduct_hour++;
                                }

                                $deduct_hour = $deduct_hour . ':00';
                                
                                $diff_deduct_minit = abs(strtotime($diff_hours) - strtotime($deduct_hour)) / 60;
                                $diff_deduct_hour = intdiv($diff_deduct_minit, 60) . ':' . ($diff_deduct_minit % 60);
                               
                                $attendance->hours = $diff_deduct_hour;
                                
                                if ($event['events']['event_type'] == '1')
                                {
                                    $attendance->training_hour = $diff_deduct_hour;
                                    $attendance->service_hour = $diff_deduct_hour;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else if ($event['events']['event_type'] == '2')
                                {
                                    $attendance->activity_hour = $diff_deduct_hour;
                                    $attendance->service_hour = $diff_deduct_hour;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                else
                                {
                                    $attendance->service_hour = $diff_deduct_hour;
                                    $attendance->remaining_hour = $remaining_hour;
                                }
                                $result = $attendance->save();
                            }
                        }
                    }
                }
            }
            return 'Success';
        }else{
            return 'No event found';
        }

        \Log::info('Cron Job Ended');
    }
}
