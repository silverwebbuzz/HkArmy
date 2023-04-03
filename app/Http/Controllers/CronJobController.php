<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Models\EventTokenManage;
use App\Http\Models\Settings;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use Config;
use Mail;
use Log;

class CronJobController extends Controller {

    public function __construct() {
		date_default_timezone_set(Config::get('constants.timeZone'));
		$sitesettings = Helper::getsitesettings();
		if (!empty($sitesettings->min_hour)) {
			$this->globalmin = $sitesettings->min_hour;
		} else {
			$this->globalmin = 30;
		}
	}

    /**
     * USE : Users events token expire every day using cron job
     */
    public function ExpiredEventTokenCronJob(){
        Log::info('Expired Event Token Cron-Job Start : '.date('Y-m-d h:i:s'));
        $currentDate = date('Y-m-d');
        if(EventTokenManage::where('expire_date', '<=', date('Y-m-d'))->exists()){
            $Update = EventTokenManage::where('expire_date', '<=', date('Y-m-d'))->update(['status' => 'expired']);
            if($Update){
                // Send email after successfull cronjob run
                $sendMail = Mail::send(['html' => 'email.expired_token_cron_job_notification'], [], function ($message){
                    $message->to(Config::get('mail.cron_job_send_email_address'),'Admin' ?? '');
                    $message->subject('Events Token Expired Cron-Job Run Successfully');
                });
                Log::info('Expired Event Token Cron-Job Run Successfully: '.date('Y-m-d h:i:s'));
                echo 'Expired Event Token Cron-Job Run Successfully';exit;
            }else{
                Log::info('Expired Event Token Cron-Job Failed : '.date('Y-m-d h:i:s'));
                echo 'Expired Event Token Cron-Job Failed';exit;
            }
        }else{
            Log::info('Expired Event Token Cron-Job Run Successfully: '.date('Y-m-d h:i:s'));
            echo 'Expired Event Token Cron-Job Run Successfully';exit;
        }
    }
}